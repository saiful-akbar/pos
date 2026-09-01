<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Purchase\DTOs\CreatePurchaseDTO;
use App\Base\Repository;
use App\Domain\Purchase\Entities\PurchaseEntity;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use App\Domain\Purchase\Repositories\PurchaseRepositoryInterface;
use App\Domain\Purchase\ValueObjects\SupplierPurchaseValue;
use App\Domain\Purchase\ValueObjects\UserPurchaseValue;
use App\Models\Purchase;
use Override;

class PurchaseRepository extends Repository implements PurchaseRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Purchase());
    }

    /**
     * Membuat (insert) data pembalian.
     */
    #[Override]
    public function create(CreatePurchaseDTO $dto): PurchaseEntity
    {
        try {
            $data = $this->model->query()->create($dto->toArray());

            return $this->mapModelToPurchaseEntity(
                model: $data->load('user', 'supplier'),
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
     * Pemetaan model ke PurchaseEntity.
     */
    private function mapModelToPurchaseEntity(Purchase $model): PurchaseEntity
    {
        return new PurchaseEntity(
            id: $model->id,
            user: new UserPurchaseValue(
                id: $model->user->id,
                username: $model->user->username,
                fullName: $model->user->full_name,
                photo: $model->user->photo,
                isActive: $model->user->is_active === 1,
            ),
            supplier: new SupplierPurchaseValue(
                id: $model->supplier->id,
                code: $model->supplier->code,
                name: $model->supplier->name,
                phoneNumber: $model->supplier->phone_number,
                address: $model->supplier->address,
                notes: $model->supplier->notes,
                isActive: $model->supplier->is_active === 1
            ),
            purchaseDate: parse_date_time_immutable($model->purchase_date),
            invoiceNumber: $model->invoice_number,
            totalQuantity: $model->total_quantity,
            totalPrice: $model->total_price,
            paymentStatus: PaymentStatusEnum::tryFrom($model->payment_status),
            notes: $model->notes,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at
        );
    }
}
