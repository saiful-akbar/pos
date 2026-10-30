<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Price;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $categories = Category::query()->pluck('id', 'code');
            $units = Unit::query()->pluck('id', 'code');

            foreach ($this->data() as $data) {
                // Buat produk (firstOrCreate agar seeder aman dijalankan ulang).
                $product = Product::query()->firstOrCreate(
                    [
                        'code' => $data['code']
                    ],
                    [
                        'category_id' => $categories[$data['category']],
                        'name' => $data['name'],
                        'minimum_stock' => 0,
                        'current_stock' => 0,
                        'discount' => 0,
                        'is_active' => true,
                    ],
                );

                // Buat 3 harga untuk 3 unit berbeda; unit pertama (PCS) menjadi default.
                foreach ($this->units($data['purchase_price']) as $index => $unit) {
                    Price::query()->firstOrCreate(
                        [
                            'product_id' => $product->id,
                            'unit_id' => $units[$unit['code']],
                        ],
                        [
                            'multiples_quantity' => $unit['multiples_quantity'],
                            'purchase_price' => $unit['purchase_price'],
                            'selling_price' => $unit['selling_price'],
                            'is_default' => $index === 0,
                        ],
                    );
                }
            }
        });
    }

    /**
     * Tiga unit per produk dengan harga berbeda, dihitung dari harga beli per PCS.
     *
     * @return array<int, array{code: string, multiples_quantity: int, purchase_price: int, selling_price: int}>
     */
    private function units(int $purchasePrice): array
    {
        return [
            // 1 PCS.
            [
                'code' => 'PCS',
                'multiples_quantity' => 1,
                'purchase_price' => $purchasePrice,
                'selling_price' => (int) round($purchasePrice * 1.20),
            ],
            // 1 Renceng = 10 PCS (harga sedikit lebih murah per PCS).
            [
                'code' => 'RENCENG',
                'multiples_quantity' => 10,
                'purchase_price' => $purchasePrice * 10,
                'selling_price' => (int) round($purchasePrice * 10 * 1.15),
            ],
            // 1 Box = 40 PCS (harga paling murah per PCS).
            [
                'code' => 'BOX',
                'multiples_quantity' => 40,
                'purchase_price' => $purchasePrice * 40,
                'selling_price' => (int) round($purchasePrice * 40 * 1.10),
            ],
        ];
    }

    /**
     * Data dummy produk.
     *
     * @return array<int, array{code: string, name: string, category: string, purchase_price: int}>
     */
    private function data(): array
    {
        return [
            [
                'code' => 'P0001',
                'name' => 'Mie Instan Goreng',
                'category' => 'A0001',
                'purchase_price' => 2800
            ],
            [
                'code' => 'P0002',
                'name' => 'Biskuit Kelapa',
                'category' => 'A0001',
                'purchase_price' => 1500
            ],
            [
                'code' => 'P0003',
                'name' => 'Wafer Cokelat',
                'category' => 'A0001',
                'purchase_price' => 2000
            ],
            [
                'code' => 'P0004',
                'name' => 'Air Mineral 600ml',
                'category' => 'A0002',
                'purchase_price' => 2500
            ],
            [
                'code' => 'P0005',
                'name' => 'Teh Botol',
                'category' => 'A0002',
                'purchase_price' => 4000
            ],
            [
                'code' => 'P0006',
                'name' => 'Kopi Sachet',
                'category' => 'A0002',
                'purchase_price' => 1200
            ],
            [
                'code' => 'P0007',
                'name' => 'Kecap Manis Sachet',
                'category' => 'A0003',
                'purchase_price' => 900
            ],
            [
                'code' => 'P0008',
                'name' => 'Saus Sambal Sachet',
                'category' => 'A0003',
                'purchase_price' => 800
            ],
            [
                'code' => 'P0009',
                'name' => 'Meses Cokelat',
                'category' => 'A0004',
                'purchase_price' => 1000
            ],
            [
                'code' => 'P0010',
                'name' => 'Sabun Cuci Piring',
                'category' => 'A0006',
                'purchase_price' => 5500
            ],
        ];
    }
}
