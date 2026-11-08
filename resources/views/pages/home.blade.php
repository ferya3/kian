<x-layouts.app>
    @include('partials.home.hero')

    {{--
        نوار شاخص‌ها فعلاً غیرفعال است تا بعداً جای دیگری بنشیند. پارشال و
        داده‌اش (stats در HomeController) سر جایشان‌اند؛ برگرداندنش یک خط است.
    --}}
    {{-- @include('partials.home.stats') --}}

    @include('partials.home.finder')

    {{--
        بخش‌های زیرِ صفحه تا نزدیک‌شدنِ اسکرول رندر نمی‌شوند (defer-render).

        صفحه‌ی اصلی ۲۵۰۰ عنصر دارد و بیشترشان پایینِ صفحه‌اند؛ گوشی پیش از
        نخستین نقاشی همه را چیدمان می‌کرد — روی پردازنده‌ی متوسط ۱٫۳ ثانیه.
        حالا فقط آنچه دیده می‌شود. هر بخش در یک پوسته‌ی جدا، تا مرورگر هر کدام
        را جدا رد کند.
    --}}
    @foreach(['products', 'why-ceramic', 'process', 'factory', 'solutions', 'projects', 'technical'] as $part)
        <div class="defer-render" data-alpine-lazy>@include('partials.home.'.$part)</div>
    @endforeach

    {{--
        آخرین بخش رندرِ تنبل نمی‌گیرد: کوچک است، و ابزارِ سنجشِ دسترس‌پذیری
        (axe در Lighthouse) رنگِ پس‌زمینه‌ی متنِ رندرنشده را از قابِ تیره‌ی
        بیرونی می‌خواند و عددهای نارنجی‌اش را «کم‌کنتراست» گزارش می‌کرد.
    --}}
    @include('partials.home.sustainability')
</x-layouts.app>
