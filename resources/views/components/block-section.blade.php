@props(['product', 'hotspots' => true])

@php
    $pattern = $product->voidPattern();
    $vw = 400;                      // فضای مختصات SVG
    $vh = round($vw * $product->width_mm / $product->length_mm);
    $pad = 14;
    $gapX = 7; $gapY = 7;
    $cellW = ($vw - $pad * 2 - $gapX * ($pattern['cols'] - 1)) / $pattern['cols'];
    $cellH = ($vh - $pad * 2 - $gapY * ($pattern['rows'] - 1)) / $pattern['rows'];
@endphp

<div class="relative">
    <svg viewBox="0 0 {{ $vw }} {{ $vh }}" class="w-full" role="img"
         aria-label="مقطع افقی {{ $product->name }} — {{ $pattern['rows'] }} ردیف و {{ $pattern['cols'] }} ستون حفره">
        <defs>
            <linearGradient id="body-{{ $product->id }}" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0" stop-color="#d98f6c"/>
                <stop offset="0.55" stop-color="#b4552d"/>
                <stop offset="1" stop-color="#8c3f20"/>
            </linearGradient>
        </defs>

        {{-- بدنه --}}
        <rect x="1" y="1" width="{{ $vw - 2 }}" height="{{ $vh - 2 }}" rx="6" fill="url(#body-{{ $product->id }})"/>

        {{-- درز نر و ماده در دو سر بلوک --}}
        <path d="M1 {{ $vh * 0.32 }} h10 v{{ $vh * 0.36 }} h-10 z" fill="#00000022"/>
        <path d="M{{ $vw - 11 }} {{ $vh * 0.32 }} h10 v{{ $vh * 0.36 }} h-10 z" fill="#ffffff1f"/>

        {{-- حفره‌ها --}}
        @for($r = 0; $r < $pattern['rows']; $r++)
            @for($c = 0; $c < $pattern['cols']; $c++)
                {{-- ردیف‌های زوج نیم‌سلول جابه‌جا می‌شوند: همان آرایش زیگزاگ واقعی --}}
                @php $shift = $r % 2 === 1 ? $cellW * 0.18 : 0; @endphp
                <rect x="{{ round($pad + $c * ($cellW + $gapX) + $shift, 2) }}"
                      y="{{ round($pad + $r * ($cellH + $gapY), 2) }}"
                      width="{{ round($cellW, 2) }}" height="{{ round($cellH, 2) }}"
                      rx="2.5" fill="#2a1109" fill-opacity=".82"/>
            @endfor
        @endfor
    </svg>

    @if($hotspots && $product->cavities->isNotEmpty())
        @foreach($product->cavities as $index => $cavity)
            <button type="button"
                    @click="selectCavity({{ $index }})"
                    :aria-pressed="activeCavity === {{ $index }} ? 'true' : 'false'"
                    class="group absolute grid h-8 w-8 -translate-x-1/2 -translate-y-1/2 place-items-center rounded-full transition-transform duration-300 hover:scale-110 focus-visible:scale-110"
                    style="left: {{ $cavity->x }}%; top: {{ $cavity->y }}%"
                    aria-label="{{ $cavity->label }}">
                <span class="absolute inset-0 rounded-full bg-sand-50/25 backdrop-blur-[1px]"></span>
                <span class="absolute inset-0 animate-ping rounded-full bg-sand-50/40"
                      :class="activeCavity === {{ $index }} && 'hidden'"
                      style="animation-duration: 2.6s"></span>
                <span class="relative grid h-4 w-4 place-items-center rounded-full border-2 border-sand-50 transition-colors"
                      :class="activeCavity === {{ $index }} ? 'bg-sand-50' : 'bg-clay-600'"></span>
            </button>
        @endforeach
    @endif
</div>
