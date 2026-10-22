<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\SaleReturnCartDetailDTO;
use App\Base\Repository;
use App\Domain\Sale\Entities\SaleReturnCartDetailEntity;
use App\Domain\Sale\Repositories\SaleReturnCartDetailRepositoryInterface;
use App\Models\SaleReturnCartDetail;
use Illuminate\Database\Eloquent\Builder;
use Override;

class SaleReturnCartDetailRepository extends Repository implements SaleReturnCartDetailRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new SaleReturnCartDetail());
    }

    /**
     * Mengambil data berdasarkan sale_return_cart_id beserta data produk dan unit dalam format query builder.
     */
    #[Override]
    public function getQueryBySaleReturnCartId(string $saleReturnCartId): Builder
    {
        try {
            $columns = [
                'sale_return_cart_details.id as id',
                'sale_return_cart_details.sale_return_cart_id as sale_return_cart_id',
                'sale_return_cart_details.multiples_quantity as multiples_quantity',
                'sale_return_cart_details.purchase_price as purchase_price',
                'sale_return_cart_details.selling_price as selling_price',
                'sale_return_cart_details.quantity as quantity',
                'sale_return_cart_details.subtotal as subtotal',
                'sale_return_cart_details.discount as discount',
                'sale_return_cart_details.grand_total as grand_total',
                'products.id as product_id',
                'products.code as product_code',
                'products.name as product_name',
                'units.id as unit_id',
                'units.code as unit_code',
                'units.name as unit_name',
            ];

            return $this->model->query()
                ->select($columns)
                ->leftJoin('products', 'sale_return_cart_details.product_id', '=', 'products.id')
                ->leftJoin('units', 'sale_return_cart_details.unit_id', '=', 'units.id')
                ->where('sale_return_cart_details.sale_return_cart_id', $saleReturnCartId)
                ->orderBy('sale_return_cart_details.created_at', 'desc');
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil daftar (koleksi) data berdasarkan sale_return_cart_id.
     *
     * @return array<SaleReturnCartDetailEntity>
     */
    #[Override]
    public function getBySaleReturnCartId(string $saleReturnCartId): array
    {
        try {
            // Ambil seluruh detail keranjang beserta relasi produk dan unit.
            $models = $this->model
                ->newQuery()
                ->with(['product', 'unit'])
                ->where('sale_return_cart_id', $saleReturnCartId)
                ->orderBy('created_at', 'asc')
                ->get();

            return $models
                ->map(fn (SaleReturnCartDetail $model): SaleReturnCartDetailEntity => SaleReturnCartDetailEntity::fromModel($model))
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
     * Mengambil data berdasarkan ID.
     */
    #[Override]
    public function findById(string $id): ?SaleReturnCartDetailEntity
    {
        try {
            // Ambil detail keranjang pengembalian beserta relasi produk dan unit.
            $model = $this->model
                ->newQuery()
                ->with(['product', 'unit'])
                ->find($id);

            return $model !== null
                ? SaleReturnCartDetailEntity::fromModel($model)
                : null;
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data berdasarkan sale_return_cart_id dan product_id.
     */
    #[Override]
    public function findBySaleReturnCartIdAndProductId(string $saleReturnCartId, string $productId): ?SaleReturnCartDetailEntity
    {
        try {
            $model = $this->model
                ->newQuery()
                ->with(['product', 'unit'])
                ->where('sale_return_cart_id', $saleReturnCartId)
                ->where('product_id', $productId)
                ->first();

            return $model !== null
                ? SaleReturnCartDetailEntity::fromModel($model)
                : null;
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Membuat data sale return cart detail baru.
     */
    #[Override]
    public function create(SaleReturnCartDetailDTO $value): SaleReturnCartDetailEntity
    {
        try {
            $model = $this->model
                ->newQuery()
                ->create($value->toArray());

            $model->load(['product', 'unit']);

            return SaleReturnCartDetailEntity::fromModel($model);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Update data sale return cart detail.
     */
    #[Override]
    public function update(string $id, SaleReturnCartDetailDTO $value): int
    {
        try {
            return $this->model
                ->newQuery()
                ->where('id', $id)
                ->update($value->toArray());
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghapus permanen data sale return cart detail berdasarkan ID.
     */
    #[Override]
    public function deleteById(string $id): int
    {
        try {
            // Hapus permanen (force delete) baris detail keranjang.
            return $this->model
                ->newQuery()
                ->where('id', $id)
                ->forceDelete();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghapus permanen seluruh data berdasarkan sale_return_cart_id.
     */
    #[Override]
    public function deleteBySaleReturnCartId(string $saleReturnCartId): int
    {
        try {
            // Hapus permanen (force delete) seluruh detail milik keranjang.
            return $this->model
                ->newQuery()
                ->where('sale_return_cart_id', $saleReturnCartId)
                ->forceDelete();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghitung total produk.
     */
    #[Override]
    public function countProductBySaleReturnCartId(string $saleReturnCartId): float
    {
        try {
            $result = $this->model
                ->newQuery()
                ->where('sale_return_cart_id', $saleReturnCartId)
                ->count('product_id');

            return parse_float($result);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghitung total quantity.
     */
    #[Override]
    public function sumQuantityBySaleReturnCartId(string $saleReturnCartId): float
    {
        try {
            $result = $this->model
                ->newQuery()
                ->where('sale_return_cart_id', $saleReturnCartId)
                ->sum('quantity');

            return parse_float($result);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghitung total amount (grand_total).
     */
    #[Override]
    public function sumAmountBySaleReturnCartId(string $saleReturnCartId): float
    {
        try {
            $result = $this->model
                ->newQuery()
                ->where('sale_return_cart_id', $saleReturnCartId)
                ->sum('grand_total');

            return parse_float($result);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
