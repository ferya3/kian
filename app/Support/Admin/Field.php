<?php

namespace App\Support\Admin;

/**
 * تعریف یک فیلد در پنل مدیریت.
 *
 * فیلدها هم فرم را می‌سازند، هم قواعد اعتبارسنجی را، هم ستون‌های جدول را.
 * نکته‌ی امنیتی: فقط فیلدهایی که اینجا اعلام شده‌اند ذخیره می‌شوند. مدل‌ها
 * $guarded = [] دارند، پس اگر ورودی را بر اساس همین فهرست فیلتر نکنیم، هر
 * ستون دیتابیس از طریق فرم قابل نوشتن می‌شود.
 */
class Field
{
    public array $rules = [];

    public array $options = [];

    public ?string $hint = null;

    public ?string $suffix = null;

    public ?string $placeholder = null;

    public string $width = 'full';

    /** عنوان بخشی از فرم که این فیلد در آن می‌نشیند؛ null یعنی بخش نخستِ بی‌عنوان. */
    public ?string $section = null;

    public bool $inList = false;

    public bool $sortable = false;

    public bool $onlyOnCreate = false;

    public bool $onlyOnEdit = false;

    public mixed $default = null;

    public ?string $relatedModel = null;

    public string $relatedLabel = 'name';

    /** نام متد رابطه روی مدل — اگر با نام ستون هم‌خوان نباشد. */
    public ?string $relationName = null;

    public ?string $disk = 'public';

    public ?string $folder = null;

    protected function __construct(
        public string $key,
        public string $label,
        public string $type = 'text',
    ) {}

    // ------------------------------------------------------------ سازنده‌ها --

    public static function text(string $key, string $label): static
    {
        return new static($key, $label);
    }

    public static function slug(string $key, string $label = 'نشانی یکتا (slug)'): static
    {
        return (new static($key, $label, 'slug'))
            ->hint('در URL استفاده می‌شود. فقط حروف، رقم و خط تیره.');
    }

    public static function email(string $key, string $label): static
    {
        return new static($key, $label, 'email');
    }

    public static function password(string $key, string $label): static
    {
        return new static($key, $label, 'password');
    }

    public static function textarea(string $key, string $label): static
    {
        return new static($key, $label, 'textarea');
    }

    public static function longtext(string $key, string $label): static
    {
        return (new static($key, $label, 'longtext'))
            ->hint('هر پاراگراف را در یک خط جدا بنویسید.');
    }

    public static function number(string $key, string $label): static
    {
        return new static($key, $label, 'number');
    }

    public static function decimal(string $key, string $label): static
    {
        return new static($key, $label, 'decimal');
    }

    public static function boolean(string $key, string $label): static
    {
        return new static($key, $label, 'boolean');
    }

    /** فهرست مقادیر متنی — هر خط یک آیتم. */
    public static function lines(string $key, string $label): static
    {
        return (new static($key, $label, 'lines'))
            ->hint('هر مورد را در یک خط بنویسید.');
    }

    /** @param array<string, string> $options */
    public static function select(string $key, string $label, array $options): static
    {
        $field = new static($key, $label, 'select');
        $field->options = $options;

        return $field;
    }

    /** @param array<string, string> $options */
    public static function checkboxes(string $key, string $label, array $options): static
    {
        $field = new static($key, $label, 'checkboxes');
        $field->options = $options;

        return $field;
    }

    public static function relation(string $key, string $label, string $model, string $labelColumn = 'name'): static
    {
        $field = new static($key, $label, 'relation');
        $field->relatedModel = $model;
        $field->relatedLabel = $labelColumn;

        return $field;
    }

    public static function date(string $key, string $label): static
    {
        return new static($key, $label, 'date');
    }

    public static function file(string $key, string $label, string $folder): static
    {
        $field = new static($key, $label, 'file');
        $field->folder = $folder;

        return $field;
    }

    public static function image(string $key, string $label, string $folder): static
    {
        $field = new static($key, $label, 'image');
        $field->folder = $folder;

        return $field;
    }

    public static function readonly(string $key, string $label): static
    {
        return new static($key, $label, 'readonly');
    }

    // -------------------------------------------------------------- زنجیره --

    public function rules(array|string $rules): static
    {
        $this->rules = is_string($rules) ? explode('|', $rules) : $rules;

        return $this;
    }

    public function hint(string $hint): static
    {
        $this->hint = $hint;

        return $this;
    }

    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function suffix(string $suffix): static
    {
        $this->suffix = $suffix;

        return $this;
    }

    public function relationName(string $name): static
    {
        $this->relationName = $name;

        return $this;
    }

    public function half(): static
    {
        $this->width = 'half';

        return $this;
    }

    public function third(): static
    {
        $this->width = 'third';

        return $this;
    }

    /**
     * فیلد را در یک بخشِ عنوان‌دار از فرم می‌گذارد.
     *
     * فرم محصول ۳۵ فیلد دارد؛ بدون بخش‌بندی روی موبایل یک ستون بی‌پایان می‌شود.
     * ترتیب اعلام فیلدها ترتیب بخش‌ها را هم تعیین می‌کند.
     */
    public function section(string $section): static
    {
        $this->section = $section;

        return $this;
    }

    public function inList(bool $sortable = false): static
    {
        $this->inList = true;
        $this->sortable = $sortable;

        return $this;
    }

    public function default(mixed $value): static
    {
        $this->default = $value;

        return $this;
    }

    public function onlyOnCreate(): static
    {
        $this->onlyOnCreate = true;

        return $this;
    }

    public function onlyOnEdit(): static
    {
        $this->onlyOnEdit = true;

        return $this;
    }

    // -------------------------------------------------------------- کمکی‌ها --

    /** قواعد اعتبارسنجی نهایی — ترکیب قواعد اعلام‌شده با قواعد ذاتی نوع فیلد. */
    public function validationRules(): array
    {
        $base = match ($this->type) {
            'email' => ['email:rfc', 'max:190'],
            'password' => ['string', 'min:10', 'max:190'],
            'number' => ['integer'],
            'decimal' => ['numeric'],
            'boolean' => ['boolean'],
            'slug' => ['string', 'max:190', 'regex:/^[\pL\pN\-]+$/u'],
            'date' => ['date'],
            'select', 'relation' => [],
            'checkboxes', 'lines' => ['array'],
            // آپلود: نوع و حجم همیشه محدود می‌شود، حتی اگر منبع چیزی نگوید
            'image' => ['image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            'file' => ['file', 'mimes:pdf,dwg,dxf,rvt,ifc,skp,zip,xlsx,jpg,png,svg', 'max:20480'],
            default => ['string', 'max:2000'],
        };

        return array_values(array_unique([...$base, ...$this->rules]));
    }

    public function isFileUpload(): bool
    {
        return in_array($this->type, ['file', 'image'], true);
    }

    public function isEditable(): bool
    {
        return $this->type !== 'readonly';
    }
}
