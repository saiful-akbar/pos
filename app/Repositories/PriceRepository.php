<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Price\DTOs\PriceDTO;
use App\Application\Price\DTOs\UpdatePriceDTO;
use App\Base\Repository;
use App\Domain\Price\Entities\PriceEntity;
use App\Domain\Price\Repositories\PriceRepositoryInterface;
use App\Domain\Price\ValueObjects\ProductPriceValue;
use App\Domain\Price\ValueObjects\UnitPriceValue;
use App\Models\Price;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Override;

class PriceRepository extends Repository implements PriceRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Price());
    }

    /**
     * Mengambil daftar dalam format query builder.
     */
    #[Override]
    public function getQueryByProductId(string $productId): Builder
    {
        try {
            $columns = [
                'prices.id as id',
                'prices.multiples_quantity as multiples_quantity',
                'prices.purchase_price as purchase_price',
                'prices.selling_price as selling_price',
                'prices.is_default as is_default',
                'products.id as product_id',
                'products.code as product_code',
                'products.name as product_name',
                'products.image as product_image',
                'products.minimum_stock as product_minimum_stock',
                'products.current_stock as product_current_stock',
                'products.is_active as product_is_active',
                'units.id as unit_id',
                'units.code as unit_code',
                'units.name as unit_name',
                'units.is_active as unit_is_active',
            ];

            return $this->model->query()
                ->from('prices')
                ->select($columns)
                ->leftJoin('products', 'prices.product_id', '=', 'products.id')
                ->leftJoin('units', 'prices.unit_id', '=', 'units.id')
                ->where('products.id', $productId);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data berdasarkan id
     */
    #[Override]
    public function findById(string $id): ?PriceEntity
    {
        try {
            $result = $this->model->query()
                ->with(['product', 'unit'])
                ->find($id);

            if ($result === null) {
                return null;
            }

            return $this->mapModelToPriceEntity(
                model: $result,
            );
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data berdasarkan product_id dan unit_id.
     */
    #[Override]
    public function findByProductIdAndUnitId(string $productId, ?string $unitId = null, ?bool $isDefault = null): ?PriceEntity
    {
        try {
            $query = $this->model->query()
                ->with(['product', 'unit'])
                ->where('product_id', $productId);

            if ($unitId !== null) {
                $query->where('unit_id', $unitId);
            }

            if ($isDefault !== null) {
                $query->where('is_default', $isDefault);
            }

            $result = $query->orderBy('created_at', 'asc')->first();

            if ($result === null) {
                return null;
            }

            return $this->mapModelToPriceEntity(
                model: $result,
            );
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Memeriksa apakah data ada berdasarkan product_id dan unit_id.
     */
    #[Override]
    public function existsByProductIdAndUnitId(string $productId, string $unitId, ?bool $isDefault = null): bool
    {
        try {
            $query = $this->model->query()
                ->where('product_id', $productId)
                ->where('unit_id', $unitId);

            if (is_bool($isDefault)) {
                $query->where('is_default', $isDefault);
            }

            return $query->exists();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Memeriksa apakah data terdapat harage default berdasarkan product id.
     */
    #[Override]
    public function existsDefaultByProductId(string $productId): bool
    {
        try {
            return $this->model->query()
                ->where('product_id', $productId)
                ->where('is_default', 1)
                ->exists();
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
    public function create(PriceDTO $value): PriceEntity
    {
        try {
            $result = $this->model->query()->create($value->toArray());

            return $this->mapModelToPriceEntity(
                model: $result->load(['product', 'unit']),
            );
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Update harga berdasarkan id.
     */
    #[Override]
    public function update(UpdatePriceDTO $value): int
    {
        try {
            return $this->model->query()
                ->where('id', $value->id)
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
     * Mengunci (lock for update) seluruh harga milik produk selama transaksi berjalan.
     */
    #[Override]
    public function lockByProductId(string $productId): void
    {
        try {
            $this->model->query()
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->pluck('id');
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menjadikan seluruh harga produk sebagai bukan default, kecuali harga dengan id tertentu.
     */
    #[Override]
    public function unsetDefaultByProductId(string $productId, string $exceptId): int
    {
        try {
            return $this->model->query()
                ->where('product_id', $productId)
                ->where('id', '!=', $exceptId)
                ->where('is_default', 1)
                ->update(['is_default' => 0]);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Hapus permanen.
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
     * Mengambil daftar berdasarkan product_id.
     *
     * @return PriceEntity[]
     */
    #[Override]
    public function getByProductId(string $productId): array
    {
        try {
            return $this->model->query()
                ->with(['product', 'unit'])
                ->where('product_id', $productId)
                ->get()
                ->map(fn ($data) => $this->mapModelToPriceEntity($data))
                ->toArray();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Pemetaan class Model ke class ProductPriceValue.
     */
    private function mapModelToProductPriceValue(Product $model): ProductPriceValue
    {
        return new ProductPriceValue(
            id: $model->id,
            code: $model->code,
            name: $model->name,
            image: $model->image,
            minimumStock: parse_float($model->minimum_stock),
            currentStock: parse_float($model->current_stock),
            discount: parse_float($model->discount),
            isActive: $model->is_active === 1,
        );
    }

    /**
     * Pemetaan class Model ke class UnitPriceValue.
     */
    private function mapModelToUnitPriceValue(Unit $model): UnitPriceValue
    {
        return new UnitPriceValue(
            id: $model->id,
            code: $model->code,
            name: $model->name,
            isActive: $model->_is_active === 1,
        );
    }

    /**
     * Pemetaan class Model ke class PriceEntity
     */
    private function mapModelToPriceEntity(Price $model): PriceEntity
    {
        return new PriceEntity(
            id: $model->id,
            product: $this->mapModelToProductPriceValue($model->product),
            unit: $this->mapModelToUnitPriceValue($model->unit),
            multiplesQuantity: parse_float($model->multiples_quantity),
            purchasePrice: parse_float($model->purchase_price),
            sellingPrice: parse_float($model->selling_price),
            isDefault: (bool) $model->is_default,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
