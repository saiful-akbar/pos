<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Base\Repository;
use App\Domain\Dashboard\Entities\LowStockProductEntity;
use App\Domain\Dashboard\Repositories\ProductDashboardRepositoryInterface;
use App\Models\Product;
use Illuminate\Database\Query\Builder;
use Override;

class ProductDashboardRepository extends Repository implements ProductDashboardRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Product());
    }

    /**
     * Mengambil produk aktif dengan stok saat ini pada atau di bawah batas minimum.
     *
     * Sumber stok resmi adalah products.current_stock (diperbarui oleh pembelian, penjualan, pengembalian, dan aktivasi stok
     * dalam satuan dasar: quantity x multiples_quantity), sedangkan products.minimum_stock diisi pada satuan yang sama.
     *
     * @return LowStockProductEntity[]
     */
    #[Override]
    public function getLowStockProducts(int $limit): array
    {
        try {
            return $this->lowStockQuery()
                ->select('products.id', 'products.code', 'products.name', 'products.current_stock', 'products.minimum_stock', 'categories.name as category_name')
                // Paling mendesak: selisih stok terhadap minimum paling kecil (paling negatif) lebih dahulu.
                ->orderByRaw('(products.current_stock - products.minimum_stock) asc')
                ->orderBy('products.code')
                ->limit($limit)
                ->get()
                ->map(fn (object $row): LowStockProductEntity => new LowStockProductEntity(
                    id: $row->id,
                    code: $row->code,
                    name: $row->name,
                    categoryName: $row->category_name,
                    currentStock: bcadd((string) $row->current_stock, '0', 2),
                    minimumStock: bcadd((string) $row->minimum_stock, '0', 2),
                ))
                ->all();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghitung seluruh produk aktif yang stoknya pada atau di bawah batas minimum.
     */
    #[Override]
    public function countLowStockProducts(): int
    {
        try {
            return $this->lowStockQuery()->count();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Query dasar produk stok menipis: tidak dihapus, aktif, stok <= batas minimum.
     */
    private function lowStockQuery(): Builder
    {
        return $this->model->newQuery()->toBase()
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->where('products.is_active', true)
            ->whereColumn('products.current_stock', '<=', 'products.minimum_stock');
    }
}
