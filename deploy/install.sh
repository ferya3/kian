#!/usr/bin/env bash
#
# نصب سایت سفال کیان روی سرور اوبونتو (۲۲.۰۴ / ۲۴.۰۴)
#
#   curl -fsSL https://raw.githubusercontent.com/ferya3/kian/claude/ceramic-factory-website-p5je97/deploy/install.sh | sudo bash
#
# متغیرهای قابل تنظیم (همه اختیاری):
#   DOMAIN=kian-ceramic.ir   دامنه‌ی سایت؛ پیش‌فرض: هر هاستی
#   APP_DIR=/var/www/kian    مسیر نصب
#   BRANCH=main              برنچ گیت
#   DB=sqlite|mysql          موتور دیتابیس؛ پیش‌فرض sqlite
#   DB_PASSWORD=...          رمز کاربر mysql (اگر DB=mysql و خالی باشد، ساخته می‌شود)
#   SSL=1                    گرفتن گواهی Let's Encrypt (نیازمند DOMAIN و DNS آماده)
#   SKIP_SYSTEM=1            پرش از نصب بسته‌های سیستمی (برای اجرای مجدد/به‌روزرسانی)
#
set -euo pipefail

REPO="${REPO:-https://github.com/ferya3/kian.git}"
BRANCH="${BRANCH:-claude/ceramic-factory-website-p5je97}"
APP_DIR="${APP_DIR:-/var/www/kian}"
DOMAIN="${DOMAIN:-}"
DB="${DB:-sqlite}"
DB_NAME="${DB_NAME:-kian}"
DB_USER="${DB_USER:-kian}"
DB_PASSWORD="${DB_PASSWORD:-}"
SSL="${SSL:-0}"
SKIP_SYSTEM="${SKIP_SYSTEM:-0}"
PHP_VERSION="${PHP_VERSION:-8.4}"
NODE_MAJOR="${NODE_MAJOR:-22}"

step() { printf '\n\033[1;33m==>\033[0m \033[1m%s\033[0m\n' "$1"; }
die()  { printf '\n\033[1;31mخطا:\033[0m %s\n' "$1" >&2; exit 1; }

[ "$(id -u)" -eq 0 ] || die "این اسکریپت را با sudo اجرا کنید."
command -v apt-get >/dev/null || die "این اسکریپت فقط برای اوبونتو/دبیان است."

# ---------------------------------------------------------------- بسته‌ها ----
if [ "$SKIP_SYSTEM" != "1" ]; then
    step "نصب بسته‌های سیستمی"

    export DEBIAN_FRONTEND=noninteractive
    apt-get update -qq
    apt-get install -y -qq ca-certificates curl git unzip gnupg software-properties-common >/dev/null

    # PHP 8.4 از مخزن ondrej
    if ! command -v "php$PHP_VERSION" >/dev/null; then
        add-apt-repository -y ppa:ondrej/php >/dev/null 2>&1
        apt-get update -qq
    fi

    apt-get install -y -qq \
        "php$PHP_VERSION-fpm" "php$PHP_VERSION-cli" "php$PHP_VERSION-mbstring" \
        "php$PHP_VERSION-xml" "php$PHP_VERSION-curl" "php$PHP_VERSION-zip" \
        "php$PHP_VERSION-intl" "php$PHP_VERSION-gd" "php$PHP_VERSION-bcmath" \
        "php$PHP_VERSION-sqlite3" "php$PHP_VERSION-mysql" \
        nginx >/dev/null
    # php-intl برای تاریخ شمسی و php-gd برای پردازش تصویر لازم است.

    # Node
    if ! command -v node >/dev/null || [ "$(node -v | cut -c2- | cut -d. -f1)" -lt "$NODE_MAJOR" ]; then
        curl -fsSL "https://deb.nodesource.com/setup_${NODE_MAJOR}.x" | bash - >/dev/null 2>&1
        apt-get install -y -qq nodejs >/dev/null
    fi

    # Composer
    if ! command -v composer >/dev/null; then
        curl -fsSL https://getcomposer.org/installer -o /tmp/composer-setup.php
        php /tmp/composer-setup.php --quiet --install-dir=/usr/local/bin --filename=composer
        rm -f /tmp/composer-setup.php
    fi

    if [ "$DB" = "mysql" ]; then
        apt-get install -y -qq mysql-server >/dev/null
        systemctl enable --now mysql
    fi
fi

command -v composer >/dev/null || die "composer نصب نشد."
command -v npm >/dev/null || die "npm نصب نشد."

# ------------------------------------------------------------------ سورس ----
step "دریافت سورس در $APP_DIR"

# بعد از chown به www-data، اجرای مجدد اسکریپت با root به گارد
# «dubious ownership» گیت می‌خورد؛ این خط اجازه‌ی به‌روزرسانی را می‌دهد.
git config --global --add safe.directory "$APP_DIR" 2>/dev/null || true

if [ -d "$APP_DIR/.git" ]; then
    git -C "$APP_DIR" fetch --depth 1 origin "$BRANCH"
    git -C "$APP_DIR" reset --hard "origin/$BRANCH"
else
    mkdir -p "$(dirname "$APP_DIR")"
    git clone --depth 1 --branch "$BRANCH" "$REPO" "$APP_DIR"
fi

cd "$APP_DIR"

# ---------------------------------------------------------------- وابستگی ---
step "نصب وابستگی‌ها و ساخت assets"

export COMPOSER_ALLOW_SUPERUSER=1
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
npm ci --no-audit --no-fund
npm run build

# -------------------------------------------------------------------- env ---
step "پیکربندی محیط"

[ -f .env ] || cp .env.example .env
grep -q '^APP_KEY=base64' .env || php artisan key:generate --force

if [ -n "$DOMAIN" ]; then
    [ "$SSL" = "1" ] && APP_URL="https://$DOMAIN" || APP_URL="http://$DOMAIN"
else
    APP_URL="http://$(hostname -I | awk '{print $1}')"
fi

sed -i "s|^APP_ENV=.*|APP_ENV=production|" .env
sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|" .env
sed -i "s|^APP_URL=.*|APP_URL=$APP_URL|" .env

if [ "$DB" = "mysql" ]; then
    if [ -z "$DB_PASSWORD" ]; then
        DB_PASSWORD="$(openssl rand -base64 24 | tr -d '/+=' | head -c 24)"
        printf '\nرمز دیتابیس ساخته‌شده: %s\n' "$DB_PASSWORD"
    fi

    mysql -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';"
    mysql -e "ALTER USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';"
    mysql -e "GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost'; FLUSH PRIVILEGES;"

    sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=mysql|" .env
    sed -i "s|^# *DB_HOST=.*|DB_HOST=127.0.0.1|;    s|^DB_HOST=.*|DB_HOST=127.0.0.1|" .env
    sed -i "s|^# *DB_PORT=.*|DB_PORT=3306|;         s|^DB_PORT=.*|DB_PORT=3306|" .env
    sed -i "s|^# *DB_DATABASE=.*|DB_DATABASE=$DB_NAME|; s|^DB_DATABASE=.*|DB_DATABASE=$DB_NAME|" .env
    sed -i "s|^# *DB_USERNAME=.*|DB_USERNAME=$DB_USER|; s|^DB_USERNAME=.*|DB_USERNAME=$DB_USER|" .env
    sed -i "s|^# *DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWORD|; s|^DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWORD|" .env
else
    sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=sqlite|" .env
    touch database/database.sqlite
fi

# --------------------------------------------------------------- دیتابیس ----
step "مهاجرت دیتابیس"

if php artisan migrate:status >/dev/null 2>&1; then
    php artisan migrate --force            # نصب قبلی: فقط مهاجرت‌های جدید
else
    php artisan migrate --seed --force     # نصب تازه: با داده‌ی نمونه
fi

php artisan storage:link || true

# ------------------------------------------------------------- دسترسی‌ها ----
step "تنظیم دسترسی‌ها و کش"

php artisan optimize
chown -R www-data:www-data "$APP_DIR"
find "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" -type d -exec chmod 775 {} +
find "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" -type f -exec chmod 664 {} +
[ -f database/database.sqlite ] && chmod 664 database/database.sqlite

# ------------------------------------------------------------------ nginx ---
step "پیکربندی nginx"

# روی سرورهایی که IPv6 غیرفعال است، listen [::] باعث شکست nginx -t می‌شود
IPV6_LISTEN=""
[ -f /proc/net/if_inet6 ] && IPV6_LISTEN="listen [::]:80;"

cat > /etc/nginx/sites-available/kian << NGINX
server {
    listen 80;
    $IPV6_LISTEN
    server_name ${DOMAIN:-_};
    root $APP_DIR/public;

    index index.php;
    charset utf-8;
    client_max_body_size 32M;

    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ ^/index\.php(/|\$) {
        fastcgi_pass unix:/run/php/php$PHP_VERSION-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)\$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT \$realpath_root;
        internal;
    }

    # فونت‌ها و assets ساخته‌شده‌ی Vite هش دارند، پس تا یک سال کش می‌شوند
    location ~* ^/build/.*\.(css|js|woff2?)\$ {
        expires 1y;
        access_log off;
        add_header Cache-Control "public, immutable";
    }

    location ~* \.(svg|png|jpg|jpeg|webp|avif|ico|mp4)\$ {
        expires 30d;
        access_log off;
    }

    location ~ /\.(?!well-known).* { deny all; }

    gzip on;
    gzip_types text/css application/javascript application/json image/svg+xml application/xml;
    gzip_min_length 512;

    error_page 404 /index.php;
    access_log /var/log/nginx/kian-access.log;
    error_log  /var/log/nginx/kian-error.log;
}
NGINX

ln -sf /etc/nginx/sites-available/kian /etc/nginx/sites-enabled/kian
rm -f /etc/nginx/sites-enabled/default
nginx -t

if [ -d /run/systemd/system ]; then
    systemctl enable --now "php$PHP_VERSION-fpm" nginx
    systemctl reload nginx
else
    # کانتینر یا WSL بدون systemd
    service "php$PHP_VERSION-fpm" restart || true
    service nginx restart || true
fi

# -------------------------------------------------------------------- ssl ---
if [ "$SSL" = "1" ] && [ -n "$DOMAIN" ]; then
    step "گرفتن گواهی SSL برای $DOMAIN"
    apt-get install -y -qq certbot python3-certbot-nginx >/dev/null
    certbot --nginx -d "$DOMAIN" --non-interactive --agree-tos --register-unsafely-without-email --redirect
fi

# ------------------------------------------------------------------ پایان ---
printf '\n\033[1;32m✓ نصب کامل شد.\033[0m\n'
printf '  آدرس : %s\n' "$APP_URL"
printf '  مسیر : %s\n' "$APP_DIR"
printf '  دیتابیس: %s\n\n' "$DB"
printf 'به‌روزرسانی بعدی:\n  cd %s && sudo SKIP_SYSTEM=1 bash deploy/install.sh\n\n' "$APP_DIR"
