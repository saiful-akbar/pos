<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Product\DTOs\CreateProductDTO;
use App\Application\Product\DTOs\UpdateProductDTO;
use App\Base\Repository;
use App\Domain\Product\Entities\ProductDetailEntity;
use App\Domain\Product\Entities\ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Product\ValueObjects\CategoryProductValue;
use App\Domain\Product\ValueObjects\UnitProductValue;
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
     * Mengambil daftar produk dalam format query builder.
     */
    #[Override]
    public function getQuery(): Builder
    {
        try {
            return $this->model->query()->with(['category', 'unit']);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data produk berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?ProductDetailEntity
    {
        try {
            $product = $this->model->query()
                ->with(['category', 'unit'])
                ->find($id);

            return $product === null ? null : $this->mapModelToProductDetailEntity($product);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Membuat produk baru
     */
    #[Override]
    public function create(CreateProductDTO $dto): ProductEntity
    {
        try {
            return $this->mapModelToProductEntity(
                model: $this->model->query()->create($dto->toArray())
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
     * Update produk.
     */
    #[Override]
    public function update(UpdateProductDTO $dto): void
    {
        try {
            $this->model->query()
                ->where('id', '=', $dto->id)
                ->update($dto->toArray());
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Reset discount.
     */
    #[Override]
    public function resetDiscount(int $discount): void
    {
        try {
            $this->model->query()
                ->where('is_active', '=', 1)
                ->update(['discount' => $discount]);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Perbarui gambar
     */
    #[Override]
    public function updateImage(string $id, ?string $path = null): void
    {
        try {
            $this->model->query()
                ->where('id', '=', $id)
                ->update(['image' => $path]);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Megambil data produk berdasarkan kode atau barcode.
     *
     * @param string $code
     * @return ProductEntity|null
     */
    #[Override]
    public function findByCodeOrBarcode(string $code): ?ProductEntity
    {
        try {
            $data = $this->model->query()
                ->where('code', $code)
                ->orWhere('barcode', $code)
                ->first();

            return is_null($data) ? null : $this->mapModelToProductEntity(model: $data);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Pemetaan class model ke class ProductEntity
     */
    private function mapModelToProductEntity(Product $model): ProductEntity
    {
        return new ProductEntity(
            id: $model->id,
            categoryId: $model->category_id,
            unitId: $model->unit_id,
            code: $model->code,
            barcode: $model->barcode,
            name: $model->name,
            sellingPrice: $model->selling_price,
            discount: $model->discount,
            minimumStock: $model->minimum_stock,
            currentStock: $model->current_stock,
            isActive: $model->is_active === 1,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }

    /**
     * Pemetaan class model ke class ProductDetailEntity
     */
    private function mapModelToProductDetailEntity(Product $model): ProductDetailEntity
    {
        return new ProductDetailEntity(
            id: $model->id,
            category: new CategoryProductValue(
                id: $model->category->id,
                code: $model->category->code,
                name: $model->category->name,
                isActive: $model->category->is_active === 1,
            ),
            unit: new UnitProductValue(
                id: $model->unit->id,
                code: $model->unit->code,
                name: $model->unit->name,
                isActive: $model->unit->is_active === 1,
            ),
            code: $model->code,
            barcode: $model->barcode,
            name: $model->name,
            sellingPrice: $model->selling_price,
            discount: $model->discount,
            minimumStock: $model->minimum_stock,
            currentStock: $model->current_stock,
            isActive: $model->is_active === 1,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
