<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CatalogSeeder::class,
            ProjectSeeder::class,
            FactorySeeder::class,
            KnowledgeSeeder::class,
            AdminSeeder::class,

            /*
            | بی‌قیدِ کلیدِ فروشگاه اجرا می‌شود و این عمدی است: ردیف‌های
            | فروشنده و عرضه با کلیدِ خاموش هیچ‌جا دیده نمی‌شوند، ولی اگر
            | اینجا شرطی بود، روزِ روشن‌کردن باید کسی یادش می‌ماند که
            | seederِ جدا هم بزند — و ویترین خالی بالا می‌آمد.
            */
            ShopSeeder::class,
        ]);
    }
}
