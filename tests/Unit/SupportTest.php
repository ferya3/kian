<?php

namespace Tests\Unit;

use App\Support\Digits;
use App\Support\Jalali;
use App\Support\Slug;
use PHPUnit\Framework\TestCase;

class SupportTest extends TestCase
{
    public function test_it_builds_readable_persian_slugs(): void
    {
        $this->assertSame(
            'راهنمای-انتخاب-ضخامت-دیوار',
            Slug::make('راهنمای انتخاب ضخامت دیوار')
        );
    }

    public function test_it_normalises_arabic_letters_and_digits_in_slugs(): void
    {
        $this->assertSame('بلوک-سفالی-20', Slug::make('بلوك سفالي ۲۰'));
    }

    public function test_it_never_produces_repeated_or_trailing_separators(): void
    {
        $this->assertSame('بلوک-20-سفالی', Slug::make('  بلوک ۲۰ —— سفالی!!! '));
    }

    public function test_it_converts_latin_digits_to_persian(): void
    {
        $this->assertSame('۱۲۰٬۰۰۰', Jalali::digits('120,000'));
        $this->assertSame('۶٫۵', Jalali::digits('6.5'));
    }

    public function test_it_normalises_persian_and_arabic_digits(): void
    {
        // قلم سایت ارقام را فارسی نشان می‌دهد، پس کاربر هم فارسی تایپ می‌کند
        $this->assertSame('120', Digits::toLatin('۱۲۰'));
        $this->assertSame('0.28', Digits::toLatin('۰٫۲۸'));
        $this->assertSame('1234567890', Digits::toLatin('١٢٣٤٥٦٧٨٩٠'));
        $this->assertSame('09121234567', Digits::digitsOnly('۰۹۱۲-۱۲۳ ۴۵۶۷'));
    }

    public function test_it_formats_gregorian_dates_as_jalali(): void
    {
        $date = new \DateTimeImmutable('2026-08-21', new \DateTimeZone('UTC'));

        $this->assertSame('۳۰ مرداد ۱۴۰۵', Jalali::format($date));
        $this->assertSame('۱۴۰۵', Jalali::year($date));
    }
}
