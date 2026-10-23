<?php

declare(strict_types=1);

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
            // Idempotent: menu dengan route yang sama tidak dibuat ulang saat seeder dijalankan berulang.
            $module = Module::query()->firstOrCreate(
                ['route' => $data['route']],
                [
                    'name' => $data['name'],
                    'icon' => $data['icon'],
                    'path' => $data['path'],
                ],
            );

            foreach ($data['details'] as $detail) {
                $moduleDetail = Module::query()->firstOrCreate(
                    ['route' => $detail['route']],
                    [
                        'header_id' => $module->id,
                        'name' => $detail['name'],
                        'path' => $detail['path'],
                    ],
                );

                foreach ($roles as $role) {
                    if (in_array($role->name, $detail['roles'], true)) {
                        // Hak akses yang sudah ada tidak ditimpa.
                        if ($role->modules()->where('modules.id', $moduleDetail->id)->exists()) {
                            continue;
                        }

                        $now = now();

                        if ($role->name === 'Kasir') {
                            if ($moduleDetail->name === 'Diskon') {
                                $role->modules()->attach($moduleDetail->id, [
                                    'read' => true,
                                    'create' => true,
                                    'update' => true,
                                    'remove' => false,
                                    'delete' => false,
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ]);

                                continue;
                            }

                            if ($moduleDetail->route === 'transactions.purchases') {
                                $role->modules()->attach($moduleDetail->id, [
                                    'read' => true,
                                    'create' => true,
                                    'update' => true,
                                    'remove' => false,
                                    'delete' => false,
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ]);

                                continue;
                            }

                            if ($moduleDetail->route === 'transactions.sales') {
                                $role->modules()->attach($moduleDetail->id, [
                                    'read' => true,
                                    'create' => true,
                                    'update' => true,
                                    'remove' => false,
                                    'delete' => false,
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ]);

                                continue;
                            }

                            // Kasir dapat mencatat dan mengubah transaksi kas, tetapi tidak menghapus.
                            if ($moduleDetail->route === 'cash.transactions') {
                                $role->modules()->attach($moduleDetail->id, [
                                    'read' => true,
                                    'create' => true,
                                    'update' => true,
                                    'remove' => false,
                                    'delete' => false,
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ]);

                                continue;
                            }

                            if ($moduleDetail->route === 'stockOpnames.daily') {
                                $role->modules()->attach($moduleDetail->id, [
                                    'read' => true,
                                    'create' => true,
                                    'update' => true,
                                    'remove' => false,
                                    'delete' => false,
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ]);

                                continue;
                            }

                            if ($moduleDetail->route === 'stockOpnames.monthly') {
                                $role->modules()->attach($moduleDetail->id, [
                                    'read' => true,
                                    'create' => true,
                                    'update' => true,
                                    'remove' => false,
                                    'delete' => false,
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ]);

                                continue;
                            }
                        }

                        $role->modules()->attach($moduleDetail->id, [
                            'read' => true,
                            'create' => $role->name === 'Admin',
                            'update' => $role->name === 'Admin',
                            'remove' => $role->name === 'Admin',
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
     */
    private function data(): array
    {
        return [
            [
                'name' => 'Data Master',
                'icon' => 'data_table',
                'path' => '/masters',
                'route' => 'masters',
                'details' => [
                    [
                        'name' => 'Role',
                        'path' => '/masters/roles',
                        'route' => 'masters.roles',
                        'roles' => ['Admin'],
                    ],
                    [
                        'name' => 'User',
                        'path' => '/masters/users',
                        'route' => 'masters.users',
                        'roles' => ['Admin'],
                    ],
                    [
                        'name' => 'Pemasok',
                        'path' => '/masters/suppliers',
                        'route' => 'masters.suppliers',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                    [
                        'name' => 'Pelanggan (Member)',
                        'path' => '/masters/customers',
                        'route' => 'masters.customers',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                    [
                        'name' => 'Kategori produk',
                        'path' => '/masters/categories',
                        'route' => 'masters.categories',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                    [
                        'name' => 'Unit (Satuan produk)',
                        'path' => '/masters/units',
                        'route' => 'masters.units',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                    [
                        'name' => 'Produk',
                        'path' => '/masters/products',
                        'route' => 'masters.products',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                ],
            ],
            [
                'name' => 'Transaksi',
                'icon' => 'shopping_cart',
                'path' => '/transactions',
                'route' => 'transactions',
                'details' => [
                    [
                        'name' => 'Pembelian',
                        'path' => '/transactions/purchases',
                        'route' => 'transactions.purchases',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                    [
                        'name' => 'Penjualan',
                        'path' => '/transactions/sales',
                        'route' => 'transactions.sales',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                    [
                        'name' => 'Pengembalian',
                        'path' => '/transactions/returns',
                        'route' => 'transactions.returns',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                ],
            ],
            [
                'name' => 'Kas',
                'icon' => 'payments',
                'path' => '/cash',
                'route' => 'cash',
                'details' => [
                    [
                        'name' => 'Ringkasan Kas',
                        'path' => '/cash/dashboard',
                        'route' => 'cash.dashboard',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                    [
                        'name' => 'Transaksi Kas',
                        'path' => '/cash/transactions',
                        'route' => 'cash.transactions',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                    [
                        'name' => 'Rekonsiliasi Kas',
                        'path' => '/cash/reconciliation',
                        'route' => 'cash.reconciliation',
                        'roles' => ['Admin'],
                    ],
                ],
            ],
            [
                'name' => 'Stock',
                'icon' => 'inventory',
                'path' => '/stocks',
                'route' => 'stocks',
                'details' => [
                    [
                        'name' => 'Stock harian',
                        'path' => '/stocks/daily',
                        'route' => 'stocks.daily',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                    [
                        'name' => 'Stock bulanan',
                        'path' => '/stocks/monthly',
                        'route' => 'stocks.monthly',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                ],
            ],
            [
                'name' => 'Laporan',
                'icon' => 'monitoring',
                'path' => '/reports',
                'route' => 'reports',
                'details' => [
                    [
                        'name' => 'Laporan pembelian',
                        'path' => '/reports/purchases',
                        'route' => 'reports.purchases',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                    [
                        'name' => 'Laporan penjualan',
                        'path' => '/reports/sales',
                        'route' => 'reports.sales',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                    [
                        'name' => 'Laporan pengembalian',
                        'path' => '/reports/returns',
                        'route' => 'reports.returns',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                    [
                        'name' => 'Laporan stock',
                        'path' => '/reports/stocks',
                        'route' => 'reports.stocks',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                    [
                        'name' => 'Laporan kas',
                        'path' => '/reports/cash',
                        'route' => 'reports.cash',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                ],
            ],
            [
                'name' => 'Pengaturan',
                'icon' => 'settings',
                'path' => '/settings',
                'route' => 'settings',
                'details' => [
                    [
                        'name' => 'Pengaturan aplikasi',
                        'path' => '/settings/applications',
                        'route' => 'settings.applications',
                        'roles' => ['Admin', 'Kasir'],
                    ],
                ],
            ],
        ];
    }
}
