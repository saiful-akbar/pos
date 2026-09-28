<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Purchase\DTOs\CreatePurchaseDetailDTO;
use App\Application\Purchase\DTOs\GetPurchasesDTO;
use App\Base\Repository;
use App\Domain\Purchase\Entities\PurchaseDetailEntity;
use App\Domain\Purchase\Repositories\PurchaseDetailRepositoryInterface;
use App\Domain\Purchase\ValueObjects\CategoryProductPurchaseValue;
use App\Domain\Purchase\ValueObjects\ProductPurchaseValue;
use App\Domain\Purchase\ValueObjects\UnitProductPurchaseValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
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
     * Membuat (insert) data purchase detail.
     */
    #[Override]
    public function create(CreatePurchaseDetailDTO $dto): PurchaseDetailEntity
    {
        try {
            return $this->mapModelToPurchaseDetailEntity(
                model: $this->model->query()->create($dto->toArray()),
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
     * Mengambil daftar purchase detail
     * berdasarkan id purchase dalam format query builder.
     */
    #[Override]
    public function getByPurchaseIdQuery(string $purchaseId): Builder
    {
        try {
            return $this->model->query()
                ->with('product')
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
     * Mengambil daftar purchase detail berdasarkan id purchase.
     *
     * @return PurchaseDetailEntity[]
     */
    #[Override]
    public function getByPurchaseId(string $purchaseId): array
    {
        try {
            return $this->model->query()
                ->with('product')
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
     * Mengambil daftar detail pembelian berdasarkan rentang tanggal.
     */
    #[Override]
    public function getByDate(GetPurchasesDTO $params): array
    {
        try {
            $purchase = Purchase::query()
                ->select('id')
                ->whereBetween('purchase_date', [$params->fromDate->format('Y-m-d'), $params->toDate->format('Y-m-d')]);

            if ($params->paymentStatus !== null) {
                $purchase->where('payment_status', $params->paymentStatus->value);
            }

            $purchaseIds = $purchase->get()->pluck('id');

            return $this->model->query()
                ->with(['product.unit', 'product.category'])
                ->whereIn('purchase_id', $purchaseIds)
                ->get()
                ->map(fn (PurchaseDetail $data) => $this->mapModelToPurchaseDetailEntity($data))
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
     * Pemetaan model ke PurchaseDetailEntity.
     */
    private function mapModelToPurchaseDetailEntity(PurchaseDetail $model): PurchaseDetailEntity
    {
        return new PurchaseDetailEntity(
            id: $model->id,
            purchaseId: $model->purchase_id,
            product: $this->mapModelToProductPurchaseValue($model->product),
            quantity: parse_float($model->quantity),
            purchasePrice: parse_float($model->purchase_price),
            subtotalPrice: parse_float($model->subtotal_price),
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at
        );
    }

    /**
     * Pemetaan class Model ke class CategoryProductPurchaseValue
     */
    private function mapModelToCategoryProductPurchaseValue(Category $model): CategoryProductPurchaseValue
    {
        return new CategoryProductPurchaseValue(
            id: $model->id,
            code: $model->code,
            name: $model->name,
        );
    }

    /**
     * Pemetaan class Model ke class UnitProductPurchaseValue
     */
    private function mapModelToUnitProductPurchaseValue(Unit $model): UnitProductPurchaseValue
    {
        return new UnitProductPurchaseValue(
            id: $model->id,
            code: $model->code,
            name: $model->name,
        );
    }

    /**
     * Pemetaan class Model ke class ProductPurchaseValue
     */
    private function mapModelToProductPurchaseValue(Product $model): ProductPurchaseValue
    {
        return new ProductPurchaseValue(
            id: $model->id,
            code: $model->code,
            barcode: $model->barcode,
            name: $model->name,
            image: $model->image,
            category: $model->category === null ? null : $this->mapModelToCategoryProductPurchaseValue($model->category),
            unit: $model->unit === null ? null : $this->mapModelToUnitProductPurchaseValue($model->unit),
        );
    }
}
