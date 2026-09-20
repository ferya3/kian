@php
    use App\Support\Locales;

    /*
    | $tone یک عبارتِ Alpine است و نه یک کلاس: هدر روی هیرو شفاف می‌شود و
    | رنگ متنش با اسکرول عوض می‌شود، پس رنگ دکمه باید به همان حالت وصل باشد.
    | جاهایی که چنین حالتی ندارند (منوی موبایل) مقدار پیش‌فرض را می‌گیرند.
    */
    $tone ??= "'text-ink-700 hover:bg-sand-200'";
@endphp

{{--
    سوئیچر زبان.

    فهرست از config/locales.php می‌آید، پس زبان تازه خودبه‌خود اینجا پیدا
    می‌شود. هر پیوند به «همین صفحه» به زبان دیگر می‌رود و نه به خانه —
    پارامترهای مسیر و کوئری سرِ جایشان می‌مانند.

    با یک زبانِ روشن اصلاً رندر نمی‌شود؛ دکمه‌ای که فقط یک گزینه دارد،
    دکمه نیست.
--}}
@if(Locales::all()->count() > 1)
    <div class="relative shrink-0" x-data="{ langOpen: false }" @keydown.escape="langOpen = false">
        <button type="button"
                @click="langOpen = ! langOpen"
                @click.outside="langOpen = false"
                :aria-expanded="langOpen ? 'true' : 'false'"
                aria-haspopup="menu"
                aria-label="{{ __('site.language.switch') }}"
                class="tech flex min-h-11 items-center gap-1 rounded-full px-3 text-micro font-bold uppercase tracking-[0.1em] transition lg:min-h-9"
                :class="{{ $tone }}">
            {{ Locales::meta()['short'] ?? strtoupper(Locales::current()) }}
            <x-icon name="chevron-down" size="14" class="transition-transform" ::class="langOpen && 'rotate-180'" />
        </button>

        <div x-show="langOpen" x-transition.opacity.duration.150ms
             role="menu"
             style="display: none; inset-inline-end: 0"
             class="absolute z-50 mt-2 min-w-[9.5rem] overflow-hidden rounded-2xl border border-sand-300 bg-sand-50 py-1 shadow-float">
            @foreach(Locales::all() as $code => $meta)
                @php $isCurrent = $code === Locales::current(); @endphp
                <a href="{{ Locales::urlFor($code) }}"
                   role="menuitem"
                   hreflang="{{ $meta['html'] }}"
                   lang="{{ $meta['html'] }}"
                   dir="{{ $meta['dir'] }}"
                   @if($isCurrent) aria-current="true" @endif
                   class="flex min-h-11 items-center justify-between gap-3 px-4 text-[0.9375rem] transition {{ $isCurrent ? 'bg-sand-200 font-bold text-ink-900' : 'text-ink-600 hover:bg-sand-100 hover:text-ink-900' }}">
                    <span>{{ $meta['name'] }}</span>
                    <span class="tech text-micro text-ink-400">{{ $meta['short'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endif
