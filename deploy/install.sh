#!/usr/bin/env bash
#
# نصب سایت کیان بهساز روی سرور اوبونتو (۲۲.۰۴ / ۲۴.۰۴)
#
#   curl -fsSL https://raw.githubusercontent.com/ferya3/kian/claude/loving-babbage-3kd7tm/deploy/install.sh | sudo bash
#
# متغیرهای قابل تنظیم (همه اختیاری):
#   DOMAIN=kianbehsaz.ir     دامنه‌ی سایت؛ پیش‌فرض: هر هاستی
#   APP_DIR=/var/www/kian    مسیر نصب
#   BRANCH=main              برنچ گیت
#   DB=sqlite|mysql          موتور دیتابیس؛ پیش‌فرض sqlite
#   DB_PASSWORD=...          رمز کاربر mysql (اگر DB=mysql و خالی باشد، ساخته می‌شود)
#   SSL=1                    گرفتن گواهی Let's Encrypt (نیازمند DOMAIN و DNS آماده)
#   SKIP_SYSTEM=1            پرش از نصب بسته‌های سیستمی (برای اجرای مجدد/به‌روزرسانی)
#
# برای به‌روزرسانی، همین دستور را با SKIP_SYSTEM=1 دوباره اجرا کنید. اجرای آن از
# روی curl (نه از نسخه‌ی داخل سرور) امن‌تر است: اسکریپت داخل سرور ممکن است قدیمی
# باشد و اصلاحات بعدی — مثل گارد safe.directory گیت — را نداشته باشد.
#
set -euo pipefail

REPO="${REPO:-https://github.com/ferya3/kian.git}"
SELF_URL="${SELF_URL:-https://raw.githubusercontent.com/ferya3/kian/claude/loving-babbage-3kd7tm/deploy/install.sh}"
DEFAULT_BRANCH="claude/loving-babbage-3kd7tm"
# خالی یعنی «هرچه روی سرور هست»؛ پایین‌تر، پس از پیداشدن نصبِ قبلی، پر می‌شود
BRANCH="${BRANCH:-}"
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

# -------------------------------------------------------------- بازگشت ----
#
# از اینجا به بعد سایتِ زنده دست می‌خورد.
#
# git reset کلِ کد را یک‌باره عوض می‌کند، ولی وابستگی و assetها و مهاجرت و
# کش دقایقی بعد می‌رسند. در آن فاصله سایت با کدِ تازه و بقیه‌ی چیزهای کهنه
# کار می‌کند — و اگر چیزی در میانه بشکند (ساختِ assets روی سرورِ کم‌حافظه،
# شبکه، مهاجرت)، اسکریپت می‌ایستد و سایت همان‌طور خراب می‌ماند.
#
# پس پیش از هر تغییر، نقطه‌ی بازگشت برمی‌داریم و یک تله می‌گذاریم: هر
# شکستی، همه‌چیز را به حالت پیش از اجرا برمی‌گرداند.
#
# فقط برای نصبِ موجود. نصبِ تازه چیزی ندارد که به آن برگردد.
ROLLBACK_DIR=""
ROLLBACK_REF=""

snapshot() {
    [ -d "$APP_DIR/.git" ] || return 0

    ROLLBACK_REF="$(git -C "$APP_DIR" rev-parse HEAD 2>/dev/null || true)"
    ROLLBACK_DIR="$(mktemp -d)"

    # assetهای ساخته‌شده و کشِ پیکربندی: هر دو پس از شکست، نیمه‌کاره می‌مانند
    [ -d "$APP_DIR/public/build" ] && cp -a "$APP_DIR/public/build" "$ROLLBACK_DIR/build"
    [ -d "$APP_DIR/bootstrap/cache" ] && cp -a "$APP_DIR/bootstrap/cache" "$ROLLBACK_DIR/cache"

    #
    # ‏.env هم، چون همین اسکریپت دست‌کاری‌اش می‌کند.
    #
    # پیام شکست می‌گفت «‎.env دست نخورده» و راست نبود.
    [ -f "$APP_DIR/.env" ] && cp -a "$APP_DIR/.env" "$ROLLBACK_DIR/env"

    step "نقطه‌ی بازگشت: ${ROLLBACK_REF:0:8}"
}

rollback() {
    local code=$?

    trap - ERR EXIT
    [ "$code" -eq 0 ] && return 0
    [ -n "$ROLLBACK_REF" ] || return 0

    printf '\n\033[1;31m✗ به‌روزرسانی شکست خورد — برگرداندن سایت به حالت قبل…\033[0m\n' >&2

    git -C "$APP_DIR" reset --hard "$ROLLBACK_REF" >/dev/null 2>&1 || true

    if [ -d "$ROLLBACK_DIR/build" ]; then
        rm -rf "$APP_DIR/public/build"
        cp -a "$ROLLBACK_DIR/build" "$APP_DIR/public/build"
    fi

    if [ -d "$ROLLBACK_DIR/cache" ]; then
        rm -rf "$APP_DIR/bootstrap/cache"
        cp -a "$ROLLBACK_DIR/cache" "$APP_DIR/bootstrap/cache"
    fi

    [ -f "$ROLLBACK_DIR/env" ] && cp -a "$ROLLBACK_DIR/env" "$APP_DIR/.env"

    # ساختِ نیمه‌کاره نباید در public بماند؛ nginx سرو می‌کندش
    rm -rf "$APP_DIR/public/build-next"

    chown -R www-data:www-data "$APP_DIR" 2>/dev/null || true
    systemctl reload "php$PHP_VERSION-fpm" 2>/dev/null || true

    printf '\033[1;33m  سایت به کامیت %s برگشت و باید بالا باشد.\033[0m\n' "${ROLLBACK_REF:0:8}" >&2
    printf '  خطای بالا را بفرستید؛ دیتابیس دست نخورده است و .env هم به حالت قبل برگشت.\n\n' >&2

    exit "$code"
}

trap rollback ERR EXIT

snapshot

# ------------------------------------------------------------------ سورس ----
step "دریافت سورس در $APP_DIR"

# بعد از chown به www-data، اجرای مجدد اسکریپت با root به گارد
# «dubious ownership» گیت می‌خورد؛ این خط اجازه‌ی به‌روزرسانی را می‌دهد.
git config --global --add safe.directory "$APP_DIR" 2>/dev/null || true

# ---------------------------------------------------------------- برنچ ----
#
# اگر کاربر BRANCH نداده و نصبِ قبلی هست، همان برنچی که روی سرور است می‌ماند.
#
# پیش‌تر اینجا یک عددِ ثابت بود و به‌روزرسانیِ بدونِ BRANCH سرور را با
# reset --hard به آن برنچ می‌برد — یعنی سروری که روی برنچ تازه بود، بی‌صدا
# صدوشصت کامیت عقب می‌رفت. دستورِ به‌روزرسانیِ خودِ README هم همین تله را
# داشت.
if [ -z "$BRANCH" ] && [ -d "$APP_DIR/.git" ]; then
    BRANCH="$(git -C "$APP_DIR" rev-parse --abbrev-ref HEAD 2>/dev/null || true)"

    # HEAD جداافتاده نامِ برنچ ندارد و رشته‌ی «HEAD» برمی‌گرداند
    [ "$BRANCH" = "HEAD" ] && BRANCH=""

    [ -n "$BRANCH" ] && step "برنچِ روی سرور: $BRANCH"
fi

BRANCH="${BRANCH:-$DEFAULT_BRANCH}"

if [ -d "$APP_DIR/.git" ]; then
    # refspec صریح لازم است: کلون با --branch تک‌برنچی است و فقط برنچِ همان
    # کلون را در refs/remotes نگه می‌دارد. با fetch ساده، origin/$BRANCH برای
    # هر برنچ دیگری اصلاً ساخته نمی‌شود و reset با «unknown revision» می‌ایستد.
    git -C "$APP_DIR" fetch --depth 1 origin "+refs/heads/$BRANCH:refs/remotes/origin/$BRANCH"
    git -C "$APP_DIR" reset --hard "refs/remotes/origin/$BRANCH"

    # نام برنچ محلی هم با برنچ مقصد یکی می‌شود؛ وگرنه پس از تعویض BRANCH،
    # git status نام برنچ قبلی را نشان می‌دهد و گمراه‌کننده است.
    git -C "$APP_DIR" checkout -B "$BRANCH"
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

#
# حافظه‌ی مبادله، اگر رم کم است و swap ندارد.
#
# ساختِ assets سنگین‌ترین مرحله‌ی این اسکریپت است و روی سرورِ دو گیگابایتیِ
# بی‌swap، کرنل فرایندِ node را می‌کُشد. پیامش هم گویا نیست: «Killed» و
# کدِ ۱۳۷. نتیجه‌اش public/build نیمه‌کاره است، یعنی مانیفستِ ناقص و ۵۰۰
# روی هر صفحه — همان خرابی‌ای که پس از به‌روزرسانی دیده می‌شد.
#
# یک‌بار ساخته می‌شود و می‌ماند؛ اجرای دوباره دست نمی‌زند.
TOTAL_RAM_MB="$(free -m | awk '/^Mem:/{print $2}')"
SWAP_MB="$(free -m | awk '/^Swap:/{print $2}')"

if [ "${TOTAL_RAM_MB:-0}" -lt 3000 ] && [ "${SWAP_MB:-0}" -lt 512 ] && [ ! -f /swapfile ]; then
    step "ساختن ۲ گیگابایت swap (رم ${TOTAL_RAM_MB}MB، بی‌swap)"

    if fallocate -l 2G /swapfile 2>/dev/null || dd if=/dev/zero of=/swapfile bs=1M count=2048 status=none; then
        chmod 600 /swapfile
        mkswap /swapfile >/dev/null
        swapon /swapfile
        grep -q '^/swapfile' /etc/fstab || echo '/swapfile none swap sw 0 0' >> /etc/fstab
    else
        printf '  \033[1;33mswap ساخته نشد؛ ساختِ assets ممکن است با کمبود حافظه بمیرد.\033[0m\n'
    fi
fi

#
# ساخت در جای دیگر، و جابه‌جایی در پایان.
#
# vite پیش از ساختن، پوشه‌ی مقصد را خالی می‌کند. اگر مستقیم روی
# public/build می‌ساخت و وسطِ کار می‌مُرد، assetهای سالمِ قبلی هم رفته
# بودند و سایت تا پایانِ عیب‌یابی پایین می‌ماند. این‌طور، تا لحظه‌ی آخر
# نسخه‌ی قبلی سرِ جایش است.
BUILD_TMP="$APP_DIR/public/build-next"
rm -rf "$BUILD_TMP"

npm run build -- --outDir "$BUILD_TMP" --emptyOutDir

[ -f "$BUILD_TMP/manifest.json" ] || [ -f "$BUILD_TMP/.vite/manifest.json" ] \
    || die "ساختِ assets مانیفست نداد — جابه‌جایی انجام نشد و سایت دست‌نخورده ماند."

rm -rf "$APP_DIR/public/build"
mv "$BUILD_TMP" "$APP_DIR/public/build"

# -------------------------------------------------------------------- env ---
step "پیکربندی محیط"

[ -f .env ] || cp .env.example .env
grep -q '^APP_KEY=base64' .env || php artisan key:generate --force

if [ -n "$DOMAIN" ]; then
    #
    # با SSL=1 هم فعلاً http می‌نشیند؛ https بعد از موفقیتِ certbot.
    #
    # ترتیب مهم است. تا گرفتنِ گواهی صد خط مانده، و اگر certbot شکست بخورد
    # — DNS هنوز نرسیده، چالش به سرور نمی‌رسد، سقفِ درخواست پر است — ‎.env
    # با https می‌ماند و هیچ‌چیز روی ۴۴۳ نیست. آن‌وقت لاراول هر بازدید را
    # به https می‌فرستد و سایت از بیرون بسته می‌شود، در حالی که از داخل
    # سالم است و هیچ لاگی هم ندارد. همان «بعد از هر به‌روزرسانی بالا
    # نمی‌آید».
    APP_URL="http://$DOMAIN"
else
    #
    # بی DOMAIN، نشانیِ موجود دست نمی‌خورد.
    #
    # پیش‌تر اینجا بی‌قید و شرط IP سرور می‌نشست، یعنی اولین به‌روزرسانیِ
    # بدونِ DOMAIN، دامنه‌ای را که مدیر دستی در .env گذاشته بود پاک می‌کرد.
    # اثرش بی‌صداست: سایت بالا می‌آید ولی canonical و sitemap و لینک‌های
    # مطلق به IP اشاره می‌کنند.
    #
    # فقط نصبِ تازه — یا .env ای که هنوز روی مقدارِ نمونه مانده — IP می‌گیرد.
    APP_URL="$(sed -n 's/^APP_URL=//p' .env | head -1 | tr -d '\r"')"

    case "$APP_URL" in
        ''|http://localhost*|https://localhost*)
            APP_URL="http://$(hostname -I | awk '{print $1}')" ;;
    esac
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

# پیوند storage برای تصویرهای آپلودشده حیاتی است: بدون آن هر عکس ۴۰۳ می‌گیرد
# و سایت بی‌صدا بدون تصویر می‌ماند. پس شکستش را رد نمی‌کنیم.
php artisan storage:link || true

if [ ! -e public/storage ]; then
    printf '\n\033[1;31m✗ پیوند public/storage ساخته نشد — تصویرهای آپلودشده نمایش داده نمی‌شوند.\033[0m\n'
    printf '  دستی اجرا کنید: cd %s && sudo -u www-data php artisan storage:link\n\n' "$APP_DIR"
fi

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

# بی دامنه، بلوک باید catch-all باشد؛ با دامنه، هم apex و هم www را بگیرد —
# وگرنه www به بلوکِ پیش‌فرضِ nginx می‌افتد و نه به سایت.
if [ -n "$DOMAIN" ]; then
    SERVER_NAME="$DOMAIN www.$DOMAIN"
else
    SERVER_NAME="_"
fi

cat > /etc/nginx/sites-available/kian << NGINX
server {
    listen 80;
    $IPV6_LISTEN
    server_name $SERVER_NAME;
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

    #
    # www هم در گواهی می‌آید، اگر رکوردش به همین سرور اشاره کند.
    #
    # شرطی است و نه همیشگی: certbot برای هر دامنه‌ای که در خط فرمان بیاید
    # چالش می‌گیرد، و اگر www جایی اشاره نکند کلِ درخواست شکست می‌خورد —
    # یعنی apex هم بی‌گواهی می‌ماند. پس اول می‌پرسیم DNS چه می‌گوید.
    CERT_DOMAINS="-d $DOMAIN"

    if [ "${WWW:-auto}" != "0" ] && getent hosts "www.$DOMAIN" >/dev/null 2>&1; then
        CERT_DOMAINS="$CERT_DOMAINS -d www.$DOMAIN"
        step "www.$DOMAIN هم در گواهی می‌آید"
    fi

    # shellcheck disable=SC2086
    certbot --nginx $CERT_DOMAINS --non-interactive --agree-tos --register-unsafely-without-email --redirect

    # حالا که گواهی گرفته شد و ۴۴۳ شنونده دارد، نشانی هم https می‌شود
    APP_URL="https://$DOMAIN"
    sed -i "s|^APP_URL=.*|APP_URL=$APP_URL|" .env
    php artisan config:cache >/dev/null
    chown www-data:www-data "$APP_DIR/.env"
    chown -R www-data:www-data "$APP_DIR/bootstrap/cache"
fi

# ------------------------------------------------------------- سلامت ----
#
# آخرین حرف را خودِ سایت می‌زند و نه موفق‌بودنِ دستورها.
#
# تا حالا اسکریپت «نصب کامل شد» می‌گفت چون هیچ فرمانی خطا نداده بود، در
# حالی که صفحه می‌توانست ۵۰۰ بدهد — مانیفستِ ناقص، کشِ کهنه، سرویسی که
# بالا نیامده. تنها سنجشِ معتبر، خواستنِ خودِ صفحه است.
#
# شکستش به تله‌ی بالا می‌خورد و همه‌چیز به حالت قبل برمی‌گردد.
step "بررسی سلامت سایت"

systemctl reload "php$PHP_VERSION-fpm" >/dev/null 2>&1 || true
systemctl reload nginx >/dev/null 2>&1 || true

HEALTH_HOST="${DOMAIN:-localhost}"
HEALTH_CODE=""
HEALTH_REDIRECT=""

# یک درخواست به همین سرور. «کد|نشانیِ بعدی» برمی‌گرداند.
#
# ‏--resolve دامنه را به ۱۲۷.۰.۰.۱ می‌بندد تا سنجش به DNS و شبکه‌ی بیرون
# گره نخورد، و ‎-k چون اینجا سلامتِ اپ سنجیده می‌شود و نه اعتبارِ گواهی.
hit() {
    curl -s -o /dev/null -w '%{http_code}|%{redirect_url}' --max-time 15 -k \
        --resolve "$HEALTH_HOST:80:127.0.0.1" \
        --resolve "$HEALTH_HOST:443:127.0.0.1" \
        -H "Host: $HEALTH_HOST" "$1" 2>/dev/null || true
}

#
# دنبالِ ریدایرکت می‌رویم، ولی در هر پرش https را به http برمی‌گردانیم.
#
# چون اینجا روی خودِ سرور ایستاده‌ایم و TLS ممکن است جای دیگری تمام شود —
# کلودفلر، یک لودبالانسر — پس نبودنِ شنونده روی ۴۴۳ لزوماً خرابی نیست.
# این پیمایش همان کاری را می‌کند که آن لایه می‌کند: درخواست را با http به
# مبدأ می‌رساند. اگر اپ سالم باشد، آخرش ۲xx می‌دهد.
#
# نتیجه در HEALTH_CODE، و نخستین ریدایرکت در HEALTH_REDIRECT.
walk() {
    local url="$1" out next hop=0

    HEALTH_CODE=""
    HEALTH_REDIRECT=""

    while [ "$hop" -lt 5 ]; do
        out="$(hit "$url")"
        HEALTH_CODE="${out%%|*}"
        next="${out#*|}"

        case "$HEALTH_CODE" in
            3*) [ -n "$next" ] || break
                [ "$hop" -eq 0 ] && HEALTH_REDIRECT="$next"
                url="http://${next#http*://}"
                hop=$((hop + 1)) ;;
            *) break ;;
        esac
    done
}

for _ in 1 2 3 4 5 6 7 8 9 10; do
    walk "http://$HEALTH_HOST/"

    case "$HEALTH_CODE" in
        2*) break ;;
    esac

    sleep 2
done

#
# فقط ۲xx سالم است.
#
# تا دیروز ۳xx هم «✓» می‌گرفت و دنبالش نمی‌رفت — و سروری که ریشه‌اش به
# بن‌بست ریدایرکت می‌شد، «نصب کامل شد» می‌گرفت.
case "$HEALTH_CODE" in
    2*)
        printf '  صفحه‌ی اصلی: HTTP %s ✓\n' "$HEALTH_CODE"
        ;;
    *)
        printf '\n  آخرین خطای لاراول:\n' >&2
        tail -n 20 "$APP_DIR/storage/logs/laravel.log" 2>/dev/null | sed 's/^/    /' >&2
        die "سایت پس از به‌روزرسانی بالا نیامد (HTTP ${HEALTH_CODE:-بی‌پاسخ})."
        ;;
esac

#
# اپ سالم است، ولی آیا بازدیدکننده هم به آن می‌رسد؟
#
# اگر ریشه به https می‌رود و اینجا چیزی روی ۴۴۳ نیست، یا جلوترش لایه‌ای
# هست که TLS را تمام می‌کند — که درست است — یا نیست و سایت از بیرون بسته
# است. از روی سرور نمی‌شود بینشان فرق گذاشت، پس برنمی‌گردانیم و فقط
# می‌گوییم؛ بازگرداندن هم دردی از این دوا نمی‌کند.
case "$HEALTH_REDIRECT" in
    https://*)
        if ! ss -ltn 2>/dev/null | grep -q ':443'; then
            printf '\n\033[1;33m  ! نشانیِ سایت https است ولی روی این سرور چیزی به ۴۴۳ گوش نمی‌دهد.\033[0m\n'
            printf '    اگر کلودفلر (ابر نارنجی) یا لودبالانسری جلوی سرور است، درست است و کاری لازم نیست.\n'
            printf '    وگرنه سایت از بیرون باز نمی‌شود. یکی از این دو:\n'
            printf '      گواهی بگیرید : sudo DOMAIN=%s SSL=1 bash deploy/install.sh\n' "$HEALTH_HOST"
            printf "      یا http کنید  : sudo sed -i 's|^APP_URL=.*|APP_URL=http://%s|' %s/.env && sudo -u www-data php artisan config:cache\n" "$HEALTH_HOST" "$APP_DIR"
        fi
        ;;
esac

# ------------------------------------------------------------------ پایان ---
printf '\n\033[1;32m✓ نصب کامل شد.\033[0m\n'
printf '  آدرس : %s\n' "$APP_URL"
printf '  مسیر : %s\n' "$APP_DIR"
printf '  دیتابیس: %s\n\n' "$DB"
printf 'پنل مدیریت: %s/admin\n' "$APP_URL"
printf '  ساخت کاربر مدیر (گذرواژه تعاملی پرسیده می‌شود، در history نمی‌ماند):\n'
printf '    cd %s && sudo -u www-data php artisan admin:create\n\n' "$APP_DIR"
printf 'به‌روزرسانی بعدی (همیشه آخرین نسخه‌ی اسکریپت را می‌گیرد):\n'
printf '  curl -fsSL %s | sudo SKIP_SYSTEM=1 bash\n\n' "$SELF_URL"
