<x-layouts.app>
    @include('partials.home.hero')

    {{--
        نوار شاخص‌ها فعلاً غیرفعال است تا بعداً جای دیگری بنشیند. پارشال و
        داده‌اش (stats در HomeController) سر جایشان‌اند؛ برگرداندنش یک خط است.
    --}}
    {{-- @include('partials.home.stats') --}}

    @include('partials.home.finder')
    @include('partials.home.products')
    @include('partials.home.why-ceramic')
    @include('partials.home.process')
    @include('partials.home.factory')
    @include('partials.home.solutions')
    @include('partials.home.projects')
    @include('partials.home.technical')
    @include('partials.home.sustainability')
</x-layouts.app>
