<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Purchase\DTOs\CreatePurchaseDetailDTO;
use App\Base\Repository;
use App\Domain\Purchase\Entities\PurchaseDetailEntity;
use App\Domain\Purchase\Repositories\PurchaseDetailRepositoryInterface;
use App\Domain\Purchase\ValueObjects\ProductPurchaseValue;
use App\Models\PurchaseDetail;
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
     * Pemetaan model ke PurchaseDetailEntity.
     */
    private function mapModelToPurchaseDetailEntity(PurchaseDetail $model): PurchaseDetailEntity
    {
        return new PurchaseDetailEntity(
            id: $model->id,
            purchaseId: $model->purchase_id,
            product: new ProductPurchaseValue(
                id: $model->product->id,
                code: $model->product->code,
                barcode: $model->product->barcode,
                name: $model->product->name,
                image: $model->product->image
            ),
            quantity: $model->quantity,
            purchasePrice: $model->purchase_price,
            subtotalPrice: $model->subtotal_price,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at
        );
    }
}
