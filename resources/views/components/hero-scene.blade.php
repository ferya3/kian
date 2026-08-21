{{--
    صحنه‌ی سینمایی قهرمان.
    عمداً وکتور است و نه عکس استوک: بارگذاری فوری، وزن ناچیز، و لایه‌هایی که
    مستقل از هم پارالاکس می‌گیرند تا حسِ حرکت دوربین در عمق ساخته شود.
--}}
<div {{ $attributes->merge(['class' => 'pointer-events-none absolute inset-0 overflow-hidden']) }} aria-hidden="true">

    {{-- آسمانِ کوره --}}
    <div class="absolute inset-0" style="background: radial-gradient(120% 90% at 62% 12%, #3d1b0e 0%, #1c1109 42%, #0e0e0e 78%)"></div>

    {{-- تابش کوره — نفس می‌کشد --}}
    <div class="absolute left-[18%] top-[26%] h-[46vmax] w-[46vmax] -translate-x-1/2 rounded-full blur-[90px]"
         data-parallax="0.06"
         style="background: radial-gradient(circle, rgba(226,115,47,.34), rgba(180,85,45,.12) 45%, transparent 70%);
                animation: kiln-breathe 9s ease-in-out infinite"></div>

    {{-- لایه‌های خاک — مقاطع زمین‌شناسی معدن رس --}}
    <svg class="absolute inset-x-0 bottom-0 h-[42%] w-full sm:h-[62%]" viewBox="0 0 1600 520" preserveAspectRatio="xMidYMax slice"
         data-parallax="0.14">
        <defs>
            <linearGradient id="strata-a" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#5c2815"/><stop offset="1" stop-color="#3d1b0e"/>
            </linearGradient>
            <linearGradient id="strata-b" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#7c361c"/><stop offset="1" stop-color="#4d2212"/>
            </linearGradient>
        </defs>
        <path d="M0 210 C 260 168, 520 236, 800 200 S 1340 150, 1600 196 V520 H0 Z" fill="url(#strata-b)" opacity=".55"/>
        <path d="M0 288 C 300 250, 560 320, 860 282 S 1360 240, 1600 286 V520 H0 Z" fill="url(#strata-a)" opacity=".75"/>
        <path d="M0 372 C 320 344, 620 402, 920 372 S 1380 336, 1600 374 V520 H0 Z" fill="#2a1109"/>
        <g stroke="#b4552d" stroke-opacity=".16" stroke-width="1.2" fill="none">
            <path d="M0 240 C 300 206, 560 272, 860 236 S 1360 194, 1600 240"/>
            <path d="M0 322 C 320 292, 620 350, 920 320 S 1380 284, 1600 322"/>
        </g>
    </svg>

    {{-- سایه‌نمای کارخانه --}}
    <svg class="absolute bottom-[24%] left-0 hidden h-[30%] w-full sm:block" viewBox="0 0 1600 300" preserveAspectRatio="xMidYMax meet"
         data-parallax="0.22">
        <g fill="#170d07" fill-opacity=".92">
            {{-- سوله‌های خط تولید --}}
            <path d="M120 300V150l120-46 120 46v150z"/>
            <path d="M360 300V168l150-40 150 40v132z"/>
            <path d="M660 300V186h300v114z"/>
            {{-- کوره تونلی --}}
            <rect x="980" y="206" width="420" height="94" rx="4"/>
            {{-- دودکش --}}
            <path d="M1240 206V56h44v150z"/>
            <path d="M1232 56h60v14h-60z"/>
        </g>
        {{-- دهانه‌ی کوره --}}
        <rect x="1008" y="240" width="46" height="44" rx="3" fill="#e2732f" fill-opacity=".85"/>
        <rect x="1008" y="240" width="46" height="44" rx="3" fill="#f5a45f" fill-opacity=".5"/>
        {{-- پنجره‌های روشن سوله --}}
        <g fill="#e2732f" fill-opacity=".38">
            <rect x="700" y="216" width="18" height="26" rx="2"/>
            <rect x="740" y="216" width="18" height="26" rx="2"/>
            <rect x="780" y="216" width="18" height="26" rx="2"/>
            <rect x="404" y="204" width="16" height="24" rx="2"/>
            <rect x="440" y="204" width="16" height="24" rx="2"/>
        </g>
    </svg>

    {{-- پالت‌های بلوک در پیش‌زمینه --}}
    <svg class="absolute bottom-0 left-0 hidden h-[26%] w-full sm:block" viewBox="0 0 1600 240" preserveAspectRatio="xMidYMax slice"
         data-parallax="0.34">
        <g fill="#0e0e0e">
            <rect x="60" y="120" width="230" height="120" rx="4"/>
            <rect x="330" y="86" width="210" height="154" rx="4"/>
            <rect x="1120" y="104" width="240" height="136" rx="4"/>
            <rect x="1390" y="140" width="180" height="100" rx="4"/>
        </g>
        {{-- بازتاب نور کوره روی لبه‌ی بالایی پالت‌ها --}}
        <g fill="#b4552d" fill-opacity=".34">
            <rect x="60" y="120" width="230" height="3"/>
            <rect x="330" y="86" width="210" height="3"/>
            <rect x="1120" y="104" width="240" height="3"/>
            <rect x="1390" y="140" width="180" height="3"/>
        </g>
    </svg>

    {{-- ذرات غبار خاک --}}
    <div class="absolute inset-0 opacity-[0.28]"
         style="background-image:
            radial-gradient(1.5px 1.5px at 18% 32%, #f5d0b4 50%, transparent),
            radial-gradient(1px 1px at 44% 58%, #f5d0b4 50%, transparent),
            radial-gradient(1.5px 1.5px at 68% 24%, #f5d0b4 50%, transparent),
            radial-gradient(1px 1px at 82% 66%, #f5d0b4 50%, transparent),
            radial-gradient(1px 1px at 30% 76%, #f5d0b4 50%, transparent);
            animation: dust-drift 24s linear infinite"></div>

    {{-- پرده‌ی ملایم پشت متن — روی هیروی کوتاه گوشی، خوانایی را تضمین می‌کند --}}
    <div class="absolute inset-0 bg-gradient-to-t from-ink-950/60 via-ink-950/25 to-transparent sm:hidden"></div>

    {{-- محو شدن به رنگ پس‌زمینه‌ی صفحه --}}
    <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-sand-100 to-transparent sm:h-32"></div>
</div>

@once
    @push('head')
        <style>
            @keyframes kiln-breathe {
                0%, 100% { opacity: .78; transform: translateX(-50%) scale(1); }
                50%      { opacity: 1;   transform: translateX(-50%) scale(1.07); }
            }
            @keyframes dust-drift {
                from { background-position: 0 0, 0 0, 0 0, 0 0, 0 0; }
                to   { background-position: 60px -90px, -50px -70px, 40px -110px, -70px -80px, 30px -100px; }
            }
            @media (prefers-reduced-motion: reduce) {
                [style*="kiln-breathe"], [style*="dust-drift"] { animation: none !important; }
            }
        </style>
    @endpush
@endonce
