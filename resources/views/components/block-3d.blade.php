@props(['product', 'size' => 250, 'interactive' => true])

@php
    // نگاشت ابعاد واقعی محصول به پیکسل — نسبت‌ها واقعی می‌مانند.
    $max = max($product->length_mm, $product->width_mm, $product->height_mm);
    $l = round($product->length_mm / $max * $size, 1);
    $w = round($product->width_mm / $max * $size, 1);
    $h = round($product->height_mm / $max * $size, 1);
    $pattern = $product->voidPattern();
@endphp

{{-- ارتفاع حداقلی لازم است چون چرخش سه‌بعدی از کادر اصلی بیرون می‌زند --}}
<div class="grid place-items-center"
     style="perspective: 1400px; perspective-origin: 50% 45%; min-height: {{ round($size * 0.95) }}px">
    <div class="relative transition-transform duration-100 ease-linear
                {{ $interactive ? 'cursor-grab touch-none active:cursor-grabbing' : '' }}"
         style="transform-style: preserve-3d; width: {{ $l }}px; height: {{ $h }}px; transform: rotateX(-14deg) rotateY(-24deg)"
         @if($interactive)
             :style="`transform-style: preserve-3d; width: {{ $l }}px; height: {{ $h }}px; transform: ${transform}`"
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
             style="transform: translateZ({{ $w / 2 }}px); background: linear-gradient(155deg, #c87755, #a34a26 55%, #8c3f20)">
            <div class="absolute inset-0 opacity-40"
                 style="background-image: repeating-linear-gradient(90deg, rgba(0,0,0,.16) 0 1px, transparent 1px {{ $l / $pattern['cols'] }}px)"></div>
            <div class="absolute inset-x-0 top-0 h-px bg-white/25"></div>
        </div>

        {{-- وجه پشت --}}
        <div class="absolute inset-0 rounded-[3px]"
             style="transform: translateZ(-{{ $w / 2 }}px) rotateY(180deg); background: linear-gradient(155deg, #8c3f20, #6d3119)"></div>

        {{-- وجه راست (سر بلوک — درز نر و ماده) --}}
        <div class="absolute overflow-hidden rounded-[3px]"
             style="width: {{ $w }}px; height: {{ $h }}px; left: 50%; top: 50%;
                    margin-left: -{{ $w / 2 }}px; margin-top: -{{ $h / 2 }}px;
                    transform: rotateY(90deg) translateZ({{ $l / 2 }}px);
                    background: linear-gradient(180deg, #b06248, #7c3a1f)">
            <div class="absolute inset-y-0 right-1/3 w-[14%] bg-black/25"></div>
            <div class="absolute inset-y-0 left-1/3 w-[14%] bg-white/10"></div>
        </div>

        {{-- وجه چپ --}}
        <div class="absolute rounded-[3px]"
             style="width: {{ $w }}px; height: {{ $h }}px; left: 50%; top: 50%;
                    margin-left: -{{ $w / 2 }}px; margin-top: -{{ $h / 2 }}px;
                    transform: rotateY(-90deg) translateZ({{ $l / 2 }}px);
                    background: linear-gradient(180deg, #94472a, #6d3119)"></div>

        {{-- وجه بالا — حفره‌های عمودی واقعی محصول --}}
        <div class="absolute overflow-hidden rounded-[3px]"
             style="width: {{ $l }}px; height: {{ $w }}px; left: 50%; top: 50%;
                    margin-left: -{{ $l / 2 }}px; margin-top: -{{ $w / 2 }}px;
                    transform: rotateX(90deg) translateZ({{ $h / 2 }}px);
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
             style="width: {{ $l }}px; height: {{ $w }}px; left: 50%; top: 50%;
                    margin-left: -{{ $l / 2 }}px; margin-top: -{{ $w / 2 }}px;
                    transform: rotateX(-90deg) translateZ({{ $h / 2 }}px);
                    background: #5c2815"></div>
    </div>
</div>
