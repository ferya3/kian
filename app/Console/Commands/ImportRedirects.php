<?php

namespace App\Console\Commands;

use App\Models\Redirect;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * نشانی‌های سایتِ قدیم را به جدولِ ریدایرکت می‌آورد.
 *
 *   php artisan redirects:import --archive=kiyanbehsaz.com
 *       فهرستِ صفحه‌هایی که آرشیوِ وب (web.archive.org) از دامنه دیده.
 *
 *   php artisan redirects:import /root/urls.txt
 *       هر خط یک نشانی، یا «نشانیِ قدیم  مقصد». خروجیِ CSV ِ سرچ کنسول هم
 *       مستقیم خوانده می‌شود (ستونِ اول).
 *
 * برای هر نشانی مقصدی حدس زده می‌شود و در یادداشت علامت می‌خورد؛ مدیر در
 * پنل اصلاحش می‌کند. ردیفی که از پیش هست دست نمی‌خورد — کارِ دستیِ مدیر
 * با اجرای دوباره پاک نمی‌شود.
 */
class ImportRedirects extends Command
{
    protected $signature = 'redirects:import
        {file? : فایلِ فهرستِ نشانی‌ها}
        {--archive= : دامنه برای خواندن از آرشیوِ وب}
        {--dry-run : فقط نشان بده، ذخیره نکن}';

    protected $description = 'آوردنِ نشانی‌های سایتِ قدیم به ریدایرکت‌ها';

    /** نشانی‌هایی که صفحه نیستند: فایل، ساختارِ وردپرس، جستجو. */
    protected const SKIP = '~^/(wp-admin|wp-includes|wp-content|wp-json|feed|comments|xmlrpc\.php|wp-login\.php)(/|$)|/(feed|trackback|amp|embed)$|\.(css|js|jpe?g|png|gif|webp|svg|ico|woff2?|ttf|eot|pdf|zip|xml|txt|php)$|^/\?(s|replytocom|share|amp)=~i';

    public function handle(): int
    {
        $rows = []; // key => explicit target|null

        if ($domain = $this->option('archive')) {
            $domain = preg_replace('~^(https?://)?(www\.)?~i', '', rtrim($domain, '/'));
            $this->info("خواندن از آرشیوِ وب برای {$domain} …");

            try {
                // filter دوبار می‌آید؛ آرایه‌ی Http آن را filter[0] می‌کرد که آرشیو نمی‌فهمد
                $response = Http::timeout(180)->retry(2, 3000)->get('https://web.archive.org/cdx/search/cdx?'.implode('&', [
                    'url='.rawurlencode($domain.'/*'),
                    'output=json', 'fl=original', 'collapse=urlkey', 'limit=20000',
                    'filter=statuscode:200', 'filter=mimetype:text/html',
                ]));
            } catch (Throwable $e) {
                $this->error('آرشیوِ وب جواب نداد: '.$e->getMessage());

                return self::FAILURE;
            }

            if (! $response->ok()) {
                $this->error('آرشیوِ وب خطا داد: HTTP '.$response->status());

                return self::FAILURE;
            }

            foreach (array_slice($response->json() ?? [], 1) as $line) {
                $rows[Redirect::normalize($line[0])] ??= null;
            }
        }

        if ($file = $this->argument('file')) {
            if (! is_readable($file)) {
                $this->error("فایل خوانده نشد: {$file}");

                return self::FAILURE;
            }

            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim(preg_replace('/^\xEF\xBB\xBF/', '', $line));
                $parts = str_contains($line, ',')
                    ? array_map(fn ($c) => trim($c, " \t\""), str_getcsv($line))
                    : preg_split('/\s+/', $line);

                $old = $parts[0] ?? '';
                if (! preg_match('~^(https?://|/)~i', $old)) {
                    continue; // سرستونِ CSV و خطِ بی‌نشانی
                }

                $new = $parts[1] ?? null;
                $rows[Redirect::normalize($old)] = $new && preg_match('~^(https?://|/)~i', $new) ? $new : ($rows[Redirect::normalize($old)] ?? null);
            }
        }

        if (! $rows) {
            $this->warn('نشانی‌ای پیدا نشد. یا --archive=دامنه بدهید، یا مسیرِ فایل.');

            return $this->option('archive') || $this->argument('file') ? self::SUCCESS : self::INVALID;
        }

        $existing = Redirect::query()->pluck('to_url', 'from_path');
        $added = $filled = $skipped = 0;
        $preview = [];

        foreach ($rows as $key => $target) {
            if ($key === '/' || preg_match(self::SKIP, $key)) {
                $skipped++;

                continue;
            }

            $guessed = $target === null;
            $to = $target ?? Redirect::guess($key);

            if ($existing->has($key)) {
                // فقط ردیفی که خودِ سایت از ۴۰۴ ثبت کرده و هنوز مقصد ندارد پر می‌شود
                if ($existing[$key] !== null) {
                    $skipped++;

                    continue;
                }
                $this->option('dry-run') || Redirect::query()->where('from_path', $key)->first()?->update([
                    'to_url' => $to, 'note' => $guessed ? 'حدسِ خودکار — بررسی کنید' : null,
                ]);
                $filled++;
            } else {
                $this->option('dry-run') || Redirect::create([
                    'from_path' => $key, 'to_url' => $to, 'status_code' => 301,
                    'source' => $this->option('archive') ? 'archive' : 'import',
                    'note' => $guessed ? 'حدسِ خودکار — بررسی کنید' : null,
                ]);
                $added++;
            }

            if (count($preview) < 40) {
                $preview[] = [$key, $to.($guessed ? '  (حدس)' : '')];
            }
        }

        if ($preview) {
            $this->table(['نشانی قدیم', 'مقصد'], $preview);
        }

        $this->info(($this->option('dry-run') ? '[آزمایشی — چیزی ذخیره نشد] ' : '')
            ."تازه: {$added} — پرشده: {$filled} — رد/تکراری: {$skipped}");
        $this->line('مقصدهای حدسی را در پنل، «سیستم ← ریدایرکت نشانی‌های قدیم» بررسی کنید.');

        return self::SUCCESS;
    }
}
