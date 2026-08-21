@props(['project'])

@php
    // تصویر مولد و قطعی برای هر پروژه — تا زمانی که عکس واقعی آپلود شود.
    // seed از slug ساخته می‌شود تا هر پروژه همیشه همان ترکیب را داشته باشد.
    $seed = crc32($project->slug);
    $rand = function (int $min, int $max) use (&$seed) {
        $seed = ($seed * 1103515245 + 12345) & 0x7fffffff;
        return $min + $seed % max(1, $max - $min + 1);
    };

    $palettes = [
        'residential'  => ['#b4552d', '#7c361c', '#ded7cb'],
        'commercial'   => ['#8c4a2b', '#2e2e2e', '#c4bbaa'],
        'industrial'   => ['#6d3119', '#454545', '#a3a3a3'],
        'mass-housing' => ['#c87755', '#5c2815', '#ece8e0'],
        'public'       => ['#e2732f', '#3d1b0e', '#ded7cb'],
    ];

    [$accent, $dark, $light] = $palettes[$project->category?->slug] ?? $palettes['residential'];
    $towers = $rand(4, 6);
@endphp

<svg {{ $attributes->merge(['class' => 'h-full w-full']) }} viewBox="0 0 800 600" preserveAspectRatio="xMidYMid slice"
     role="img" aria-label="{{ $project->title }} — {{ $project->city }}، {{ \App\Support\Jalali::digits($project->year) }}">
    <defs>
        <linearGradient id="sky-{{ $project->id }}" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="{{ $light }}"/>
            <stop offset="1" stop-color="#f5f3ef"/>
        </linearGradient>
        <linearGradient id="face-{{ $project->id }}" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="{{ $accent }}"/>
            <stop offset="1" stop-color="{{ $dark }}"/>
        </linearGradient>
    </defs>

    <rect width="800" height="600" fill="url(#sky-{{ $project->id }})"/>

    {{-- خورشید کم‌رمق --}}
    <circle cx="{{ $rand(140, 660) }}" cy="{{ $rand(80, 180) }}" r="54" fill="{{ $accent }}" fill-opacity=".12"/>

    {{-- بلوک‌های ساختمانی --}}
    @php $x = -40; @endphp
    @for($i = 0; $i < $towers; $i++)
        @php
            $w = $rand(110, 190);
            $h = $rand(210, 430);
            $y = 600 - $h;
            $cols = max(2, (int) floor($w / 42));
            $rows = max(3, (int) floor($h / 52));
        @endphp
        <g>
            <rect x="{{ $x }}" y="{{ $y }}" width="{{ $w }}" height="{{ $h }}"
                  fill="{{ $i % 2 === 0 ? 'url(#face-'.$project->id.')' : $dark }}"
                  fill-opacity="{{ $i % 2 === 0 ? '.92' : '.82' }}"/>

            {{-- بازشوها — ریتم ماژولار بلوک --}}
            <g fill="#f5f3ef" fill-opacity="{{ $i % 2 === 0 ? '.22' : '.14' }}">
                @for($r = 0; $r < $rows; $r++)
                    @for($c = 0; $c < $cols; $c++)
                        @if($rand(0, 10) > 2)
                            <rect x="{{ $x + 16 + $c * (($w - 32) / $cols) }}"
                                  y="{{ $y + 22 + $r * (($h - 40) / $rows) }}"
                                  width="{{ max(10, ($w - 32) / $cols - 14) }}"
                                  height="{{ max(14, ($h - 40) / $rows - 20) }}" rx="2"/>
                        @endif
                    @endfor
                @endfor
            </g>

            <rect x="{{ $x }}" y="{{ $y }}" width="{{ $w }}" height="4" fill="#f5f3ef" fill-opacity=".3"/>
        </g>
        @php $x += $w + $rand(6, 26); @endphp
    @endfor

    {{-- زمین --}}
    <rect y="576" width="800" height="24" fill="{{ $dark }}" fill-opacity=".9"/>
</svg>
