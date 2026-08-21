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
        ]);
    }
}
