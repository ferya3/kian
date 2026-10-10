<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use App\Models\Setting;
use App\Support\Contact;
use App\Support\Locales;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

/**
 * «پنل ← اطلاعات تماس» — تلفن، نشانی، ایمیل، ساعت کاری، شبکه‌های اجتماعی.
 *
 * یک فرمِ ثابت با میدان‌های برچسب‌دار، و نه فهرستِ کلید/مقدارِ
 * «تنظیمات محتوا»: آنجا مدیر باید نامِ کلید را بداند و هیچ اعتبارسنجی‌ای
 * نیست — شماره‌ای که حرف دارد، ایمیلی که @ ندارد، همه ذخیره می‌شوند و روی
 * سایت می‌نشینند.
 */
class ContactSettingsController extends Controller
{
    /** زبان‌هایی که برای نشانی و ساعت کاری جعبه‌ی ترجمه می‌گیرند. */
    protected function otherLocales(): array
    {
        return Locales::declared()->except(Locales::default())->all();
    }

    public function show()
    {
        $values = collect(array_keys(Contact::FIELDS))
            ->mapWithKeys(fn (string $f) => [$f => Contact::stored($f) ?? (string) config(Contact::FIELDS[$f])])
            ->all();

        foreach (array_keys($this->otherLocales()) as $code) {
            foreach (Contact::LOCALIZED as $f) {
                $values["{$f}_{$code}"] = Contact::stored("{$f}_{$code}") ?? '';
            }
        }

        return view('admin.contact', [
            'values' => $values,
            'locales' => $this->otherLocales(),
        ]);
    }

    public function update(Request $request)
    {
        $phone = ['regex:/^[+\d۰-۹٠-٩\s\-()]{3,30}$/u'];

        $rules = [
            'phone' => ['required', ...$phone],
            'sales_phone' => ['nullable', ...$phone],
            'sales_extension' => ['nullable', 'regex:/^[\d۰-۹٠-٩]{1,8}$/u'],
            'email' => ['nullable', 'email:rfc', 'max:120'],
            'technical_email' => ['nullable', 'email:rfc', 'max:120'],
            'address' => ['required', 'string', 'max:300'],
            'address_locality' => ['nullable', 'string', 'max:60'],
            'address_region' => ['nullable', 'string', 'max:60'],
            'postal_code' => ['nullable', 'regex:/^[\d۰-۹٠-٩\-\s]{5,12}$/u'],
            'working_hours' => ['nullable', 'string', 'max:200'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ];

        foreach (Contact::SOCIAL as $network) {
            $rules[$network] = ['nullable', 'url:https,http', 'max:200'];
        }

        foreach (array_keys($this->otherLocales()) as $code) {
            $rules["address_{$code}"] = ['nullable', 'string', 'max:300'];
            $rules["working_hours_{$code}"] = ['nullable', 'string', 'max:200'];
        }

        $data = $request->validate($rules, [
            '*.regex' => 'قالبِ :attribute درست نیست.',
        ], [
            'phone' => 'تلفن مرکزی',
            'sales_phone' => 'خط مستقیم',
            'sales_extension' => 'داخلی',
            'email' => 'ایمیل فروش',
            'technical_email' => 'ایمیل واحد فنی',
            'address' => 'نشانی',
            'postal_code' => 'کد پستی',
            'lat' => 'عرض جغرافیایی',
            'lng' => 'طول جغرافیایی',
        ]);

        $changed = [];

        DB::transaction(function () use ($data, &$changed) {
            foreach ($data as $key => $value) {
                $value = trim((string) $value);
                $before = Contact::stored($key);

                if ($before === $value) {
                    continue;
                }

                Setting::put(Contact::PREFIX.$key, $value, 'contact');
                $changed[] = $key;
            }
        });

        if ($changed !== []) {
            ActivityLog::record('updated', null, $changed, 'اطلاعات تماس');
        }

        return redirect()->route('admin.contact')
            ->with('success', $changed === [] ? 'تغییری نبود.' : 'اطلاعات تماس ذخیره شد و روی سایت نشست.');
    }
}
