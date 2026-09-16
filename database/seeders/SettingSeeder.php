<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::query()->create([
            'app_name' => 'SMI Sembako',
            'app_version' => '1.0.0',
            'store_name' => 'SMI Sembako',
            'store_logo' => null,
            'store_address' => null,
            'background_image' => null,
        ]);
    }
}
