<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductCategory;
use Database\Seeders\CatalogSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * هم‌خوان‌کردن کاتالوگِ سرور با کاتالوگِ کد.
 *
 * سرورِ موجود فقط migrate می‌گیرد و نه --seed، پس وقتی خطِ تولید عوض
 * می‌شود محصول‌های قدیمی سرِ جایشان می‌مانند: سایت چیزی را نشان می‌دهد که
 * کارخانه نمی‌سازد، و مشتری سفارشش را می‌دهد.
 *
 * migrate:fresh --seed این را حل می‌کند ولی همه‌چیز را می‌برد — کاربران
 * پنل، تصویرهای آپلودشده، سفارش‌ها. این دستور فقط به جدول‌های کاتالوگ دست
 * می‌زند.
 */
class SyncCatalog extends Command
{
    protected $signature = 'catalog:sync {--force : بدون پرسیدن}';

    protected $description = 'هم‌خوان‌کردن محصول‌ها و دسته‌ها با کاتالوگ کد';

    public function handle(): int
    {
        $keep = $this->slugsInCode();

        $doomed = Product::query()->whereNotIn('slug', $keep)->get();

        /*
        | عرضه‌ی فروشگاه مانعِ حذف است و نه چیزی که بی‌صدا با محصول برود.
        |
        | عرضه یعنی فروشنده‌ای روی آن محصول قیمت گذاشته و شاید سفارشی هم
        | در راه باشد. پاک‌کردنش از اینجا یعنی تصمیمِ تجاریِ کسِ دیگری را
        | گرفتن، پس فقط گزارش می‌دهیم و دست نمی‌زنیم.
        */
        $blocked = $doomed->filter(fn (Product $p) => $p->offers()->exists());

        if ($blocked->isNotEmpty()) {
            $this->error('این محصول‌ها در فروشگاه عرضه دارند و حذف نشدند:');

            foreach ($blocked as $product) {
                $this->line("  • {$product->name} ({$product->slug})");
            }

            $this->line('  اول عرضه‌هایشان را در «پنل ← فروشگاه ← عرضه‌ها» بردارید.');
        }

        $removable = $doomed->diff($blocked);

        $this->report($removable, $keep);

        if (! $this->option('force') && ! $this->confirm('ادامه؟', true)) {
            return self::FAILURE;
        }

        DB::transaction(function () use ($removable) {
            foreach ($removable as $product) {
                $product->cavities()->delete();
                $product->documents()->delete();
                $product->projects()->detach();
                $product->solutions()->detach();
                $product->delete();
            }

            (new CatalogSeeder)->run();

            $this->pruneEmptyCategories();
        });

        $this->newLine();
        $this->info('کاتالوگ هم‌خوان شد: '.Product::count().' محصول در '
            .ProductCategory::whereNull('parent_id')->count().' خانواده.');

        $this->warn('  عددهای فنی محصول‌های تازه تخمینی‌اند — در پنل با مقدار واقعی جایگزین کنید.');
        $this->warn('  و پس از آن این دستور را دوباره نزنید: مقدارهای پنل را با همین تخمین‌ها برمی‌گرداند.');

        return self::SUCCESS;
    }

    /**
     * اسلاگ‌هایی که کاتالوگِ کد می‌سازد.
     *
     * از خودِ seeder خوانده می‌شود و نه از فهرستی دستی در این دستور: دو
     * فهرست که باید با هم بمانند، روزی از هم جدا می‌شوند — و آن روز این
     * دستور محصولِ درست را حذف می‌کند.
     *
     * @return array<int, string>
     */
    protected function slugsInCode(): array
    {
        preg_match_all(
            "/'slug' => '([a-z0-9-]+)', 'sku'/",
            file_get_contents(database_path('seeders/CatalogSeeder.php')),
            $matches
        );

        return $matches[1];
    }

    /** دسته‌ای که نه محصول دارد نه زیرمجموعه، در منو به صفحه‌ی خالی می‌رسد. */
    protected function pruneEmptyCategories(): void
    {
        ProductCategory::query()
            ->whereDoesntHave('products')
            ->whereDoesntHave('children')
            ->delete();
    }

    /** @param  \Illuminate\Support\Collection<int, Product>  $removable */
    protected function report($removable, array $keep): void
    {
        $this->newLine();

        if ($removable->isEmpty()) {
            $this->line('محصولی برای حذف نیست.');
        } else {
            $this->line('حذف می‌شوند ('.$removable->count().'):');

            foreach ($removable as $product) {
                $this->line("  − {$product->name} ({$product->slug})");
            }
        }

        $missing = array_diff($keep, Product::pluck('slug')->all());

        if ($missing !== []) {
            $this->line('ساخته می‌شوند ('.count($missing).'):');

            foreach ($missing as $slug) {
                $this->line("  + {$slug}");
            }
        }
    }
}
