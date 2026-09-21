<?php

namespace App\Http\Middleware;

use App\Support\Locales;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * زبان را از پیشوند مسیر برمی‌دارد و به کل درخواست می‌دهد.
 *
 * URL::defaults گرانبهاترین خط اینجاست: پارامتر locale را برای همه‌ی
 * فراخوانی‌های route() پر می‌کند، پس ویوها دست‌نخورده می‌مانند و
 * route('products.index') خودش /en/products می‌سازد وقتی کاربر انگلیسی است.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        /*
        | زبانی که مدیر خاموشش کرده، همین‌جا تمام می‌شود.
        |
        | مسیرش هنوز می‌خورد (قیدِ پیشوند از declaredCodes می‌آید تا کشِ مسیرها
        | به حالتِ متغیر گره نخورد)، پس اگر اینجا رد می‌شد، همان صفحه زیر دو
        | نشانی منتشر می‌ماند — یک‌بار /fa/products و یک‌بار /en/products با
        | متنِ فارسی. ۳۰۱ و نه ۴۰۴: نشانی‌های زبانِ خاموش پیش‌تر ایندکس شده‌اند
        | و باید اعتبارشان به صفحه‌ی زنده منتقل شود.
        */
        if (Locales::isDisabled($locale)) {
            return $this->moveToDefault($request);
        }

        if (! Locales::supports($locale)) {
            $locale = Locales::default();
        }

        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);

        /*
        | و حالا پارامتر را از مسیر برمی‌داریم.
        |
        | لاراول پارامترهای مسیر را به‌ترتیب و نه به‌نام به کنترلر می‌دهد
        | (ControllerDispatcher با array_values صدا می‌زند). با ماندنِ locale
        | به‌عنوان پارامتر اول، هر متدی مثل show(Product $product) رشته‌ی
        | «fa» را به‌جای مدل می‌گرفت.
        |
        | تولید نشانی آسیب نمی‌بیند: URL::defaults بالا مقدار را دارد.
        */
        $request->route()?->forgetParameter('locale');

        // انتخاب کاربر یادش می‌ماند تا دفعه‌ی بعد «/» او را به همان‌جا ببرد
        $response = $next($request);

        if ($request->cookie(Locales::COOKIE) !== $locale) {
            $response->headers->setCookie(
                cookie()->forever(Locales::COOKIE, $locale, sameSite: 'lax')
            );
        }

        return $response;
    }

    /**
     * همین صفحه، در زبان پیش‌فرض.
     *
     * از مسیر ساخته می‌شود و نه با جایگزینیِ رشته در نشانی، تا پارامترها —
     * شناسه‌ی محصول، فیلتر، شماره‌ی صفحه — سرِ جایشان بمانند. اگر مسیر نامی
     * نداشت (که نباید داشته باشد) به خانه‌ی همان زبان می‌رسیم و نه به ۵۰۰.
     */
    protected function moveToDefault(Request $request): Response
    {
        $default = Locales::default();
        $route = $request->route();
        $name = $route?->getName();

        if (! $name) {
            return redirect('/'.$default, 301);
        }

        return redirect(
            route($name, array_merge(
                $route->parameters(),
                $request->query(),
                ['locale' => $default],
            )),
            301,
        );
    }
}
