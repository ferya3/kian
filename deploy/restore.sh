#!/usr/bin/env bash
#
# بازگردانیِ پشتیبانِ کیان بهساز روی یک نصبِ تازه.
#
#   sudo APP_URL=http://1.2.3.4 bash restore.sh /root/kian-backup-YYYYMMDD-HHMMSS.tar.gz
#
# یا مستقیم از مخزن:
#
#   curl -fsSL https://raw.githubusercontent.com/ferya3/kian/<branch>/deploy/restore.sh \
#     | sudo APP_URL=http://1.2.3.4 bash -s -- /root/kian-backup-….tar.gz
#
# پیش‌نیاز: install.sh یک‌بار روی همین سرور اجرا شده باشد.
#
# پشتیبانی که با دستورِ README ساخته شده باشد را می‌شناسد (database.sqlite،
# ‎.env و uploads.tar.gz در یک پوشه)، و پشتیبانی که کلِ پوشه‌ی سایت را دارد
# (…/database/database.sqlite و …/storage/app/public). هر چیزِ دیگری را با
# فهرستِ محتوایش رد می‌کند و دست به سایت نمی‌زند.
#
# متغیرها:
#   APP_URL   نشانیِ سایت روی سرورِ تازه. خالی یعنی http://<نخستین IP سرور>.
#             تا دامنه به این سرور اشاره نکرده، IP بگذارید — APP_URL ِ پشتیبان
#             به سرورِ قدیم اشاره می‌کند.
#   APP_DIR   پیش‌فرض /var/www/kian
#
set -Eeuo pipefail

APP_DIR="${APP_DIR:-/var/www/kian}"
PHP_VERSION="${PHP_VERSION:-8.4}"
ARCHIVE="${1:-}"

step() { printf '\n\033[1;33m==>\033[0m %s\n' "$*"; }
ok()   { printf '  \033[1;32m✓\033[0m %s\n' "$*"; }
warn() { printf '  \033[1;33m!\033[0m %s\n' "$*"; }
die()  { printf '\n\033[1;31mخطا:\033[0m %s\n\n' "$*" >&2; exit 1; }

as_web() { sudo -u www-data "$@"; }
artisan() { as_web php "$APP_DIR/artisan" "$@"; }

# -------------------------------------------------------------- پیش‌شرط ----
[ "$(id -u)" -eq 0 ] || die "با sudo اجرا کنید."
[ -n "$ARCHIVE" ] || die "مسیرِ فایلِ پشتیبان را بدهید:  sudo bash restore.sh /root/kian-backup-….tar.gz"
[ -f "$ARCHIVE" ] || die "فایل پیدا نشد: $ARCHIVE"
[ -f "$APP_DIR/artisan" ] || die "سایت در $APP_DIR نصب نیست. اول install.sh را اجرا کنید، بعد این را."
command -v php >/dev/null || die "php نصب نیست — install.sh کامل اجرا نشده."

step "فایلِ پشتیبان"
echo "  $ARCHIVE"
ls -lh "$ARCHIVE" | awk '{print "  حجم: " $5}'
echo "  sha256: $(sha256sum "$ARCHIVE" | cut -d' ' -f1)"
echo "  (همان عددی باید باشد که سرورِ قدیم موقعِ ساختنِ پشتیبان چاپ کرد)"

# ------------------------------------------------------------ بازکردن ----
WORK="$(mktemp -d)"
cleanup() { rm -rf "$WORK"; }
trap cleanup EXIT

step "بازکردن"
case "$ARCHIVE" in
    *.tar.gz|*.tgz) tar -xzf "$ARCHIVE" -C "$WORK" ;;
    *.tar)          tar -xf  "$ARCHIVE" -C "$WORK" ;;
    *.zip)          command -v unzip >/dev/null || apt-get install -y -qq unzip >/dev/null
                    unzip -q "$ARCHIVE" -d "$WORK" ;;
    *) die "قالبِ فایل شناخته نشد ($ARCHIVE). ‎.tar.gz یا ‎.zip بدهید." ;;
esac

# فقط یک دیتابیس؛ دو تا یعنی نمی‌دانیم کدام — حدس نمی‌زنیم
mapfile -t DBS < <(find "$WORK" -type f -name 'database.sqlite')
if [ "${#DBS[@]}" -ne 1 ]; then
    echo "  محتوای پشتیبان:" >&2
    (cd "$WORK" && find . -maxdepth 4 | head -40 | sed 's/^/    /') >&2
    die "پشتیبان باید دقیقاً یک database.sqlite داشته باشد؛ ${#DBS[@]} تا پیدا شد. چیزی تغییر نکرد."
fi
DB_SRC="${DBS[0]}"
ROOT="$(dirname "$DB_SRC")"
# پشتیبانِ کلِ پوشه: database.sqlite داخلِ database/ است و ریشه یک سطح بالاتر
[ "$(basename "$ROOT")" = "database" ] && [ -f "$ROOT/../artisan" -o -d "$ROOT/../storage" ] && ROOT="$(dirname "$ROOT")"

ENV_SRC=""
for c in "$ROOT/.env" "$(dirname "$DB_SRC")/.env"; do [ -f "$c" ] && ENV_SRC="$c" && break; done

UPLOADS_TAR=""; UPLOADS_DIR=""
if [ -f "$(dirname "$DB_SRC")/uploads.tar.gz" ]; then
    UPLOADS_TAR="$(dirname "$DB_SRC")/uploads.tar.gz"
elif [ -d "$ROOT/storage/app/public" ]; then
    UPLOADS_DIR="$ROOT/storage/app/public"
fi

ok "دیتابیس: ${DB_SRC#$WORK/}"
[ -n "$ENV_SRC" ] && ok ".env: ${ENV_SRC#$WORK/}" || warn ".env در پشتیبان نیست — APP_KEY ِ تازه می‌ماند؛ مقدارهای رمزنگاری‌شده‌ی قدیم باز نمی‌شوند."
if [ -n "$UPLOADS_TAR" ]; then ok "عکس‌ها: ${UPLOADS_TAR#$WORK/}"
elif [ -n "$UPLOADS_DIR" ]; then ok "عکس‌ها: ${UPLOADS_DIR#$WORK/}"
else warn "پوشه‌ی عکس‌ها در پشتیبان نیست — متن‌ها برمی‌گردند ولی عکس‌های آپلودشده نه."
fi
[ -f "$(dirname "$DB_SRC")/MANIFEST.txt" ] && sed 's/^/  /' "$(dirname "$DB_SRC")/MANIFEST.txt"

if [ -n "$ENV_SRC" ]; then
    CONN="$(sed -n 's/^DB_CONNECTION=//p' "$ENV_SRC" | tail -1 | tr -d '"\r ')"
    case "${CONN:-sqlite}" in
        sqlite) ;;
        *) die "‎.env ِ پشتیبان DB_CONNECTION=$CONN دارد ولی دیتابیسِ پشتیبان sqlite است. چیزی تغییر نکرد." ;;
    esac
fi

# ---------------------------------------------------------- سلامتِ دیتابیس ----
step "سلامتِ دیتابیسِ پشتیبان"
php -r '
try {
    $r = (new PDO("sqlite:".$argv[1]))->query("PRAGMA integrity_check")->fetchColumn();
} catch (Throwable $e) { $r = $e->getMessage(); }
if ($r !== "ok") { fwrite(STDERR, "  integrity_check: $r\n"); exit(1); }
' "$DB_SRC" || die "دیتابیسِ پشتیبان خراب است. چیزی تغییر نکرد."
ok "integrity_check: ok"

# ------------------------------------------------------- نقطه‌ی بازگشت ----
#
# از اینجا سایت دست می‌خورد. هر شکستی همه‌چیز را به همین حالت برمی‌گرداند.
STAMP="$(date +%Y%m%d-%H%M%S)"
SNAP="/root/kian-pre-restore-$STAMP"
mkdir -p "$SNAP"
cp -a "$APP_DIR/database/database.sqlite" "$SNAP/" 2>/dev/null || true
cp -a "$APP_DIR/.env" "$SNAP/" 2>/dev/null || true
tar -czf "$SNAP/uploads.tar.gz" -C "$APP_DIR/storage/app" public 2>/dev/null || true

rollback() {
    local code=$?
    trap - ERR
    printf '\n\033[1;31m✗ بازگردانی شکست خورد — سایت به حالتِ پیش از آن برمی‌گردد…\033[0m\n' >&2
    rm -f "$APP_DIR/database/database.sqlite-wal" "$APP_DIR/database/database.sqlite-shm"
    [ -f "$SNAP/database.sqlite" ] && cp -a "$SNAP/database.sqlite" "$APP_DIR/database/database.sqlite"
    [ -f "$SNAP/.env" ] && cp -a "$SNAP/.env" "$APP_DIR/.env"
    if [ -f "$SNAP/uploads.tar.gz" ]; then
        rm -rf "$APP_DIR/storage/app/public"
        tar -xzf "$SNAP/uploads.tar.gz" -C "$APP_DIR/storage/app"
    fi
    chown -R www-data:www-data "$APP_DIR/database" "$APP_DIR/storage" "$APP_DIR/.env" 2>/dev/null || true
    artisan optimize:clear >/dev/null 2>&1 || true
    artisan up >/dev/null 2>&1 || true
    printf '  نسخه‌ی پیش از بازگردانی در %s است.\n\n' "$SNAP" >&2
    exit "$code"
}
trap rollback ERR

step "نقطه‌ی بازگشت: $SNAP"
artisan down --retry=15 >/dev/null 2>&1 || true

# ------------------------------------------------------------ جای‌گذاری ----
step "جای‌گذاریِ دیتابیس"
#
# پرونده‌های -wal و -shm ِ دیتابیسِ تازه باید بروند: SQLite آن‌ها را روی هر
# پرونده‌ای که هم‌نامشان باشد اعمال می‌کند — یعنی روی دیتابیسِ بازگردانده‌شده —
# و نتیجه دیتابیسِ خراب است.
rm -f "$APP_DIR/database/database.sqlite-wal" "$APP_DIR/database/database.sqlite-shm"
install -o www-data -g www-data -m 664 "$DB_SRC" "$APP_DIR/database/database.sqlite"
chown www-data:www-data "$APP_DIR/database"
ok "دیتابیس سرِ جایش است"

if [ -n "$ENV_SRC" ]; then
    step "جای‌گذاریِ ‎.env"
    install -o www-data -g www-data -m 640 "$ENV_SRC" "$APP_DIR/.env"
    grep -q '^APP_KEY=base64:' "$APP_DIR/.env" && ok "APP_KEY ِ پشتیبان حفظ شد" || warn "APP_KEY در ‎.env ِ پشتیبان نیست."
fi

# نشانیِ سایت: APP_URL ِ پشتیبان به سرورِ قدیم اشاره می‌کند
NEW_URL="${APP_URL:-http://$(hostname -I | awk '{print $1}')}"
if grep -q '^APP_URL=' "$APP_DIR/.env"; then
    sed -i "s|^APP_URL=.*|APP_URL=$NEW_URL|" "$APP_DIR/.env"
else
    echo "APP_URL=$NEW_URL" >> "$APP_DIR/.env"
fi
ok "APP_URL=$NEW_URL"
# کوکیِ نشست که به دامنه یا https ِ سرورِ قدیم بسته شده، ورود به پنل را روی
# IP بی‌صدا ناممکن می‌کند: فرم می‌رود و دوباره به صفحه‌ی ورود برمی‌گردد.
for k in SESSION_DOMAIN SESSION_SECURE_COOKIE; do
    v="$(sed -n "s/^$k=//p" "$APP_DIR/.env" | tail -1 | tr -d '"\r ')"
    case "$v" in ''|null|false) ;; *) warn "$k=$v در ‎.env است؛ تا دامنه و https برنگشته، ورود به پنل ممکن است کار نکند." ;; esac
done

step "جای‌گذاریِ عکس‌ها"
mkdir -p "$APP_DIR/storage/app/public"
if [ -n "$UPLOADS_TAR" ]; then
    tar -xzf "$UPLOADS_TAR" -C "$APP_DIR/storage/app"
elif [ -n "$UPLOADS_DIR" ]; then
    cp -a "$UPLOADS_DIR/." "$APP_DIR/storage/app/public/"
fi
chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
find "$APP_DIR/storage" -type d -exec chmod 775 {} +
ok "$(find "$APP_DIR/storage/app/public" -type f | wc -l) پرونده در storage/app/public"

# ------------------------------------------------------- هم‌خوانی با کد ----
#
# پشتیبان از کدِ قدیم‌تر است. بی این، جدول‌هایی که کدِ امروز می‌خواهد —
# نشانی‌ها، بیوگرافی، … — نیستند و صفحه‌ها ۵۰۰ می‌دهند. مهاجرت‌های داده‌ای هم
# همین‌جا اجرا می‌شوند: نشانیِ قدیم نخستین ردیفِ «نشانی‌ها» می‌شود و
# شماره‌های واقعیِ کارخانه سرِ جایشان می‌نشینند.
step "به‌روزکردنِ دیتابیس برای کدِ فعلی"
artisan migrate --force
[ -e "$APP_DIR/public/storage" ] || artisan storage:link >/dev/null

step "کش"
artisan optimize:clear >/dev/null
artisan optimize >/dev/null
systemctl reload "php$PHP_VERSION-fpm" >/dev/null 2>&1 || service "php$PHP_VERSION-fpm" reload >/dev/null 2>&1 || true
artisan up >/dev/null

# ---------------------------------------------------------------- سلامت ----
step "بررسیِ سلامت"
HOST="${NEW_URL#*://}"; HOST="${HOST%%/*}"
CODE="$(curl -sL -o /dev/null -w '%{http_code}' --max-time 20 -k \
    --resolve "$HOST:80:127.0.0.1" --resolve "$HOST:443:127.0.0.1" \
    "$NEW_URL/" || true)"
case "$CODE" in
    2*) ok "صفحه‌ی اصلی: HTTP $CODE" ;;
    *)  tail -n 20 "$APP_DIR/storage/logs/laravel.log" 2>/dev/null | sed 's/^/    /' >&2
        printf '\n  سایت پس از بازگردانی جواب نداد (HTTP %s)\n' "${CODE:-بی‌پاسخ}" >&2
        false ;;
esac
trap - ERR

# ---------------------------------------------------------------- گزارش ----
step "گزارش"
php -r '
[$app] = array_slice($argv, 1);
$db = new PDO("sqlite:$app/database/database.sqlite");
$count = fn ($t) => (int) $db->query("SELECT COUNT(*) FROM \"$t\"")->fetchColumn();
foreach (["products" => "محصول", "projects" => "پروژه", "articles" => "مقاله", "locations" => "نشانی", "users" => "کاربرِ پنل"] as $t => $label) {
    try { printf("  %-12s %d\n", $label, $count($t)); } catch (Throwable) {}
}

/*
| هر نشانیِ عکسی که در دیتابیس هست، آیا روی دیسک هم هست؟
|
| همه‌ی جدول‌ها و ستون‌ها گشته می‌شوند و نه فهرستی دستی: ستونِ تصویرِ تازه‌ای
| که روزی اضافه شود، خودبه‌خود هم سنجیده می‌شود.
*/
$ext = "/\.(jpe?g|png|webp|gif|svg|avif)$/i";
$found = []; $missing = [];
foreach ($db->query("SELECT name FROM sqlite_master WHERE type=\"table\" AND name NOT LIKE \"sqlite_%\"")->fetchAll(PDO::FETCH_COLUMN) as $t) {
    foreach ($db->query("PRAGMA table_info(\"$t\")")->fetchAll(PDO::FETCH_ASSOC) as $col) {
        $c = $col["name"];
        foreach ($db->query("SELECT \"$c\" FROM \"$t\" WHERE \"$c\" LIKE \"%.%\"")->fetchAll(PDO::FETCH_COLUMN) as $v) {
            $v = (string) $v;
            $paths = str_starts_with(ltrim($v), "[") ? (json_decode($v, true) ?: []) : [$v];
            foreach ($paths as $p) {
                if (! is_string($p) || ! preg_match($ext, $p) || str_contains($p, " ") || preg_match("~^https?://~", $p)) continue;
                $file = str_starts_with($p, "/") ? "$app/public$p" : "$app/storage/app/public/$p";
                if (is_file($file)) $found[$p] = true; else $missing[$p] = "$t.$c";
            }
        }
    }
}
printf("\n  عکس‌هایی که دیتابیس به آن‌ها اشاره می‌کند: %d — روی دیسک: %d، گم: %d\n", count($found) + count($missing), count($found), count($missing));
foreach (array_slice($missing, 0, 15, true) as $p => $where) printf("    ✗ %s  (%s)\n", $p, $where);
if (count($missing) > 15) printf("    … و %d تای دیگر\n", count($missing) - 15);

echo "\n  ورود به پنل با یکی از این‌ها:\n";
foreach ($db->query("SELECT email, role FROM users WHERE is_active = 1 ORDER BY id") as $u) printf("    %s (%s)\n", $u["email"], $u["role"]);
' "$APP_DIR"

printf '\n\033[1;32m✓ بازگردانی کامل شد.\033[0m\n'
printf '  سایت: %s\n' "$NEW_URL"
printf '  نسخه‌ی پیش از بازگردانی: %s\n\n' "$SNAP"
