{{-- نمای ایزومتریک محوطه‌ی کارخانه — پایه‌ی نقشه‌ی تعاملی --}}
<svg {{ $attributes->merge(['class' => 'h-full w-full']) }} viewBox="0 0 1000 620" role="img"
     aria-label="نمای هوایی کارخانه: خط تولید، کوره تونلی، خشک‌کن، آزمایشگاه، انبار و بسته‌بندی">
    <defs>
        <linearGradient id="ground" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#242424"/><stop offset="1" stop-color="#101010"/>
        </linearGradient>
        <linearGradient id="roof" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="#565656"/><stop offset="1" stop-color="#333"/>
        </linearGradient>
        <linearGradient id="roof-hot" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#b4552d"/><stop offset="1" stop-color="#7c361c"/>
        </linearGradient>
    </defs>

    <rect width="1000" height="620" fill="url(#ground)"/>

    {{-- شبکه‌ی محوطه --}}
    <g stroke="#ffffff" stroke-opacity=".045" stroke-width="1">
        @for($i = 0; $i <= 20; $i++)
            <path d="M{{ $i * 50 }} 0 L{{ $i * 50 - 300 }} 620"/>
            <path d="M0 {{ $i * 31 }} L1000 {{ $i * 31 - 200 }}"/>
        @endfor
    </g>

    {{-- جاده‌ی محوطه --}}
    <path d="M60 470 L520 300 L960 430" stroke="#1a1a1a" stroke-width="26" fill="none" stroke-linecap="round"/>
    <path d="M60 470 L520 300 L960 430" stroke="#4f4f4f" stroke-width="1.5" stroke-dasharray="14 14" fill="none"/>

    {{-- انبار محصول --}}
    <g>
        <path d="M120 470 L260 404 L360 452 L220 518 Z" fill="url(#roof)"/>
        <path d="M120 470 L120 500 L220 548 L220 518 Z" fill="#2b2b2b"/>
        <path d="M220 518 L360 452 L360 482 L220 548 Z" fill="#1d1d1d"/>
    </g>

    {{-- خط تولید — دو سوله‌ی موازی --}}
    <g>
        <path d="M210 350 L400 262 L520 320 L330 408 Z" fill="url(#roof)"/>
        <path d="M210 350 L210 384 L330 442 L330 408 Z" fill="#2b2b2b"/>
        <path d="M330 408 L520 320 L520 354 L330 442 Z" fill="#1d1d1d"/>
        <g stroke="#e2732f" stroke-opacity=".35" stroke-width="2">
            <path d="M245 358 L430 272"/><path d="M268 369 L452 283"/><path d="M291 380 L475 294"/>
        </g>
    </g>

    {{-- خشک‌کن --}}
    <g>
        <path d="M330 452 L470 386 L560 430 L420 496 Z" fill="url(#roof)"/>
        <path d="M330 452 L330 480 L420 524 L420 496 Z" fill="#2b2b2b"/>
        <path d="M420 496 L560 430 L560 458 L420 524 Z" fill="#1d1d1d"/>
    </g>

    {{-- کوره تونلی — قلب کارخانه --}}
    <g>
        <path d="M470 300 L760 164 L860 212 L570 348 Z" fill="url(#roof-hot)" fill-opacity=".9"/>
        <path d="M470 300 L470 336 L570 384 L570 348 Z" fill="#5c2815"/>
        <path d="M570 348 L860 212 L860 248 L570 384 Z" fill="#3d1b0e"/>
        {{-- دهانه‌ی روشن کوره --}}
        <path d="M480 306 L508 292 L508 322 L480 336 Z" fill="#f0a05c"/>
        <path d="M480 306 L508 292 L508 322 L480 336 Z" fill="#ffffff" fill-opacity=".25"/>
        {{-- دودکش --}}
        <path d="M700 190 L724 179 L724 96 L700 107 Z" fill="#4a4a4a"/>
        <path d="M724 179 L748 190 L748 107 L724 96 Z" fill="#2b2b2b"/>
    </g>

    {{-- آزمایشگاه --}}
    <g>
        <path d="M640 388 L740 342 L806 374 L706 420 Z" fill="url(#roof)"/>
        <path d="M640 388 L640 412 L706 444 L706 420 Z" fill="#2b2b2b"/>
        <path d="M706 420 L806 374 L806 398 L706 444 Z" fill="#1d1d1d"/>
        <g fill="#e2732f" fill-opacity=".4">
            <path d="M652 398 L668 390 L668 406 L652 414 Z"/>
            <path d="M676 387 L692 379 L692 395 L676 403 Z"/>
        </g>
    </g>

    {{-- بسته‌بندی --}}
    <g>
        <path d="M540 486 L660 430 L744 470 L624 526 Z" fill="url(#roof)"/>
        <path d="M540 486 L540 514 L624 554 L624 526 Z" fill="#2b2b2b"/>
        <path d="M624 526 L744 470 L744 498 L624 554 Z" fill="#1d1d1d"/>
    </g>

    {{-- پالت‌های آماده‌ی بارگیری --}}
    <g fill="#b4552d" fill-opacity=".55">
        @foreach([[790, 470], [830, 490], [870, 452], [750, 496]] as [$x, $y])
            <path d="M{{ $x }} {{ $y }} l24 -12 l18 9 l-24 12 Z"/>
            <path d="M{{ $x }} {{ $y }} l0 14 l18 9 l0 -14 Z" fill-opacity=".8"/>
        @endforeach
    </g>

    {{-- نور کوره روی لبه‌ی بالایی سوله‌ها — ساختمان‌ها را از زمین جدا می‌کند --}}
    <g stroke="#b4552d" stroke-opacity=".45" stroke-width="2.5" fill="none" stroke-linecap="round">
        <path d="M210 350 L400 262"/>
        <path d="M330 452 L470 386"/>
        <path d="M120 470 L260 404"/>
        <path d="M640 388 L740 342"/>
        <path d="M540 486 L660 430"/>
    </g>

    {{-- دپوی خاک رس --}}
    <path d="M60 380 q70 -58 150 -14 q-70 46 -150 14 Z" fill="#5c2815" fill-opacity=".8"/>
    <path d="M80 400 q60 -46 128 -12 q-62 40 -128 12 Z" fill="#3d1b0e"/>
</svg>
