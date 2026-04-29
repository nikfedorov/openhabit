<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            AiToneSeeder::class,
            AiModelSeeder::class,
            HabitTemplateSeeder::class,
            InvoiceSeeder::class,
        ]);
    }
}
