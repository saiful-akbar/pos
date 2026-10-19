<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Product\DTOs\ProductDTO;
use App\Base\Repository;
use App\Domain\Product\Entities\ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Product\ValueObjects\CategoryProductValue;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Override;

class ProductRepository extends Repository implements ProductRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Product());
    }

    /**
     * Mengambil daftar dalam format query builder.
     */
    #[Override]
    public function getQuery(?bool $isActive = null): Builder
    {
        try {
            $query = $this->model->query()->with('category');

            if (is_bool($isActive)) {
                $query->where('is_active', $isActive);
            }

            return $query;
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
    public function create(ProductDTO $value): ProductEntity
    {
        try {
            $result = $this->model->query()->create($value->toArray());

            return $this->mapModelToProductEntity(
                model: $result->load('category'),
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
     * Mengambil data berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?ProductEntity
    {
        try {
            $result = $this->model->with('category')->find($id);

            if ($result === null) {
                return null;
            }

            return $this->mapModelToProductEntity(
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
     * Update
     */
    #[Override]
    public function update(string $id, ProductDTO $value): int
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
     * Soft delete berdasarkan id.
     */
    #[Override]
    public function remove(string $id): int
    {
        try {
            return $this->model->query()
                ->where('id', $id)
                ->delete();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Update stok saat ini berdasarkan id.
     */
    #[Override]
    public function updateCurrentStock(string $id, float $value): int
    {
        try {
            return $this->model->query()
                ->where('id', $id)
                ->update(['current_stock' => $value]);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Reset all discounts.
     */
    #[Override]
    public function resetAllDiscounts(float $value): int
    {
        try {
            return $this->model->query()
                ->where('is_active', 1)
                ->update(['discount' => $value]);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data berdasarkan code.
     */
    #[Override]
    public function findByCode(string $code): ?ProductEntity
    {
        try {
            $result = $this->model->query()
                ->with('category')
                ->where('code', $code)
                ->first();

            if ($result === null) {
                return null;
            }

            return $this->mapModelToProductEntity(
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
     * Pemetaan class Model ke class CategoryProductValue.
     */
    private function mapModelToCategoryProductValue(Category $model): CategoryProductValue
    {
        return new CategoryProductValue(
            id: $model->id,
            code: $model->code,
            name: $model->name,
            isActive: $model->is_active === 1,
        );
    }

    /**
     * Pemetaan class Model ke class ProductEntity.
     */
    private function mapModelToProductEntity(Product $model): ProductEntity
    {
        return new ProductEntity(
            id: $model->id,
            category: $this->mapModelToCategoryProductValue($model->category),
            code: $model->code,
            name: $model->name,
            image: $model->image,
            minimumStock: parse_float($model->minimum_stock),
            currentStock: parse_float($model->current_stock),
            discount: parse_float($model->discount),
            isActive: $model->is_active === 1,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
