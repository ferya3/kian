<section class="bg-sand-50 py-20 lg:py-28" aria-labelledby="products-heading">
    <div class="container-page">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <x-section-heading
                eyebrow="Product system"
                title="یک خانواده، ابعاد هماهنگ"
                lead="محصولات ما به‌صورت ماژولار طراحی شده‌اند: بلوک تیغه‌ای، دیواری و عایق در یک رگ‌چینی با هم می‌خوانند و برش اضافه لازم نمی‌شود."
                id="products-heading" class="lg:max-w-2xl" />

            <div data-reveal>
                <x-cta :href="route('products.index')" variant="ghost">همه محصولات</x-cta>
            </div>
        </div>

        {{-- دسکتاپ: شبکه — موبایل: ریل افقی قابل swipe --}}
        <div class="mt-12 hidden gap-6 md:grid md:grid-cols-2 xl:grid-cols-4" data-reveal-stagger="110">
            @foreach($featuredProducts as $product)
                <x-product-card :product="$product" data-reveal />
            @endforeach
        </div>

        <div class="scroll-rail mt-10 md:hidden" role="list" aria-label="محصولات پرکاربرد">
            @foreach($featuredProducts as $product)
                <x-product-card :product="$product" role="listitem" class="w-[80vw] max-w-xs" />
            @endforeach
        </div>

        <p class="mt-4 flex items-center gap-2 text-[0.8125rem] text-ink-400 md:hidden">
            <x-icon name="arrow-right" size="15" />
            برای دیدن بقیه، بکشید
        </p>
    </div>
</section>
