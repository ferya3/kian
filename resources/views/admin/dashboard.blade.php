<x-layouts.admin title="نمای کلی" subtitle="{{ \App\Support\Jalali::format(now(), 'l، d MMMM y') }}">

    {{-- کارهای باقی‌مانده --}}
    @if($warnings)
        <div class="mb-6 space-y-2">
            @foreach($warnings as $warning)
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-sand-300 bg-sand-50 px-4 py-3">
                    <p class="flex items-center gap-2.5 text-[0.9375rem] text-ink-600">
                        <x-icon name="sparkle" size="17" class="shrink-0 text-clay-500" />
                        {{ $warning['text'] }}
                    </p>
                    <a href="{{ $warning['route'] }}" class="tap gap-1.5 text-meta font-semibold text-clay-600 hover:text-clay-700">
                        {{ $warning['cta'] }}
                        <x-icon name="arrow-left" size="14" />
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    {{-- شاخص‌ها --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-4">
        @foreach($tiles as $tile)
            <a href="{{ $tile['route'] }}"
               @class([
                   'group rounded-[var(--radius-panel)] border bg-sand-50 p-4 transition hover:-translate-y-0.5 hover:shadow-lift lg:p-5',
                   'border-clay-300 bg-clay-50' => $tile['urgent'] ?? false,
                   'border-sand-300' => ! ($tile['urgent'] ?? false),
               ])>
                <span class="flex items-center justify-between">
                    <x-icon :name="$tile['icon']" size="19" class="text-clay-500" />
                    <x-icon name="arrow-left" size="15" class="text-ink-300 transition group-hover:-translate-x-1 group-hover:text-clay-500" />
                </span>
                <span class="tech mt-3 block text-3xl font-extrabold text-ink-900">
                    {{ \App\Support\Jalali::digits($tile['value']) }}
                    @if($tile['total'] !== $tile['value'])
                        <span class="text-lg font-bold text-ink-300">/ {{ \App\Support\Jalali::digits($tile['total']) }}</span>
                    @endif
                </span>
                <span class="mt-1 block text-meta text-ink-500">{{ $tile['label'] }}</span>
            </a>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-12">

        {{-- آخرین درخواست‌ها --}}
        <section class="lg:col-span-7">
            <div class="flex items-center justify-between pb-3">
                <h2 class="text-lg font-extrabold">آخرین درخواست‌ها</h2>
                <a href="{{ route('admin.resource.index', 'messages') }}" class="tap -mx-2 px-2 text-meta font-semibold text-clay-600">همه</a>
            </div>

            <div class="overflow-hidden rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50">
                @forelse($messages as $message)
                    <a href="{{ \App\Admin\Resources\ContactMessageResource::editUrl($message) }}"
                       class="flex items-center gap-4 border-b border-sand-200 p-4 transition last:border-0 hover:bg-sand-100">
                        <span @class([
                            'mt-0.5 h-2 w-2 shrink-0 rounded-full',
                            'bg-clay-500' => $message->status === 'new',
                            'bg-sand-300' => $message->status !== 'new',
                        ])></span>
                        <span class="min-w-0 flex-1">
                            <span class="flex flex-wrap items-baseline gap-x-2">
                                <span class="font-semibold">{{ $message->name }}</span>
                                <span class="tech text-meta text-ink-400" dir="ltr">{{ $message->phone }}</span>
                            </span>
                            <span class="mt-0.5 block truncate text-meta text-ink-500">{{ $message->message }}</span>
                        </span>
                        <span class="tech shrink-0 text-micro text-ink-300">
                            {{ \App\Support\Jalali::format($message->created_at, 'd MMM') }}
                        </span>
                    </a>
                @empty
                    <p class="p-8 text-center text-ink-400">هنوز درخواستی ثبت نشده است.</p>
                @endforelse
            </div>
        </section>

        {{-- فعالیت اخیر --}}
        <section class="lg:col-span-5">
            <div class="flex items-center justify-between pb-3">
                <h2 class="text-lg font-extrabold">تغییرات اخیر</h2>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.activity') }}" class="tap -mx-2 px-2 text-meta font-semibold text-clay-600">گزارش کامل</a>
                @endif
            </div>

            <ol class="overflow-hidden rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50">
                @forelse($activity as $log)
                    <li class="flex items-start gap-3 border-b border-sand-200 p-4 last:border-0">
                        <span @class([
                            'mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full',
                            'bg-clay-500' => $log->event === 'created',
                            'bg-ink-300' => $log->event === 'updated',
                            'bg-red-400' => $log->event === 'deleted',
                        ])></span>
                        <span class="min-w-0 flex-1 text-[0.875rem] leading-relaxed">
                            <span class="font-semibold">{{ $log->user_name }}</span>
                            <span class="text-ink-400">{{ $log->eventLabel() }}:</span>
                            <span class="text-ink-600">{{ $log->subject_label }}</span>
                        </span>
                        <span class="tech shrink-0 text-micro text-ink-300">
                            {{ \App\Support\Jalali::format($log->created_at, 'd MMM') }}
                        </span>
                    </li>
                @empty
                    <li class="p-8 text-center text-ink-400">فعالیتی ثبت نشده است.</li>
                @endforelse
            </ol>
        </section>
    </div>
</x-layouts.admin>
