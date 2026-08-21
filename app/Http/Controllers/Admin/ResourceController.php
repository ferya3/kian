<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use App\Models\User;
use App\Support\Admin\Field;
use App\Support\Admin\Registry;
use App\Support\Admin\Resource;
use App\Support\Digits;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * یک کنترلر برای همه‌ی منابع.
 *
 * منطق CRUD یک‌بار نوشته شده: هر رفتار امنیتی — فیلتر ورودی، اعتبارسنجی،
 * آپلود امن، ثبت لاگ — در همه‌ی بخش‌های پنل یکسان اجرا می‌شود و جایی برای
 * فراموش‌کردنش در یک منبع خاص نمی‌ماند.
 */
class ResourceController extends Controller
{
    public function index(Request $request, string $resource)
    {
        $class = $this->resolve($resource);

        $query = $class::query();

        if ($term = trim($request->string('q')->toString())) {
            $query->where(function ($q) use ($class, $term) {
                foreach ($class::searchable() as $column) {
                    $q->orWhere($column, 'like', "%{$term}%");
                }
            });
        }

        // مرتب‌سازی فقط روی ستون‌هایی که منبع اعلام کرده — نه هر رشته‌ای از URL
        $sortable = collect($class::listFields())->filter->sortable->pluck('key')->push($class::$orderBy);
        $sort = $request->string('sort')->toString();
        $direction = $request->string('dir')->toString() === 'asc' ? 'asc' : 'desc';

        if ($sort && $sortable->contains($sort)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy($class::$orderBy, $class::$orderDir);
        }

        return view('admin.resource.index', [
            'resource' => $class,
            'records' => $query->paginate($class::$perPage)->withQueryString(),
            'term' => $term,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function create(string $resource)
    {
        $class = $this->resolve($resource);
        abort_unless($class::$creatable, 404);

        return view('admin.resource.form', [
            'resource' => $class,
            'record' => new $class::$model,
            'creating' => true,
        ]);
    }

    public function store(Request $request, string $resource)
    {
        $class = $this->resolve($resource);
        abort_unless($class::$creatable, 404);

        $data = $this->validated($request, $class, null);

        $record = $class::$model::create($data);

        ActivityLog::record('created', $record, array_keys($data), $class::titleFor($record));

        return redirect()
            ->to($class::editUrl($record))
            ->with('success', $class::$singular.' ساخته شد.');
    }

    public function edit(string $resource, string $id)
    {
        $class = $this->resolve($resource);

        return view('admin.resource.form', [
            'resource' => $class,
            'record' => $class::$model::findOrFail($id),
            'creating' => false,
        ]);
    }

    public function update(Request $request, string $resource, string $id)
    {
        $class = $this->resolve($resource);
        $record = $class::$model::findOrFail($id);

        $this->guardSelfLockout($class, $record, $request);

        $data = $this->validated($request, $class, $record);

        $record->fill($data);
        $changed = array_keys($record->getDirty());
        $record->save();

        if ($changed) {
            ActivityLog::record('updated', $record, $changed, $class::titleFor($record));
        }

        return redirect()
            ->to($class::editUrl($record))
            ->with('success', $changed ? 'تغییرات ذخیره شد.' : 'تغییری برای ذخیره نبود.');
    }

    public function destroy(string $resource, string $id)
    {
        $class = $this->resolve($resource);
        abort_unless($class::$deletable, 404);

        $record = $class::$model::findOrFail($id);

        // مدیر نمی‌تواند حساب خودش را حذف کند
        if ($record instanceof User && $record->is(auth()->user())) {
            return back()->withErrors(['record' => 'حساب کاربری خودتان را نمی‌توانید حذف کنید.']);
        }

        $label = $class::titleFor($record);
        $record->delete();

        ActivityLog::record('deleted', $record, [], $label);

        return redirect()
            ->route('admin.resource.index', $class::$slug)
            ->with('success', "«{$label}» حذف شد.");
    }

    // ------------------------------------------------------------- درونی --

    /** @return class-string<resource> */
    protected function resolve(string $slug): string
    {
        $class = Registry::find($slug);

        abort_if($class === null, 404);
        abort_unless(Registry::accessible($class), 403, 'این بخش فقط برای مدیر کل در دسترس است.');

        return $class;
    }

    /**
     * فقط فیلدهای اعلام‌شده خوانده و اعتبارسنجی می‌شوند.
     *
     * مدل‌ها $guarded = [] دارند؛ اگر ورودی را اینجا فیلتر نکنیم، هر ستونی از
     * جدول با یک فیلد مخفی در فرم قابل نوشتن می‌شود.
     */
    protected function validated(Request $request, string $class, ?Model $record): array
    {
        $fields = $class::formFields($record === null);
        $rules = [];

        $this->normalizeNumericInput($request, $fields);

        foreach ($fields as $field) {
            $rules[$field->key] = $this->rulesFor($field, $record);
        }

        $validator = validator($request->only(array_column($fields, 'key')), $rules, [], $this->attributeNames($fields));
        $validated = $validator->validate();

        $data = [];

        foreach ($fields as $field) {
            $value = $validated[$field->key] ?? null;

            $data = array_merge($data, $this->castValue($request, $field, $value, $record));
        }

        return $this->respectDatabaseDefaults($class::$model, $data);
    }

    /**
     * ارقام فارسی را پیش از اعتبارسنجی لاتین می‌کند.
     *
     * قلم پنل ارقام را فارسی نشان می‌دهد، پس مدیر هم با صفحه‌کلید فارسی تایپ
     * می‌کند. بدون این مرحله «۲۵» از نظر قاعده‌ی numeric نامعتبر است و روی
     * موبایل کاربر خطایی می‌بیند که علتش را نمی‌فهمد.
     *
     * @param  array<int, Field>  $fields
     */
    protected function normalizeNumericInput(Request $request, array $fields): void
    {
        foreach ($fields as $field) {
            if (! in_array($field->type, ['number', 'decimal'], true)) {
                continue;
            }

            $value = $request->input($field->key);

            if (is_string($value) && $value !== '') {
                $request->merge([$field->key => Digits::toLatin($value)]);
            }
        }
    }

    /**
     * فیلد خالی روی ستون NOT NULL نباید null بنویسد.
     *
     * ستون‌هایی مثل position یا download_count در دیتابیس NOT NULL با مقدار
     * پیش‌فرض‌اند. اگر مدیر آن‌ها را خالی بگذارد، به‌جای خطای درج، کلید حذف
     * می‌شود تا در ساخت مقدار پیش‌فرض دیتابیس بنشیند و در ویرایش مقدار قبلی
     * دست‌نخورده بماند.
     */
    protected function respectDatabaseDefaults(string $modelClass, array $data): array
    {
        $model = new $modelClass;

        $columns = collect(Schema::connection($model->getConnectionName())->getColumns($model->getTable()))
            ->keyBy('name');

        foreach ($data as $key => $value) {
            if ($value !== null) {
                continue;
            }

            $column = $columns[$key] ?? null;

            if ($column && ! ($column['nullable'] ?? true)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    protected function rulesFor(Field $field, ?Model $record): array
    {
        $rules = $field->validationRules();

        if ($record) {
            // unique در ویرایش باید خودِ رکورد را نادیده بگیرد
            $rules = array_map(function ($rule) use ($record) {
                if (is_string($rule) && str_starts_with($rule, 'unique:')) {
                    return $rule.','.$record->getKey();
                }

                return $rule;
            }, $rules);
        }

        if ($field->type === 'relation' && $field->relatedModel) {
            $table = (new $field->relatedModel)->getTable();
            $rules[] = Rule::exists($table, 'id');
        }

        if ($field->type === 'select' && $field->options) {
            $rules[] = Rule::in(array_keys($field->options));
        }

        if ($field->type === 'checkboxes' && $field->options) {
            $rules[] = 'array';
        }

        return $rules;
    }

    /** تبدیل مقدار خام فرم به چیزی که در ستون دیتابیس می‌نشیند. */
    protected function castValue(Request $request, Field $field, mixed $value, ?Model $record): array
    {
        return match ($field->type) {
            // چک‌باکس غایب یعنی خاموش — نه «بدون تغییر»
            'boolean' => [$field->key => $request->boolean($field->key)],

            'lines' => [$field->key => $this->splitLines($request->input($field->key))],

            'checkboxes' => [$field->key => array_values(array_filter(
                (array) $value,
                fn ($v) => array_key_exists($v, $field->options)
            ))],

            'password' => filled($value) ? [$field->key => $value] : [],

            'file', 'image' => $this->storeUpload($request, $field, $record),

            'number' => [$field->key => $value === null ? null : (int) $value],
            'decimal' => [$field->key => $value === null ? null : (float) $value],

            default => [$field->key => $value],
        };
    }

    protected function splitLines(?string $raw): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $raw))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * ذخیره‌ی امن فایل آپلودی.
     *
     * نام فایل هرگز از ورودی کاربر ساخته نمی‌شود: Laravel نام تصادفی می‌دهد و
     * پسوند از mime واقعی می‌آید، نه از رشته‌ای که مرورگر فرستاده.
     */
    protected function storeUpload(Request $request, Field $field, ?Model $record): array
    {
        if (! $request->hasFile($field->key)) {
            // حذف صریح فایل موجود
            if ($request->boolean('_remove_'.$field->key) && $record?->{$field->key}) {
                Storage::disk($field->disk)->delete($record->{$field->key});

                return [$field->key => null];
            }

            return [];
        }

        $path = $request->file($field->key)->store('admin/'.$field->folder, $field->disk);

        if ($record?->{$field->key}) {
            Storage::disk($field->disk)->delete($record->{$field->key});
        }

        $data = [$field->key => $path];

        // اندازه‌ی فایل برای نمایش در مرکز دانلود
        if ($record && $record->getConnection()->getSchemaBuilder()->hasColumn($record->getTable(), 'file_size_kb')) {
            $data['file_size_kb'] = (int) ceil(Storage::disk($field->disk)->size($path) / 1024);
        }

        return $data;
    }

    protected function attributeNames(array $fields): array
    {
        return collect($fields)->mapWithKeys(fn (Field $f) => [$f->key => $f->label])->all();
    }

    /** مدیر نباید بتواند نقش یا فعال‌بودن خودش را بردارد و خودش را بیرون بیندازد. */
    protected function guardSelfLockout(string $class, Model $record, Request $request): void
    {
        if (! $record instanceof User || ! $record->is(auth()->user())) {
            return;
        }

        if ($request->input('role') !== 'admin' || ! $request->boolean('is_active')) {
            abort(422, 'نمی‌توانید نقش مدیر کل یا فعال‌بودن حساب خودتان را بردارید.');
        }
    }
}
