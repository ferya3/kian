<?php

namespace Tests;

use App\Models\Locale;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * حافظه‌ی درخواستیِ زبان‌ها، میانِ تست‌ها نمی‌ماند.
     *
     * Locale::mapOrNull برای هر درخواست یک‌بار می‌پرسد و در خاصیتی ایستا
     * نگه می‌دارد. در مرورگر این یعنی یک کوئری به‌جای ده‌تا؛ در PHPUnit یعنی
     * تستی که زبانی را خاموش می‌کند، وضعیتش را به تست بعدی می‌برد — در حالی
     * که RefreshDatabase ردیف‌ها را برگردانده.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Locale::forget();
    }
}
