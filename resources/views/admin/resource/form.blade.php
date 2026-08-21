@php
    use App\Support\Admin\Field;

    $fields = $resource::formFields($creating);
    $title = $creating ? $resource::$singular.' جدید' : $resource::titleFor($record);
    $action = $creating
        ? route('admin.resource.store', $resource::$slug)
        : $resource::updateUrl($record);

    /*
    | فیلدها به ترتیب اعلام گروه می‌شوند؛ فیلدهای بدون بخش در پنل نخست
    | می‌نشینند. کلید '' یعنی بخش بی‌عنوان.
    */
    $sections = [];
    foreach ($fields as $field) {
        $sections[$field->section ?? ''][] = $field;
    }
@endphp

<x-layouts.admin :title="$title" :subtitle="$resource::$label">

    <x-slot:actions>
        <a href="{{ route('admin.resource.index', $resource::$slug) }}"
           class="tap gap-2 rounded-xl border border-sand-300 px-4 text-meta font-semibold text-ink-600 transition hover:bg-sand-200">
            <x-icon name="chevron-left" size="16" class="rotate-180" />
            <span class="hidden sm:inline">بازگشت</span>
        </a>
        @if(! $creating && ($url = $resource::publicUrl($record)))
            <a href="{{ $url }}" target="_blank" rel="noopener"
               class="tap gap-2 rounded-xl border border-sand-300 px-4 text-meta font-semibold text-ink-600 transition hover:bg-sand-200">
                <x-icon name="external" size="15" />
                <span class="hidden sm:inline">در سایت</span>
            </a>
        @endif
    </x-slot:actions>

    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="max-w-4xl">
        @csrf
        @unless($creating) @method('PUT') @endunless

        <div class="space-y-4">
            @foreach($sections as $heading => $sectionFields)
                <section class="rounded-[var(--radius-panel)] border border-sand-300 bg-sand-50 p-5 lg:p-7">
                    @if($heading !== '')
                        <h2 class="mb-5 border-b border-sand-200 pb-3 text-[0.9375rem] font-extrabold text-ink-800">
                            {{ $heading }}
                        </h2>
                    @endif
                    <div class="grid grid-cols-1 gap-x-5 gap-y-5 sm:grid-cols-6">
                        @foreach($sectionFields as $field)
                            <div @class([
                                'sm:col-span-6' => $field->width === 'full',
                                'sm:col-span-3' => $field->width === 'half',
                                'sm:col-span-2' => $field->width === 'third',
                            ])>
                                @include('admin.fields.wrapper', ['field' => $field, 'record' => $record])
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>

        {{--
            روی گوشی فرمِ بلند یعنی دکمه‌ی ذخیره ده‌ها اسکرول پایین‌تر است؛
            نوار چسبان آن را همیشه در دسترس نگه می‌دارد. «حذف» عمداً بیرون از
            این نوار و پایین فرم می‌ماند تا کنار دکمه‌ی ذخیره لمس نشود.
        --}}
        <div data-mobile-action-bar
             class="sticky bottom-0 z-20 -mx-4 mt-5 border-t border-sand-300 bg-sand-100/95 px-4 py-3 backdrop-blur-md sm:static sm:mx-0 sm:border-0 sm:bg-transparent sm:px-0 sm:backdrop-blur-none"
             style="padding-bottom: calc(0.75rem + var(--safe-bottom))">
            <button type="submit"
                    class="tap w-full justify-center gap-2 rounded-xl bg-clay-500 px-7 font-semibold text-white transition hover:bg-clay-600 sm:w-auto">
                {{ $creating ? 'ساختن' : 'ذخیره تغییرات' }}
            </button>
        </div>
    </form>

    @if(! $creating && $resource::$deletable)
        <div class="mt-8 max-w-4xl rounded-[var(--radius-panel)] border border-red-200 bg-red-50/40 p-5">
            <h2 class="text-[0.9375rem] font-extrabold text-red-700">حذف {{ $resource::$singular }}</h2>
            <p class="mt-1 text-meta text-ink-500">این کار برگشت‌پذیر نیست.</p>
            <button type="submit" form="delete-form"
                    class="tap mt-4 gap-2 rounded-xl border border-red-300 bg-sand-50 px-4 text-meta font-semibold text-red-600 transition hover:border-red-500 hover:bg-red-50">
                <x-icon name="close" size="15" />
                حذف {{ $resource::$singular }}
            </button>
        </div>
    @endif

    @if(! $creating && $resource::$deletable)
        <form id="delete-form" method="POST" class="hidden"
              action="{{ $resource::destroyUrl($record) }}"
              onsubmit="return confirm('«{{ addslashes($resource::titleFor($record)) }}» برای همیشه حذف می‌شود. مطمئنید؟')">
            @csrf
            @method('DELETE')
        </form>
    @endif
</x-layouts.admin>
