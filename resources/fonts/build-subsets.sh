#!/usr/bin/env bash
#
# ساختِ زیرمجموعه‌ی قلم‌ها — فقط وقتی نسخه‌ی قلم عوض شود لازم است.
#
#   pip install fonttools brotli && bash resources/fonts/build-subsets.sh
#
# Vazirmatn کامل ۱۱۱ و Inter ۴۸ کیلوبایت بود و هر دو پیش از نخستین نقاشی
# دانلود می‌شوند؛ روی ۴G ِ کند، نزدیک به یک ثانیه از FCP مالِ همین‌ها بود.
# دو کار حجم را نصف می‌کند، بی تغییرِ ظاهر:
#   ۱. فقط نویسه‌هایی که سایت دارد: فارسی/عربی، لاتینِ پایه و Latin-1، نشانه‌ها.
#   ۲. محورِ وزن فقط ۴۰۰ تا ۸۰۰ — سایت وزنِ دیگری به کار نمی‌برد.
set -euo pipefail
cd "$(dirname "$0")/../.."
OUT=resources/fonts

LATIN="U+0020-007E,U+00A0-00FF,U+2010-2027,U+2030,U+2032-2033,U+2039-203A,U+20AC,U+2122,U+2190-2193,U+2212,U+2215"
ARABIC="U+0600-06FF,U+0750-077F,U+FB50-FDFF,U+FE70-FEFF,U+200C-200F,U+2066-2069,U+25CC"

pyftsubset "node_modules/vazirmatn/fonts/webfonts/Vazirmatn[wght].woff2" \
    --unicodes="$LATIN,$ARABIC,U+00D7,U+00F7" --layout-features='*' \
    --flavor=woff2 --output-file="$OUT/vazirmatn.tmp.woff2"
pyftsubset node_modules/@fontsource-variable/inter/files/inter-latin-wght-normal.woff2 \
    --unicodes="$LATIN" --layout-features='kern,liga,calt,ccmp,locl,mark,mkmk,tnum,case,ss01,ss02' \
    --flavor=woff2 --output-file="$OUT/inter.tmp.woff2"

python3 - "$OUT" <<'PY'
import sys
from fontTools.ttLib import TTFont
from fontTools.varLib import instancer
out = sys.argv[1]
for name in ("vazirmatn", "inter"):
    font = instancer.instantiateVariableFont(TTFont(f"{out}/{name}.tmp.woff2"), {"wght": (400, 800)})
    font.flavor = "woff2"
    font.save(f"{out}/{name}-subset.woff2")
PY
rm -f "$OUT"/*.tmp.woff2
ls -la "$OUT"/*-subset.woff2
