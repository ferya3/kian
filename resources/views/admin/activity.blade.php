<x-layouts.admin title="گزارش فعالیت" subtitle="{{ \App\Support\Jalali::digits($logs->total()) }} رویداد ثبت‌شده">

    <form method="GET" class="mb-5 flex flex-wrap items-center gap-2">
        <div class="relative min-w-0 flex-1 sm:max-w-xs">
            <label for="q" class="sr-only">جستجو</label>
            <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="نام کاربر یا عنوان رکورد…"
                   class="w-full rounded-xl border border-sand-300 bg-sand-50 py-2.5 pr-11 pl-4 outline-none focus:border-clay-400">
            <x-icon name="search" size="18" class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-ink-300" />
        </div>

        <div class="flex flex-wrap gap-1.5">
            <a href="{{ route('admin.activity') }}"
               @class(['tap rounded-full px-3.5 text-meta font-semibold transition',
                       'bg-ink-900 text-sand-50' => ! request('event'),
                       'bg-sand-200 text-ink-600 hover:bg-sand-300' => request('event')])>همه</a>
            @foreach($events as $key => $label)
                <a href="{{ request()->fullUrlWithQuery(['event' => $key]) }}"
                   @class(['tap rounded-full px-3.5 text-meta font-semibold transition',
                           'bg-ink-900 text-sand-50' => request('event') === $key,
                           'bg-sand-200 text-ink-600 hover:bg-sand-300' => request('event') !== $key])>{{ $label }}</a>
            @endforeach
        </div>
    </form>

    <div class="overflow-x-auto rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50">
        <table class="w-full min-w-[42rem] text-right">
            <thead class="bg-sand-200/70">
                <tr>
                    <th scope="col" class="px-4 py-3 text-meta font-bold text-ink-600">زمان</th>
                    <th scope="col" class="px-4 py-3 text-meta font-bold text-ink-600">کاربر</th>
                    <th scope="col" class="px-4 py-3 text-meta font-bold text-ink-600">رویداد</th>
                    <th scope="col" class="px-4 py-3 text-meta font-bold text-ink-600">رکورد</th>
                    <th scope="col" class="px-4 py-3 text-meta font-bold text-ink-600">فیلدهای تغییریافته</th>
                    <th scope="col" class="px-4 py-3 text-meta font-bold text-ink-600">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sand-200">
                @forelse($logs as $log)
                    <tr class="transition hover:bg-sand-100">
                        <td class="tech whitespace-nowrap px-4 py-3 text-meta text-ink-500">
                            {{ \App\Support\Jalali::format($log->created_at, 'd MMM y — HH:mm') }}
                        </td>
                        <td class="px-4 py-3 font-semibold">{{ $log->user_name }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'rounded-full px-2.5 py-1 text-micro font-semibold',
                                'bg-clay-100 text-clay-700' => $log->event === 'created',
                                'bg-sand-200 text-ink-600' => in_array($log->event, ['updated', 'login', 'logout']),
                                'bg-red-100 text-red-700' => in_array($log->event, ['deleted', 'login_failed']),
                            ])>{{ $log->eventLabel() }}</span>
                        </td>
                        <td class="px-4 py-3 text-ink-600">{{ $log->subject_label ?: '—' }}</td>
                        <td class="px-4 py-3 text-meta text-ink-400">
                            {{ $log->changes ? implode('، ', $log->changes) : '—' }}
                        </td>
                        <td class="tech px-4 py-3 text-meta text-ink-400" dir="ltr">{{ $log->ip ?: '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-10 text-center text-ink-400">رویدادی ثبت نشده است.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $logs->links() }}</div>
</x-layouts.admin>
