#!/usr/bin/env bash
#
# ساختِ بسته‌ی نصب برای سروری که به گیت‌هاب دسترسی ندارد.
#
#   bash deploy/package.sh            # از HEAD
#   bash deploy/package.sh <commit>   # از یک کامیتِ مشخص
#
# روی دستگاهی اجرا می‌شود که هم گیت‌هاب را می‌بیند و هم composer و npm دارد.
# خروجی: dist/kian-release-<commit>.tar.gz — کد + vendor + assetهای ساخته‌شده،
# یعنی سرور برای نصبش نه گیت لازم دارد، نه composer، نه npm.
#
# نصب روی سرور:
#   scp dist/kian-release-X.tar.gz root@SERVER:/root/
#   tar -xzOf /root/kian-release-X.tar.gz ./deploy/install.sh \
#     | sudo BUNDLE=/root/kian-release-X.tar.gz SKIP_SYSTEM=1 bash
#
set -euo pipefail

ROOT="$(git rev-parse --show-toplevel)"
cd "$ROOT"

REF="${1:-HEAD}"
SHA="$(git rev-parse --short=8 "$REF")"
OUT="${OUT:-$ROOT/dist}"
WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT

step() { printf '\n\033[1;33m==>\033[0m %s\n' "$*"; }

# فقط آنچه در گیت است: ‎.env و دیتابیس و آپلودهای محلی هرگز در بسته نمی‌آیند
step "کد از $SHA"
git archive "$REF" | tar -x -C "$WORK"

step "وابستگی‌های PHP"
#
# --no-scripts: package:discover بی ‎.env در این پوشه اجرا نمی‌شود؛ نصب‌کننده
# روی سرور اجرایش می‌کند. نسخه‌ی PHP ِ این دستگاه هم مهم نیست — قفل برای
# ‎8.4 است و فقط همان روی سرور اجرا می‌شود.
PLATFORM=()
php -r 'exit(version_compare(PHP_VERSION, "8.4.1", ">=") ? 0 : 1);' || PLATFORM=(--ignore-platform-req=php)
(cd "$WORK" && COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader \
    --no-interaction --prefer-dist --no-scripts --no-progress -q "${PLATFORM[@]}")

# اگر دانلودِ dist شکست بخورد، composer کلِ مخزنِ هر بسته را با تاریخچه‌اش
# کلون می‌کند — بسته از ۲۰ به ۲۷۰ مگابایت می‌رسید. تاریخچه به کارِ سرور نمی‌آید.
find "$WORK/vendor" -name .git -type d -prune -exec rm -rf {} +

step "ساختِ assets"
(cd "$WORK" && npm ci --no-audit --no-fund --silent && npm run build --silent >/dev/null && rm -rf node_modules)
[ -f "$WORK/public/build/manifest.json" ] || { echo "ساختِ assets مانیفست نداد." >&2; exit 1; }

rm -f "$WORK"/bootstrap/cache/*.php

# شناسنامه: سرور با آن می‌فهمد چه نسخه‌ای نصب است — آنجا گیتی نیست که بپرسد
{
    echo "commit: $(git rev-parse "$REF")"
    echo "date: $(git log -1 --format=%cI "$REF")"
    echo "subject: $(git log -1 --format=%s "$REF")"
    echo "built: $(date -Is)"
} > "$WORK/RELEASE"

mkdir -p "$OUT"
FILE="$OUT/kian-release-$SHA.tar.gz"
tar -czf "$FILE" -C "$WORK" .

step "آماده"
printf '  %s\n  حجم: %s\n  sha256: %s\n' "$FILE" "$(du -h "$FILE" | cut -f1)" "$(sha256sum "$FILE" | cut -d' ' -f1)"
