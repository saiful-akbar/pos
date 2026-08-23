<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::all();

        foreach ($this->data() as $data) {
            $module = Module::query()->create([
                'name' => $data['name'],
                'path' => $data['path'],
                'route' => $data['route'],
            ]);

            foreach ($data['details'] as $detail) {
                $moduleDetail = Module::query()->create([
                    'header_id' => $module->id,
                    'name' => $detail['name'],
                    'path' => $detail['path'],
                    'route' => $detail['route'],
                ]);

                foreach ($roles as $role) {
                    if (in_array($role->name, $detail['roles'], true)) {
                        $now = now();

                        $role->modules()->attach($moduleDetail->id, [
                            'read' => true,
                            'create' => $role->name === 'Admin',
                            'update' => $role->name === 'Admin',
                            'delete' => $role->name === 'Admin',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Data
     *
     * @return array
     */
    private function data(): array
    {
        return [
            [
                'name' => 'Data Master',
                'path' => '/master',
                'route' => 'master',
                'details' => [
                    [
                        'name' => 'User',
                        'path' => '/master/users',
                        'route' => 'master.users',
                        'roles' => ['Admin'],
                    ],
                    [
                        'name' => 'Role',
                        'path' => '/master/roles',
                        'route' => 'master.roles',
                        'roles' => ['Admin'],
                    ],
                    [
                        'name' => 'Kategori Produk',
                        'path' => '/master/categories',
                        'route' => 'master.categories',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                    [
                        'name' => 'Unit Produk',
                        'path' => '/master/units',
                        'route' => 'master.units',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                    [
                        'name' => 'Produk',
                        'path' => '/master/products',
                        'route' => 'master.products',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                    [
                        'name' => 'Pelanggan',
                        'path' => '/master/customers',
                        'route' => 'master.customers',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                    [
                        'name' => 'Pemasok',
                        'path' => '/master/suppliers',
                        'route' => 'master.suppliers',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                ]
            ]
        ];
    }
}
