@props([
    'eyebrow' => null,
    'title',
    'lead' => null,
    'variant' => 'light',   // light | dark
    'compact' => false,
    'image' => null,        // مسیر تصویرِ همین رکورد؛ اگر ندهید جایگاه صفحه خوانده می‌شود
    'imageAlt' => null,
])

@php
    use App\Models\SiteMedia;

    /*
    | جایگاه تصویر هر صفحه «hero.» + نام مسیر همان صفحه است. یعنی هیچ صفحه‌ای
    | لازم نیست کلید را اعلام کند: صفحه‌ی تازه با یک ردیف در SiteMedia::SLOTS
    | تصویرپذیر می‌شود و تا وقتی مدیر تصویری نگذاشته، همین طرح گرادیانی می‌ماند.
    */
    $mediaSlot = 'hero.'.(request()->route()?->getName() ?? '');
    $imageUrl = \App\Support\Media::url($image) ?? SiteMedia::url($mediaSlot);
    $alt = $imageAlt ?? SiteMedia::alt($mediaSlot);

    /*
    | متن روی عکس فقط وقتی خواناست که زمینه تیره باشد. پس تصویر، نوع هیرو را
    | هم تعیین می‌کند — نه اینکه هر صفحه جداگانه یادش باشد variant را عوض کند.
    */
    $dark = $imageUrl !== null || $variant === 'dark';
@endphp

<section {{ $attributes->merge(['class' => 'relative overflow-hidden '.($dark ? 'bg-ink-950 text-sand-50' : 'bg-sand-50')]) }}>
    @if($imageUrl)
        <img src="{{ $imageUrl }}" alt="{{ $alt }}"
             fetchpriority="high" decoding="async"
             class="absolute inset-0 h-full w-full object-cover opacity-55">
        {{-- پرده‌ی تیره از پایین: تیتر و نان روی هر عکسی کنتراست کافی می‌گیرند --}}
        <div class="absolute inset-0 bg-gradient-to-t from-ink-950 via-ink-950/75 to-ink-950/45" aria-hidden="true"></div>
    @elseif($dark)
        <div class="pointer-events-none absolute -left-32 -top-32 h-[34rem] w-[34rem] rounded-full opacity-60 blur-[110px]"
             style="background: radial-gradient(circle, rgba(180,85,45,.4), transparent 65%)" aria-hidden="true"></div>
    @else
        <div class="pointer-events-none absolute inset-0 opacity-[0.55]" aria-hidden="true"
             style="background: radial-gradient(70% 60% at 88% 0%, rgba(180,85,45,.12), transparent 60%)"></div>
    @endif

    <div class="container-page relative {{ $compact ? 'py-12 lg:py-16' : 'py-16 lg:py-24' }}">
        <x-breadcrumbs class="{{ $dark ? '[&_*]:text-sand-200/60' : '' }}" />

        <div class="mt-6 max-w-4xl">
            @if($eyebrow)
                <p class="eyebrow {{ $dark ? 'text-clay-400' : 'text-clay-600' }}"><bdi dir="ltr">{{ $eyebrow }}</bdi></p>
            @endif

            <h1 class="mt-3 text-h1 font-extrabold text-balance">{{ $title }}</h1>

            @if($lead)
                <p class="mt-5 max-w-3xl text-lead {{ $dark ? 'text-sand-200/70' : 'text-ink-500' }}">{{ $lead }}</p>
            @endif
        </div>

        {{--
            محتوای اسلات در ویوِ صفحه نوشته شده و از تیره‌شدن هیرو خبر ندارد.
            دو خاکستریِ متنِ کمکی که روی زمینه‌ی روشن طراحی شده‌اند، روی عکس
            کنتراست کافی ندارند؛ فقط همان دو بازنگاشت می‌شوند تا رنگ‌های عمدیِ
            بقیه‌ی محتوا دست‌نخورده بماند.
        --}}
        <div class="{{ $dark ? '[&_.text-ink-400]:text-sand-200/70 [&_.bg-ink-300]:bg-sand-200/40' : '' }}">
            {{ $slot }}
        </div>
    </div>
</section>
