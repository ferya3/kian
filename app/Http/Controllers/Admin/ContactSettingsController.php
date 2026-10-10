<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use App\Models\Location;
use App\Models\Setting;
use App\Support\Contact;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

/**
 * «پنل ← اطلاعات تماس» — تلفن، ایمیل و شبکه‌های اجتماعیِ شرکت.
 *
 * نشانی و ساعت کاری اینجا نیستند و در «پنل ← نشانی‌ها» اند، چون هر
 * مکان — کارخانه، دفتر فروش، انبار — نشانی و ساعت خودش را دارد.
 *
 * یک فرمِ ثابت با میدان‌های برچسب‌دار، و نه فهرستِ کلید/مقدارِ
 * «تنظیمات محتوا»: آنجا مدیر باید نامِ کلید را بداند و هیچ اعتبارسنجی‌ای
 * نیست — شماره‌ای که حرف دارد، ایمیلی که @ ندارد، همه ذخیره می‌شوند و روی
 * سایت می‌نشینند.
 */
class ContactSettingsController extends Controller
{
    public function show()
    {
        $values = collect(array_keys(Contact::FIELDS))
            ->mapWithKeys(fn (string $f) => [$f => Contact::stored($f) ?? (string) config(Contact::FIELDS[$f])])
            ->all();

        return view('admin.contact', [
            'values' => $values,
            // نشانی‌ها جای خودشان را دارند؛ اینجا فقط فهرستی برای دیدن و رفتن
            'locations' => Location::query()->orderBy('position')->orderBy('id')->get(),
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
        ];

        foreach (Contact::SOCIAL as $network) {
            $rules[$network] = ['nullable', 'url:https,http', 'max:200'];
        }

        $data = $request->validate($rules, [
            '*.regex' => 'قالبِ :attribute درست نیست.',
        ], [
            'phone' => 'تلفن مرکزی',
            'sales_phone' => 'خط مستقیم',
            'sales_extension' => 'داخلی',
            'email' => 'ایمیل فروش',
            'technical_email' => 'ایمیل واحد فنی',
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
