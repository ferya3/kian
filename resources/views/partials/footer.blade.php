@php use App\Support\Brand; @endphp

<footer class="no-print mt-24 bg-ink-950 text-sand-200">
    {{-- نوار فراخوان پیش از فوتر --}}
    <div class="border-b border-white/[0.07]">
        <div class="container-page grid grid-cols-1 gap-8 section-sm lg:grid-cols-12 lg:items-center">
            <div class="lg:col-span-7">
                <p class="eyebrow text-clay-400">{{ __('site.footer.eyebrow') }}</p>
                <h2 class="mt-3 text-h2 font-extrabold text-sand-50">
                    {{ __('site.footer.title') }}
                </h2>
                <p class="mt-4 max-w-2xl text-[1.0625rem] leading-relaxed text-sand-200/65">
                    {{ __('site.footer.lead') }}
                </p>
            </div>
            <div class="flex flex-wrap gap-3 lg:col-span-5 lg:justify-end">
                <a href="{{ route('contact') }}"
                   class="inline-flex items-center gap-2 rounded-full bg-clay-500 px-7 py-3.5 font-semibold text-white transition hover:bg-clay-400">
                    {{ __('site.actions.consult') }}
                    <x-icon name="arrow-left" size="18" />
                </a>
                <a href="tel:{{ config('kian.contact.phone_raw') }}"
                   class="inline-flex items-center gap-2 rounded-full border border-white/15 px-7 py-3.5 font-semibold text-sand-100 transition hover:border-white/40">
                    <x-icon name="phone" size="18" />
                    <span class="tech">{{ Brand::phone() }}</span>
                </a>
            </div>
        </div>
    </div>

    <div class="container-page grid grid-cols-1 gap-10 section-sm md:grid-cols-2 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <x-brand-mark class="h-11 w-11" />
                <span>
                    <span class="block text-lg font-extrabold text-sand-50">{{ Brand::legalName() }}</span>
                    <span class="tech block text-micro uppercase tracking-[0.18em] text-sand-200/45">{{ config('kian.brand.descriptor_en') }}</span>
                </span>
            </a>
            <p class="mt-5 max-w-sm text-[0.9375rem] leading-relaxed text-sand-200/55">
                {{ __('site.footer.tagline', ['tagline' => Brand::tagline(), 'year' => Brand::founded()]) }}
            </p>

            <ul class="mt-6 space-y-3 text-[0.9375rem] text-sand-200/70">
                <li class="flex gap-3">
                    <x-icon name="pin" size="18" class="mt-1 shrink-0 text-clay-400" />
                    <span>{{ Brand::address() }}</span>
                </li>
                <li class="flex gap-3">
                    <x-icon name="mail" size="18" class="mt-0.5 shrink-0 text-clay-400" />
                    <a href="mailto:{{ config('kian.contact.email') }}" class="tech tap transition hover:text-sand-50">{{ config('kian.contact.email') }}</a>
                </li>
            </ul>
        </div>

        <nav class="lg:col-span-8" aria-label="{{ __('site.nav.footer') }}">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-sand-50">{{ __('site.footer.products') }}</h3>
                    <ul class="-my-1 text-[0.9375rem] text-sand-200/60 lg:my-0 lg:space-y-2.5">
                        @foreach($megaMenu->flatMap->children->take(6) as $child)
                            <li><a href="{{ route('products.index', ['category' => $child->slug]) }}" class="tap w-full transition hover:text-clay-300 lg:w-auto">{{ $child->name }}</a></li>
                        @endforeach
                        <li><a href="{{ route('finder.show') }}" class="tap w-full font-semibold text-clay-400 transition hover:text-clay-300 lg:w-auto">{{ __('site.footer.finder') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-sand-50">{{ __('site.footer.technical') }}</h3>
                    <ul class="-my-1 text-[0.9375rem] text-sand-200/60 lg:my-0 lg:space-y-2.5">
                        <li><a href="{{ route('technical.downloads') }}" class="tap w-full transition hover:text-clay-300 lg:w-auto">{{ __('site.footer.datasheets') }}</a></li>
                        <li><a href="{{ route('technical.downloads', ['category' => 'cad']) }}" class="tap w-full transition hover:text-clay-300 lg:w-auto">{{ __('site.footer.cad') }}</a></li>
                        <li><a href="{{ route('technical.downloads', ['category' => 'bim']) }}" class="tap w-full transition hover:text-clay-300 lg:w-auto">{{ __('site.footer.bim') }}</a></li>
                        <li><a href="{{ route('technical.installation') }}" class="tap w-full transition hover:text-clay-300 lg:w-auto">{{ __('site.footer.installation') }}</a></li>
                        <li><a href="{{ route('technical.certificates') }}" class="tap w-full transition hover:text-clay-300 lg:w-auto">{{ __('site.footer.certificates') }}</a></li>
                        <li><a href="{{ route('technical.faq') }}" class="tap w-full transition hover:text-clay-300 lg:w-auto">{{ __('site.footer.faq') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-sand-50">{{ __('site.footer.company') }}</h3>
                    <ul class="-my-1 text-[0.9375rem] text-sand-200/60 lg:my-0 lg:space-y-2.5">
                        <li><a href="{{ route('about') }}" class="tap w-full transition hover:text-clay-300 lg:w-auto">{{ __('site.footer.about') }}</a></li>
                        <li><a href="{{ route('factory') }}" class="tap w-full transition hover:text-clay-300 lg:w-auto">{{ __('site.footer.factory') }}</a></li>
                        <li><a href="{{ route('technology') }}" class="tap w-full transition hover:text-clay-300 lg:w-auto">{{ __('site.footer.technology') }}</a></li>
                        <li><a href="{{ route('sustainability') }}" class="tap w-full transition hover:text-clay-300 lg:w-auto">{{ __('site.footer.sustainability') }}</a></li>
                        <li><a href="{{ route('projects.index') }}" class="tap w-full transition hover:text-clay-300 lg:w-auto">{{ __('site.footer.projects') }}</a></li>
                        <li><a href="{{ route('articles.index') }}" class="tap w-full transition hover:text-clay-300 lg:w-auto">{{ __('site.footer.articles') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-sand-50">{{ __('site.footer.sales') }}</h3>
                    <ul class="-my-1 text-[0.9375rem] text-sand-200/60 lg:my-0 lg:space-y-2.5">
                        <li><a href="{{ route('contact') }}" class="tap w-full transition hover:text-clay-300 lg:w-auto">{{ __('site.footer.quote') }}</a></li>
                        <li><a href="{{ route('distributors') }}" class="tap w-full transition hover:text-clay-300 lg:w-auto">{{ __('site.footer.distributors') }}</a></li>
                        <li><a href="{{ route('contact', ['type' => 'distributor']) }}" class="tap w-full transition hover:text-clay-300 lg:w-auto">{{ __('site.footer.become_distributor') }}</a></li>
                        <li><a href="{{ route('contact', ['type' => 'technical']) }}" class="tap w-full transition hover:text-clay-300 lg:w-auto">{{ __('site.footer.support') }}</a></li>
                    </ul>

                    <div class="mt-6 flex gap-2">
                        @foreach(['instagram' => 'IG', 'linkedin' => 'in', 'aparat' => 'AP', 'telegram' => 'TG'] as $key => $label)
                            <a href="{{ config("kian.social.$key") }}" rel="noopener noreferrer" target="_blank"
                               class="tech tap-icon rounded-full border border-white/12 text-micro font-semibold text-sand-200/70 transition hover:border-clay-400 hover:text-clay-300 lg:min-h-9 lg:min-w-9"
                               aria-label="{{ $key }}">{{ $label }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <div class="border-t border-white/[0.07]">
        <div class="container-page flex flex-col gap-3 pt-6 text-meta text-sand-200/40 sm:flex-row sm:items-center sm:justify-between"
             style="padding-bottom: max(1.5rem, var(--safe-bottom))">
            <p>© {{ \App\Support\Jalali::year() }} {{ Brand::legalName() }} — {{ __('site.footer.rights') }}</p>
            {{--
                نشان‌واژه‌ی لاتین، فقط وقتی حرفِ تازه‌ای می‌زند.
                در نسخه‌ی انگلیسی همان جمله‌ی بالای فوتر است و دو بار نوشتنش
                خطِ دومِ امضا را به تکرار بدل می‌کند.
            --}}
            @if(Brand::tagline() !== config('kian.brand.tagline_en'))
                <p class="tech"><bdi dir="ltr">{{ config('kian.brand.tagline_en') }}</bdi></p>
            @endif
        </div>
    </div>
</footer>
