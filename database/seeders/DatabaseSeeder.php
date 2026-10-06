<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            ModuleSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
            SupplierSeeder::class,
            // CustomerSeeder::class,
            // CategorySeeder::class,
            // UnitSeeder::class,
            // ProductSeeder::class,
        ]);
    }
}
