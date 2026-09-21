<section class="bg-sand-50 section-lg" aria-labelledby="products-heading">
    <div class="container-page">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <x-section-heading
                eyebrow="Product system"
                :title="__('site.home.products.title')"
                :lead="__('site.home.products.lead')"
                id="products-heading" class="lg:max-w-2xl" />

            <div data-reveal>
                <x-cta :href="route('products.index')" variant="ghost">{{ __('site.home.products.all') }}</x-cta>
            </div>
        </div>

        {{--
            یک فهرست، دو چیدمان.
            پایه (موبایل) ریل افقی قابل swipe است؛ از md به بالا با همان مارک‌آپ
            به شبکه تبدیل می‌شود. مارک‌آپ تکراری یعنی دو نسخه که از هم عقب می‌مانند.
        --}}
        <ul class="scroll-rail mt-10 md:mx-0 md:mt-12 md:grid md:snap-none md:grid-cols-2 md:gap-6 md:overflow-visible md:px-0 xl:grid-cols-4"
            data-reveal-stagger="110"
            aria-label="{{ __('site.home.products.list') }}">
            @foreach($featuredProducts as $product)
                <li class="w-[78vw] max-w-xs md:w-auto md:max-w-none">
                    <x-product-card :product="$product" data-reveal class="h-full" />
                </li>
            @endforeach
        </ul>

        <p class="mt-4 flex items-center gap-2 text-meta text-ink-400 md:hidden">
            <x-icon name="arrow-right" size="15" />
            {{ __('site.home.swipe') }}
        </p>

    </div>
</section>
