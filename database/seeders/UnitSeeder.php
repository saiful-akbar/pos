<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            foreach ($this->data() as $data) {
                Unit::query()->create($data);
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
                'code' => 'G',
                'name' => 'Gram',
            ],
            [
                'code' => 'KG',
                'name' => 'Kilogram',
            ],
            [
                'code' => 'L',
                'name' => 'Liter',
            ],
            [
                'code' => 'ML',
                'name' => 'Mililiter',
            ],
            [
                'code' => 'PCS',
                'name' => 'Pieces',
            ],
            [
                'code' => 'BOX',
                'name' => 'Box',
            ],
            [
                'code' => 'RENCENG',
                'name' => 'Renceng',
            ],
        ];
    }
}
