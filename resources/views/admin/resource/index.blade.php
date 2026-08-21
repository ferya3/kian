@php
    use App\Support\Admin\Field;

    /** @var array<int, Field> $fields */
    $fields = $resource::listFields();
    $primary = $fields[0] ?? null;
    $secondary = array_slice($fields, 1);
    $sortables = array_values(array_filter($fields, fn (Field $f) => $f->sortable));
@endphp

<x-layouts.admin :title="$resource::$label"
                 subtitle="{{ \App\Support\Jalali::digits($records->total()) }} مورد">

    <x-slot:actions>
        @if($resource::$creatable)
            <a href="{{ route('admin.resource.create', $resource::$slug) }}"
               class="tap gap-2 rounded-xl bg-clay-500 px-4 font-semibold text-white transition hover:bg-clay-600">
                <x-icon name="plus" size="17" />
                <span class="hidden sm:inline">{{ $resource::$singular }} جدید</span>
                <span class="sr-only sm:hidden">{{ $resource::$singular }} جدید</span>
            </a>
        @endif
    </x-slot:actions>

    {{-- جستجو و مرتب‌سازی --}}
    <form method="GET" class="mb-5 flex flex-wrap items-center gap-2">
        <div class="relative w-full min-w-0 sm:w-auto sm:flex-1 sm:max-w-sm">
            <label for="q" class="sr-only">جستجو در {{ $resource::$label }}</label>
            <input id="q" name="q" type="search" value="{{ $term }}"
                   placeholder="جستجو در {{ $resource::$label }}…"
                   class="w-full rounded-xl border border-sand-300 bg-sand-50 py-2.5 pr-11 pl-4 outline-none transition focus:border-clay-400">
            <x-icon name="search" size="18" class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-ink-300" />
        </div>

        {{--
            روی دسکتاپ سرستون‌ها مرتب‌سازی را انجام می‌دهند؛ در کارت‌های موبایل
            سرستونی وجود ندارد، پس همان امکان با یک select بومی برمی‌گردد
            (انتخابگر سیستمی، بدون جاوااسکریپت اضافه).
        --}}
        @if($sortables)
            <label for="sort" class="sr-only">ترتیب نمایش</label>
            <select id="sort" x-on:change="location = $el.value"
                    class="rounded-xl border border-sand-300 bg-sand-50 px-3 py-2.5 outline-none transition focus:border-clay-400 md:hidden">
                @foreach($sortables as $field)
                    @foreach(['asc' => 'صعودی', 'desc' => 'نزولی'] as $dir => $dirLabel)
                        <option value="{{ request()->fullUrlWithQuery(['sort' => $field->key, 'dir' => $dir]) }}"
                                @selected($sort === $field->key && $direction === $dir)>
                            {{ $field->label }} — {{ $dirLabel }}
                        </option>
                    @endforeach
                @endforeach
            </select>
        @endif

        @if($term)
            <a href="{{ route('admin.resource.index', $resource::$slug) }}"
               class="tap px-3 text-meta text-ink-400 hover:text-ink-900">حذف جستجو</a>
        @endif
    </form>

    @if($records->isEmpty())
        <div class="rounded-[var(--radius-panel)] border border-dashed border-sand-300 px-4 py-16 text-center">
            <p class="text-lg font-bold">{{ $term ? 'چیزی پیدا نشد' : 'هنوز موردی ثبت نشده' }}</p>
            <p class="mt-2 text-ink-400">
                {{ $term ? 'عبارت دیگری را امتحان کنید.' : 'اولین '.$resource::$singular.' را بسازید.' }}
            </p>
            @if($resource::$creatable && ! $term)
                <a href="{{ route('admin.resource.create', $resource::$slug) }}"
                   class="tap mt-6 gap-2 rounded-xl bg-clay-500 px-5 font-semibold text-white">
                    <x-icon name="plus" size="17" />
                    {{ $resource::$singular }} جدید
                </a>
            @endif
        </div>
    @else
        {{--
            زیر md، جدول به کارت تبدیل می‌شود. جدولِ ۴۴rem داخل یک اسکرول افقی
            روی گوشی یعنی سلول‌های سه‌خطی و ستون «عملیات» بیرون از دید؛
            کارت همان داده را بدون اسکرول افقی و با هدف لمسی درست نشان می‌دهد.
        --}}
        <ul class="space-y-3 md:hidden">
            @foreach($records as $record)
                <li class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <a href="{{ $resource::editUrl($record) }}"
                           class="min-w-0 flex-1 self-center py-1 font-bold leading-snug transition hover:text-clay-600">
                            @if($primary)
                                @include('admin.resource.cell', ['field' => $primary, 'record' => $record])
                            @else
                                {{ $resource::titleFor($record) }}
                            @endif
                        </a>
                        @include('admin.resource.row-actions', ['resource' => $resource, 'record' => $record])
                    </div>

                    @if($secondary)
                        <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-2.5 border-t border-sand-200 pt-3">
                            @foreach($secondary as $field)
                                <div class="min-w-0">
                                    <dt class="text-micro text-ink-400">{{ $field->label }}</dt>
                                    <dd class="mt-0.5 break-words text-meta">
                                        @include('admin.resource.cell', ['field' => $field, 'record' => $record])
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    @endif
                </li>
            @endforeach
        </ul>

        <div class="hidden overflow-x-auto rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 md:block">
            <table class="w-full min-w-[44rem] text-right">
                <thead class="bg-sand-200/70">
                    <tr>
                        @foreach($fields as $field)
                            <th scope="col" class="px-4 py-3 text-meta font-bold text-ink-600">
                                @if($field->sortable)
                                    @php
                                        $isActive = $sort === $field->key;
                                        $nextDir = $isActive && $direction === 'asc' ? 'desc' : 'asc';
                                    @endphp
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => $field->key, 'dir' => $nextDir]) }}"
                                       class="inline-flex items-center gap-1 transition hover:text-clay-600">
                                        {{ $field->label }}
                                        <x-icon name="chevron-down" size="13"
                                                class="{{ $isActive ? ($direction === 'asc' ? 'rotate-180 text-clay-600' : 'text-clay-600') : 'text-ink-300' }}" />
                                    </a>
                                @else
                                    {{ $field->label }}
                                @endif
                            </th>
                        @endforeach
                        <th scope="col" class="w-px px-4 py-3"><span class="sr-only">عملیات</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sand-200">
                    @foreach($records as $record)
                        <tr class="transition hover:bg-sand-100">
                            @foreach($fields as $i => $field)
                                <td class="px-4 py-3 align-middle">
                                    @if($i === 0)
                                        <a href="{{ $resource::editUrl($record) }}"
                                           class="font-semibold transition hover:text-clay-600">
                                            @include('admin.resource.cell', ['field' => $field, 'record' => $record])
                                        </a>
                                    @else
                                        @include('admin.resource.cell', ['field' => $field, 'record' => $record])
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-4 py-3">
                                <div class="flex justify-end">
                                    @include('admin.resource.row-actions', ['resource' => $resource, 'record' => $record])
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $records->links() }}</div>
    @endif
</x-layouts.admin>
