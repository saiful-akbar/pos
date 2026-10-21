<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\SaleCartDetailDTO;
use App\Base\Repository;
use App\Domain\Sale\Entities\SaleCartDetailEntity;
use App\Domain\Sale\Repositories\SaleCartDetailRepositoryInterface;
use App\Models\SaleCartDetail;
use Illuminate\Database\Eloquent\Builder;
use Override;

class SaleCartDetailRepository extends Repository implements SaleCartDetailRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new SaleCartDetail());
    }

    /**
     * Mengambil data berdasarkan sale_cart_id dan product_id.
     */
    #[Override]
    public function findBySaleCartIdAndProductId(string $saleCartId, string $productId): ?SaleCartDetailEntity
    {
        try {
            $result = $this->model->query()
                ->with(['product', 'unit'])
                ->where('sale_cart_id', $saleCartId)
                ->where('product_id', $productId)
                ->first();

            return $result === null ? null : SaleCartDetailEntity::fromModel($result);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data berdasarkan kolom dan value.
     */
    #[Override]
    public function find(string $column, string|float|int|bool $value): ?SaleCartDetailEntity
    {
        try {
            $result = $this->model->query()
                ->with(['product', 'unit'])
                ->where($column, $value)
                ->first();

            return $result === null ? null : SaleCartDetailEntity::fromModel($result);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Create (insert)
     */
    #[Override]
    public function create(SaleCartDetailDTO $value): SaleCartDetailEntity
    {
        try {
            $result = $this->model->query()->create($value->toArray());

            return $result === null ? null : SaleCartDetailEntity::fromModel($result->load(['product', 'unit']));
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Update.
     */
    #[Override]
    public function update(string $id, SaleCartDetailDTO $value): int
    {
        try {
            return $this->model->query()
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
     * Hapus permanen,
     */
    #[Override]
    public function delete(string $id): int
    {
        try {
            return $this->model->query()
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
     * Menghitung total produk.
     */
    #[Override]
    public function countProductBySaleCartId(string $saleCartId): float
    {
        try {
            $result = $this->model->query()
                ->where('sale_cart_id', $saleCartId)
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
    public function sumQuantityBySaleCartId(string $saleCartId): float
    {
        try {
            $result = $this->model->query()
                ->where('sale_cart_id', $saleCartId)
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
     * Menghitung total amount (subtotal)
     */
    #[Override]
    public function sumAmountBySaleCartId(string $saleCartId): float
    {
        try {
            $result = $this->model->query()
                ->where('sale_cart_id', $saleCartId)
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

    /**
     * Mengambil data berdasarkan sale_cart_id dalam format query builder.
     */
    #[Override]
    public function getQueryBySaleCartId(string $saleCartId): Builder
    {
        try {
            $columns = [
                'sale_cart_details.id as id',
                'sale_cart_details.sale_cart_id as sale_cart_id',
                'sale_cart_details.multiples_quantity as multiples_quantity',
                'sale_cart_details.purchase_price as purchase_price',
                'sale_cart_details.selling_price as selling_price',
                'sale_cart_details.quantity as quantity',
                'sale_cart_details.subtotal as subtotal',
                'sale_cart_details.discount as discount',
                'sale_cart_details.grand_total as grand_total',
                'products.id as product_id',
                'products.code as product_code',
                'products.name as product_name',
                'units.id as unit_id',
                'units.code as unit_code',
                'units.name as unit_name',
            ];

            return $this->model->query()
                ->select($columns)
                ->leftJoin('products', 'sale_cart_details.product_id', '=', 'products.id')
                ->leftJoin('units', 'sale_cart_details.unit_id', '=', 'units.id')
                ->where('sale_cart_details.sale_cart_id', $saleCartId)
                ->orderBy('sale_cart_details.created_at', 'desc');
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data berdasarkan sale_cart_id dalam.
     *
     * @return SaleCartDetailEntity[]
     */
    #[Override]
    public function getBySaleCartId(string $saleCartId): array
    {
        try {
            return $this->model->query()
                ->with(['product', 'unit'])
                ->where('sale_cart_id', $saleCartId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn ($data) => SaleCartDetailEntity::fromModel($data))
                ->toArray();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
