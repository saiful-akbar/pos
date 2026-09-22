<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\StockOpname\DTOs\DailyStockOpnameDetailDTO;
use App\Base\Repository;
use App\Domain\StockOpname\Entities\DailyStockOpnameDetailEntity;
use App\Domain\StockOpname\Repositories\DailyStockOpnameDetailRepositoryInterface;
use App\Domain\StockOpname\ValueObjects\CategoryProductStockOpnameValue;
use App\Domain\StockOpname\ValueObjects\ProductStockOpnameValue;
use App\Domain\StockOpname\ValueObjects\UnitProductStockOpnameValue;
use App\Models\Category;
use App\Models\DailyStockOpnameDetail;
use App\Models\Product;
use App\Models\Unit;
use Override;

class DailyStockOpnameDetailRepository extends Repository implements DailyStockOpnameDetailRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new DailyStockOpnameDetail());
    }

    /**
     * Mengambil daftar data berdasarkan header id.
     *
     * @return DailyStockOpnameDetailEntity[]
     */
    #[Override]
    public function getByHeaderId(string $headerId): array
    {
        try {
            return $this->model->query()
                ->with(['product.category', 'product.unit'])
                ->where('daily_stock_opname_id', '=', $headerId)
                ->get()
                ->map(fn ($data) => $this->mapModelToDailyStockOpnameDetailEntity($data))
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
     * Create (insert).
     */
    #[Override]
    public function create(DailyStockOpnameDetailDTO $value): DailyStockOpnameDetailEntity
    {
        try {
            $data = $this->model->query()->create($value->toArray());

            return $this->mapModelToDailyStockOpnameDetailEntity(
                model: $data->load(['product.unit', 'product.category']),
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
     * Update data.
     */
    #[Override]
    public function update(string $id, DailyStockOpnameDetailDTO $value): int
    {
        try {
            return $this->model->query()
                ->where('id', '=', $id)
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
     * Hapus data berdasarkan id.
     */
    #[Override]
    public function deleteById(string $id): int
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
     * Mengambil data berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?DailyStockOpnameDetailEntity
    {
        try {
            $data = $this->model->query()
                ->with(['product.category', 'product.unit'])
                ->find($id);

            if ($data === null) {
                return null;
            }

            return $this->mapModelToDailyStockOpnameDetailEntity(
                model: $data,
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
     * Mengambil data berdasarkan id header dan id product.
     */
    #[Override]
    public function findByHeaderAndProduct(string $headerId, string $productId): ?DailyStockOpnameDetailEntity
    {
        try {
            $data = $this->model->query()
                ->with(['product.category', 'product.unit'])
                ->where('daily_stock_opname_id', $headerId)
                ->where('product_id', $productId)
                ->first();

            if ($data === null) {
                return null;
            }

            return $this->mapModelToDailyStockOpnameDetailEntity(
                model: $data
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
     * Memeriksa apakah data berdasarkan header id dan product id sudah ada atau belum.
     */
    #[Override]
    public function existsByHeaderAndProduct(string $headerId, string $productId): bool
    {
        try {
            return $this->model->query()
                ->where('daily_stock_opname_id', $headerId)
                ->where('product_id', $productId)
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
     * Menghitung total produk.
     */
    #[Override]
    public function countProductByHeader(string $headerId): int|float
    {
        try {
            return (float) $this->model->query()
                ->where('daily_stock_opname_id', $headerId)
                ->distinct()
                ->count('product_id');
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghitung total stok system berdasarkan id header (daily_stock_opnames)
     */
    #[Override]
    public function sumSystemStockByHeader(string $headerId): int|float
    {
        try {
            return (float) $this->model->query()
                ->where('daily_stock_opname_id', $headerId)
                ->sum('system_stock');
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghitung total stok fisik berdasarkan id header (daily_stock_opnames)
     */
    #[Override]
    public function sumPhysicalStockByHeader(string $headerId): int|float
    {
        try {
            return (float) $this->model->query()
                ->where('daily_stock_opname_id', $headerId)
                ->sum('physical_stock');
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Pemetaan class Model ke class CategoryProductStockOpnameValue
     */
    private function mapModelToCategoryProductStockOpnameValue(Category $model): CategoryProductStockOpnameValue
    {
        return new CategoryProductStockOpnameValue(
            id: $model->id,
            code: $model->code,
            name: $model->name,
            isActive: $model->is_active === 1,
        );
    }

    /**
     * Pemetaan class model ke class UnitProductStockOpnameValue.
     */
    private function mapModelToUnitProductStockOpnameValue(Unit $model): UnitProductStockOpnameValue
    {
        return new UnitProductStockOpnameValue(
            id: $model->id,
            code: $model->code,
            name: $model->name,
            isActive: $model->is_active === 1,
        );
    }

    /**
     * Pemetaan class Model ke class ProductStockOpnameValue
     */
    private function mapModelToProductStockOpnameValue(Product $model): ProductStockOpnameValue
    {
        return new ProductStockOpnameValue(
            id: $model->id,
            category: $this->mapModelToCategoryProductStockOpnameValue($model->category),
            unit: $this->mapModelToUnitProductStockOpnameValue($model->unit),
            code: $model->code,
            barcode: $model->barcode,
            name: $model->name,
            image: $model->image,
            purchasePrice: parse_float($model->purchase_price),
            sellingPrice: parse_float($model->selling_price),
            discount: parse_float($model->discount),
            minimumStock: parse_float($model->minimum_stock),
            currentStock: parse_float($model->current_stock),
            isActive: $model->is_active === 1,
        );
    }

    /**
     * Pemetaan class Model ke class DailyStockOpnameDetailEntity.
     */
    private function mapModelToDailyStockOpnameDetailEntity(DailyStockOpnameDetail $model): DailyStockOpnameDetailEntity
    {
        return new DailyStockOpnameDetailEntity(
            id: $model->id,
            dailyStockOpnameId: $model->daily_stock_opname_id,
            product: $this->mapModelToProductStockOpnameValue($model->product),
            systemStock: parse_float($model->system_stock),
            physicalStock: parse_float($model->physical_stock),
            difference: parse_float($model->difference),
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at
        );
    }
}
