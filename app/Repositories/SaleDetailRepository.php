<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\SaleDetailDTO;
use App\Base\Repository;
use App\Domain\Sale\Entities\SaleDetailEntity;
use App\Domain\Sale\Repositories\SaleDetailRepositoryInterface;
use App\Models\SaleDetail;
use Illuminate\Database\Eloquent\Builder;
use Override;

class SaleDetailRepository extends Repository implements SaleDetailRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new SaleDetail());
    }

    /**
     * Mencari data sale detail berdasarkan ID.
     */
    #[Override]
    public function findById(string $id): ?SaleDetailEntity
    {
        try {
            $model = $this->model
                ->newQuery()
                ->with(['product', 'unit'])
                ->find($id);

            return $model !== null
                ? SaleDetailEntity::fromModel($model)
                : null;
        } catch (\Throwable $e) {
            $this->errorLog(
                __METHOD__,
                $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mencari data sale detail berdasarkan sale_id dan product_id.
     */
    #[Override]
    public function findBySaleIdAndProductId(string $saleId, string $productId): ?SaleDetailEntity
    {
        try {
            $model = $this->model
                ->newQuery()
                ->with(['product', 'unit'])
                ->where('sale_id', $saleId)
                ->where('product_id', $productId)
                ->first();

            return $model !== null
                ? SaleDetailEntity::fromModel($model)
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
     * Membuat data sale detail baru.
     */
    #[Override]
    public function create(SaleDetailDTO $value): SaleDetailEntity
    {
        try {
            $model = $this->model
                ->newQuery()
                ->create($value->toArray());

            $model->load(['product', 'unit']);

            return SaleDetailEntity::fromModel($model);
        } catch (\Throwable $e) {
            $this->errorLog(
                __METHOD__,
                $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghapus data sale detail berdasarkan ID.
     */
    #[Override]
    public function deleteById(string $id): int
    {
        try {
            return $this->model
                ->newQuery()
                ->whereKey($id)
                ->delete();
        } catch (\Throwable $e) {
            $this->errorLog(
                __METHOD__,
                $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghapus seluruh detail berdasarkan ID penjualan.
     */
    #[Override]
    public function deleteBySaleId(string $saleId): int
    {
        try {
            return $this->model
                ->newQuery()
                ->where('sale_id', $saleId)
                ->delete();
        } catch (\Throwable $e) {
            $this->errorLog(
                __METHOD__,
                $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil daftar detail penjualan berdasarkan ID penjualan.
     *
     * @return array<SaleDetailEntity>
     */
    #[Override]
    public function getBySaleId(string $saleId): array
    {
        try {
            $models = $this->model
                ->newQuery()
                ->with(['product', 'unit'])
                ->where('sale_id', $saleId)
                ->orderBy('created_at', 'asc')
                ->get();

            return $models
                ->map(fn (SaleDetail $model): SaleDetailEntity => SaleDetailEntity::fromModel($model))
                ->all();
        } catch (\Throwable $e) {
            $this->errorLog(
                __METHOD__,
                $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data berdasarkan sale_id beserta data produk dan unit dalam format query builder.
     */
    #[Override]
    public function getQueryBySaleId(string $saleId): Builder
    {
        try {
            $columns = [
                'sale_details.id as id',
                'sale_details.sale_id as sale_id',
                'sale_details.multiples_quantity as multiples_quantity',
                'sale_details.purchase_price as purchase_price',
                'sale_details.selling_price as selling_price',
                'sale_details.quantity as quantity',
                'sale_details.subtotal as subtotal',
                'sale_details.discount as discount',
                'sale_details.grand_total as grand_total',
                'products.id as product_id',
                'products.code as product_code',
                'products.name as product_name',
                'units.id as unit_id',
                'units.code as unit_code',
                'units.name as unit_name',
            ];

            return $this->model->query()
                ->select($columns)
                ->leftJoin('products', 'sale_details.product_id', '=', 'products.id')
                ->leftJoin('units', 'sale_details.unit_id', '=', 'units.id')
                ->where('sale_details.sale_id', $saleId)
                ->orderBy('sale_details.created_at', 'desc');
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
