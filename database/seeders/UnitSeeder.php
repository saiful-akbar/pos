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
     *
     * @return array
     */
    private function data(): array
    {
        return [
            [
                'code' => 'PCS',
                'name' => 'Pieces',
            ],
            [
                'code' => 'KG',
                'name' => 'Kilogram',
            ],
        ];
    }
}
