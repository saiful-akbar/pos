<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Purchase\DTOs\PurchaseDetailDTO;
use App\Base\Repository;
use App\Domain\Purchase\Entities\PurchaseDetailEntity;
use App\Domain\Purchase\Repositories\PurchaseDetailRepositoryInterface;
use App\Domain\Purchase\ValueObjects\ProductPurchaseValue;
use App\Domain\Purchase\ValueObjects\UnitPurchaseValue;
use App\Models\Product;
use App\Models\PurchaseDetail;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Override;

class PurchaseDetailRepository extends Repository implements PurchaseDetailRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new PurchaseDetail());
    }

    /**
     * Mengambil daftar berdasarkan purchase_id.
     *
     * @return PurchaseDetailEntity[]
     */
    #[Override]
    public function getByPurchaseId(string $purchaseId): array
    {
        try {
            return $this->model->query()
                ->with(['product', 'unit'])
                ->where('purchase_id', $purchaseId)
                ->get()
                ->map(fn ($data) => $this->mapModelToPurchaseDetailEntity($data))
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
     * Mengambil daftar berdasarkan purchase_id dalam format query builder.
     */
    #[Override]
    public function getQueryByPurchaseId(string $purchaseId): Builder
    {
        try {
            return $this->model->query()
                ->with(['product', 'unit'])
                ->where('purchase_id', $purchaseId);
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
    public function findById(string $id): ?PurchaseDetailEntity
    {
        try {
            $result = $this->model->query()
                ->with(['product', 'unit'])
                ->find($id);

            return $result === null ? null : $this->mapModelToPurchaseDetailEntity(model: $result);
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
    public function create(PurchaseDetailDTO $value): PurchaseDetailEntity
    {
        try {
            $result = $this->model->query()->create($value->toArray());

            return $this->mapModelToPurchaseDetailEntity(
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
     * Remove (soft delete).
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
     * Remove (soft delete) berdasarkan purchase_id.
     */
    #[Override]
    public function removeByPurchaseId(string $purchaseId): int
    {
        try {
            return $this->model->query()
                ->where('purchase_id', $purchaseId)
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
     * Pemetaan class Model ke class PurchaseDetailEntity
     */
    private function mapModelToPurchaseDetailEntity(PurchaseDetail $model): PurchaseDetailEntity
    {
        return new PurchaseDetailEntity(
            id: $model->id,
            purchaseId: $model->purchase_id,
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
