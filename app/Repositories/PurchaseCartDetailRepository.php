<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Purchase\DTOs\PurchaseCartDetailDTO;
use App\Base\Repository;
use App\Domain\Purchase\Entities\PurchaseCartDetailEntity;
use App\Domain\Purchase\Repositories\PurchaseCartDetailRepositoryInterface;
use App\Domain\Purchase\ValueObjects\ProductPurchaseValue;
use App\Domain\Purchase\ValueObjects\UnitPurchaseValue;
use App\Models\Product;
use App\Models\PurchaseCartDetail;
use App\Models\Unit;
use Override;

class PurchaseCartDetailRepository extends Repository implements PurchaseCartDetailRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new PurchaseCartDetail());
    }

    /**
     * Mengambil data berdasarkan product_id.
     */
    #[Override]
    public function findByPurchaseCartIdAndProductId(string $purchaseCartId, string $productId): ?PurchaseCartDetailEntity
    {
        try {
            $result = $this->model->query()
                ->with(['product', 'unit'])
                ->where('purchase_cart_id', $purchaseCartId)
                ->where('product_id', $productId)
                ->first();

            if ($result === null) {
                return null;
            }

            return $this->mapModelToPurchaseCartDetailEntity(
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
     * Mengambil data berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?PurchaseCartDetailEntity
    {
        try {
            $result = $this->model->query()
                ->with(['product', 'unit'])
                ->find($id);

            if ($result === null) {
                return null;
            }

            return $this->mapModelToPurchaseCartDetailEntity(
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
     * Create (insert)
     */
    #[Override]
    public function create(PurchaseCartDetailDTO $values): PurchaseCartDetailEntity
    {
        try {
            $result = $this->model->query()->create($values->toArray());

            return $this->mapModelToPurchaseCartDetailEntity(
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
     * Update
     */
    #[Override]
    public function update(string $id, PurchaseCartDetailDTO $values): int
    {
        try {
            return $this->model->query()
                ->where('id', $id)
                ->update($values->toArray());
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Delete
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
     * Menghitung total produk berdasarkan purchase_cart_id.
     */
    #[Override]
    public function countProductByPurchaseCartId(string $purchaseCartId): float
    {
        try {
            $result = $this->model->query()
                ->where('purchase_cart_id', $purchaseCartId)
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
     * Menghitung total quantity berdasarkan purchase_cart_id.
     */
    #[Override]
    public function sumQuantityByPurchaseCartId(string $purchaseCartId): float
    {
        try {
            $result = $this->model->query()
                ->where('purchase_cart_id', $purchaseCartId)
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
     * Menghitung total price (subtotal) berdasarkan purchase_cart_id.
     */
    #[Override]
    public function sumSubtotalByPurchaseCartId(string $purchaseCartId): float
    {
        try {
            $result = $this->model->query()
                ->where('purchase_cart_id', $purchaseCartId)
                ->sum('subtotal');

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
     * Mengambil daftar berdasarkan purchase_cart_id.
     *
     * @return PurchaseCartDetailEntity[]
     */
    #[Override]
    public function getByPurchaseCartId(string $purchaseCartId): array
    {
        try {
            return $this->model->query()
                ->with(['product', 'unit'])
                ->where('purchase_cart_id', $purchaseCartId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn ($data) => $this->mapModelToPurchaseCartDetailEntity($data))
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
     * Pemetaan class Model ke class ProductPurchaseValue
     */
    private function mapModelToProductPurchaseValue(Product $model): ProductPurchaseValue
    {
        return new ProductPurchaseValue(
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
     * Pemetaan class Model ke class UnitPurchaseValue
     */
    private function mapModelToUnitPurchaseValue(Unit $model): UnitPurchaseValue
    {
        return new UnitPurchaseValue(
            id: $model->id,
            code: $model->code,
            name: $model->name,
            isActive: $model->is_active === 1,
        );
    }

    /**
     * Pemetaan class Model ke class PurchaseCartDetailEntity
     */
    private function mapModelToPurchaseCartDetailEntity(PurchaseCartDetail $model): PurchaseCartDetailEntity
    {
        return new PurchaseCartDetailEntity(
            id: $model->id,
            purchaseCartId: $model->purchase_cart_id,
            product: $this->mapModelToProductPurchaseValue($model->product),
            unit: $this->mapModelToUnitPurchaseValue($model->unit),
            multiplesQuantity: parse_float($model->multiples_quantity),
            quantity: parse_float($model->quantity),
            price: parse_float($model->price),
            subtotal: parse_float($model->subtotal),
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
