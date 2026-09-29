<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            foreach ($this->data() as $data) {
                Category::query()->create($data);
            }
        });
    }

    /**
     * data
     */
    private function data(): array
    {
        return [
            [
                'code' => '0001',
                'name' => 'Makanan',
            ],
            [
                'code' => '0002',
                'name' => 'Minuman',
            ],
            [
                'code' => '0003',
                'name' => 'Bumbu',
            ],
            [
                'code' => '0004',
                'name' => 'Toping',
            ],
            [
                'code' => '0006',
                'name' => 'Alat Kebersihan',
            ],
        ];
    }
}
