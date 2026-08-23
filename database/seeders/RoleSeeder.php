<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->data() as $data) {
            Role::query()->create($data);
        }
    }

    /**
     * Data
     */
    private function data(): array
    {
        return [
            [
                'name' => 'Admin',
                'description' => null,
            ],
            [
                'name' => 'Kasir',
                'description' => null,
            ],
        ];
    }
}
