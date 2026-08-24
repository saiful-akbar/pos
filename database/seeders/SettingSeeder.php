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
            'app_name' => 'POS',
            'app_version' => '1.0.0',
            'store_name' => 'POS Toko',
            'store_logo' => null,
            'store_address' => 'Jl. buah No. 01, Jakarta, Indonesia 4454',
            'background_image' => null,
        ]);
    }
}
