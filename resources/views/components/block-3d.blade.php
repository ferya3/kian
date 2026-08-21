@props(['product', 'size' => 250, 'interactive' => true])

@php
    // نسبت‌ها از ابعاد واقعی محصول می‌آیند و اندازه‌ی نهایی با یک متغیر CSS
    // کنترل می‌شود: روی نمایشگر کوچک، مکعب کوچک می‌شود و صفحه را افقی نمی‌کشد.
    $max = max($product->length_mm, $product->width_mm, $product->height_mm);
    $rl = round($product->length_mm / $max, 4);
    $rw = round($product->width_mm / $max, 4);
    $rh = round($product->height_mm / $max, 4);

    $len = "calc(var(--bs) * $rl)";
    $wid = "calc(var(--bs) * $rw)";
    $hei = "calc(var(--bs) * $rh)";
    $halfW = 'calc(var(--bs) * '.round($rw / 2, 4).')';
    $halfL = 'calc(var(--bs) * '.round($rl / 2, 4).')';
    $halfH = 'calc(var(--bs) * '.round($rh / 2, 4).')';

    $pattern = $product->voidPattern();
    $box = "width: $len; height: $hei";
@endphp

{{-- ارتفاع حداقلی لازم است چون چرخش سه‌بعدی از کادر اصلی بیرون می‌زند --}}
<div class="grid place-items-center"
     style="--bs: min({{ $size }}px, 62vw); perspective: 1400px; perspective-origin: 50% 45%; min-height: calc(var(--bs) * 0.95)">
    <div class="relative transition-transform duration-100 ease-linear
                {{ $interactive ? 'cursor-grab touch-none active:cursor-grabbing' : '' }}"
         style="transform-style: preserve-3d; {{ $box }}; transform: rotateX(-14deg) rotateY(-24deg)"
         @if($interactive)
             :style="`transform-style: preserve-3d; {{ $box }}; transform: ${transform}`"
             @pointerdown="pointerDown($event)"
             @pointermove="pointerMove($event)"
             @pointerup="pointerUp()"
             @pointercancel="pointerUp()"
             @keydown.arrow-right.prevent="nudge('y', -8)"
             @keydown.arrow-left.prevent="nudge('y', 8)"
             @keydown.arrow-up.prevent="nudge('x', -8)"
             @keydown.arrow-down.prevent="nudge('x', 8)"
             tabindex="0"
             role="img"
             aria-label="نمای سه‌بعدی {{ $product->name }} با ابعاد {{ $product->dimensionLabel() }} سانتی‌متر — با کلیدهای جهت بچرخانید"
         @endif>

        {{-- وجه جلو --}}
        <div class="absolute inset-0 overflow-hidden rounded-[3px]"
             style="transform: translateZ({{ $halfW }}); background: linear-gradient(155deg, #c87755, #a34a26 55%, #8c3f20)">
            <div class="absolute inset-0 opacity-40"
                 style="background-image: repeating-linear-gradient(90deg, rgba(0,0,0,.16) 0 1px, transparent 1px calc(var(--bs) * {{ round($rl / $pattern['cols'], 4) }}))"></div>
            <div class="absolute inset-x-0 top-0 h-px bg-white/25"></div>
        </div>

        {{-- وجه پشت --}}
        <div class="absolute inset-0 rounded-[3px]"
             style="transform: translateZ(calc({{ $halfW }} * -1)) rotateY(180deg); background: linear-gradient(155deg, #8c3f20, #6d3119)"></div>

        {{-- وجه راست (سر بلوک — درز نر و ماده) --}}
        <div class="absolute overflow-hidden rounded-[3px]"
             style="width: {{ $wid }}; height: {{ $hei }}; left: 50%; top: 50%;
                    margin-left: calc({{ $halfW }} * -1); margin-top: calc({{ $halfH }} * -1);
                    transform: rotateY(90deg) translateZ({{ $halfL }});
                    background: linear-gradient(180deg, #b06248, #7c3a1f)">
            <div class="absolute inset-y-0 right-1/3 w-[14%] bg-black/25"></div>
            <div class="absolute inset-y-0 left-1/3 w-[14%] bg-white/10"></div>
        </div>

        {{-- وجه چپ --}}
        <div class="absolute rounded-[3px]"
             style="width: {{ $wid }}; height: {{ $hei }}; left: 50%; top: 50%;
                    margin-left: calc({{ $halfW }} * -1); margin-top: calc({{ $halfH }} * -1);
                    transform: rotateY(-90deg) translateZ({{ $halfL }});
                    background: linear-gradient(180deg, #94472a, #6d3119)"></div>

        {{-- وجه بالا — حفره‌های عمودی واقعی محصول --}}
        <div class="absolute overflow-hidden rounded-[3px]"
             style="width: {{ $len }}; height: {{ $wid }}; left: 50%; top: 50%;
                    margin-left: calc({{ $halfL }} * -1); margin-top: calc({{ $halfW }} * -1);
                    transform: rotateX(90deg) translateZ({{ $halfH }});
                    background: linear-gradient(140deg, #d98f6c, #b4552d)">
            <div class="grid h-full w-full gap-[7%] p-[7%]"
                 style="grid-template-columns: repeat({{ $pattern['cols'] }}, 1fr); grid-template-rows: repeat({{ $pattern['rows'] }}, 1fr)">
                @for($i = 0; $i < $pattern['cols'] * $pattern['rows']; $i++)
                    <span class="rounded-[1.5px] bg-ink-950/55 shadow-[inset_0_1px_2px_rgba(0,0,0,.6)]"></span>
                @endfor
            </div>
        </div>

        {{-- وجه پایین --}}
        <div class="absolute rounded-[3px]"
             style="width: {{ $len }}; height: {{ $wid }}; left: 50%; top: 50%;
                    margin-left: calc({{ $halfL }} * -1); margin-top: calc({{ $halfW }} * -1);
                    transform: rotateX(-90deg) translateZ({{ $halfH }});
                    background: #5c2815"></div>
    </div>
</div>
