<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::query()
            ->where('name', 'Admin')
            ->first()
            ->users()
            ->create([
                'username' => 'admin',
                'password' => bcrypt('admin123'),
                'full_name' => 'Administrator',
            ]);

        Role::query()
            ->where('name', 'Kasir')
            ->first()
            ->users()
            ->create([
                'username' => 'kasir',
                'password' => bcrypt('kasir123'),
                'full_name' => 'Kasir 1',
            ]);
    }
}
