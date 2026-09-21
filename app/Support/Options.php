<?php

namespace App\Support;

/**
 * گزینه‌های ثابتِ سایت، به زبانِ صفحه.
 *
 * نوع پروژه، نوع دیوار، سطح عایق و سه پرسونا — فهرستشان در config/kian.php
 * است، چون *ساختار* است: کلیدهایشان در دیتابیس ذخیره می‌شوند، قواعد
 * اعتبارسنجی از رویشان ساخته می‌شود و موتور انتخاب محصول با آن‌ها امتیاز
 * می‌دهد. عوض‌کردن یک کلید یعنی مهاجرت داده و نه ترجمه.
 *
 * ولی برچسب و توضیحِ همان کلیدها متن‌اند و دیده می‌شوند. این کلاس ترجمه را
 * روی فهرستِ پیکربندی می‌نشاند: ساختار یکی می‌ماند و متنش با زبان عوض می‌شود.
 *
 * پرونده‌ی زبانِ پیش‌فرض این کلیدها را ندارد — مقدارش همان است که در config
 * نوشته شده، و نوشتنِ دوباره‌اش یعنی دو نسخه که روزی از هم می‌افتند.
 */
class Options
{
    /**
     * یک گروه از گزینه‌های موتور انتخاب محصول، با برچسبِ ترجمه‌شده.
     *
     * @return array<string, array{label: string, hint?: string}>
     */
    public static function finder(string $group): array
    {
        return static::overlay("kian.finder.{$group}", "site.finder.{$group}");
    }

    public static function finderLabel(string $group, string $key): string
    {
        return static::finder($group)[$key]['label'] ?? $key;
    }

    /** @return array<string, array<string, string>> */
    public static function audiences(): array
    {
        return static::overlay('kian.audiences', 'site.audiences');
    }

    /**
     * فهرستِ پیکربندی، با هر کلیدی که ترجمه دارد جایگزین‌شده.
     *
     * جایگزینی فقط یک لایه عمیق است و همین کافی است: ساختارِ هر دو فهرست
     * «کلید ← آرایه‌ی تخت» است. ترجمه‌ی ناقص هم کار می‌کند — گزینه‌ای که
     * ترجمه ندارد، متنِ پیکربندی را نگه می‌دارد.
     *
     * @return array<string, array<string, mixed>>
     */
    protected static function overlay(string $configKey, string $line): array
    {
        $base = (array) config($configKey, []);
        $translated = __($line);

        if (! is_array($translated)) {
            return $base;
        }

        foreach ($translated as $key => $values) {
            if (isset($base[$key]) && is_array($values)) {
                $base[$key] = array_merge($base[$key], $values);
            }
        }

        return $base;
    }
}
