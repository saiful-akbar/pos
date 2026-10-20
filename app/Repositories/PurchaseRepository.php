<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Purchase\DTOs\PurchaseDTO;
use App\Base\Repository;
use App\Domain\Purchase\Entities\PurchaseEntity;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use App\Domain\Purchase\Repositories\PurchaseRepositoryInterface;
use App\Domain\Purchase\ValueObjects\SupplierPurchaseValue;
use App\Domain\Purchase\ValueObjects\UserPurchaseValue;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use DateTimeInterface;
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
     * Create (insert)
     */
    #[Override]
    public function create(PurchaseDTO $value): PurchaseEntity
    {
        try {
            $result = $this->model->query()->create($value->toArray());

            return $this->mapModelToPurchaseEntity(
                model: $result->load(['user', 'supplier']),
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
     * Mengambil daftar pembelian
     *
     * @return PurchaseEntity[]
     */
    #[Override]
    public function getByDate(DateTimeInterface $fromDate, DateTimeInterface $toDate): array
    {
        try {
            return $this->model->query()
                ->with(['user', 'supplier'])
                ->whereBetween('transaction_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
                ->get()
                ->map(fn ($data) => $this->mapModelToPurchaseEntity($data))
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
     * Mengambil data berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?PurchaseEntity
    {
        try {
            $result = $this->model->query()
                ->with(['user', 'supplier'])
                ->find($id);

            return $result === null ? null : $this->mapModelToPurchaseEntity(
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
     * Update
     */
    #[Override]
    public function update(string $id, PurchaseDTO $value): int
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
     * Pemetaan class Model ke class UserPurchaseValue.
     */
    private function mapModelToUserPurchaseValue(User $model): UserPurchaseValue
    {
        return new UserPurchaseValue(
            id: $model->id,
            username: $model->username,
            fullName: $model->full_name,
            photo: $model->photo,
            isActive: $model->is_active === 1,
        );
    }

    /**
     * Pemetaan class Model ke class SupplierPurchaseValue.
     */
    private function mapModelToSupplierPurchaseValue(Supplier $model): SupplierPurchaseValue
    {
        return new SupplierPurchaseValue(
            id: $model->id,
            name: $model->name,
            phoneNumber: $model->phone_number,
            address: $model->address,
            isActive: $model->is_active === 1,
        );
    }

    /**
     * Pemetaan class Model ke class PurchaseEntity
     */
    private function mapModelToPurchaseEntity(Purchase $model): PurchaseEntity
    {
        return new PurchaseEntity(
            id: $model->id,
            user: $this->mapModelToUserPurchaseValue($model->user),
            supplier: $this->mapModelToSupplierPurchaseValue($model->supplier),
            transactionDate: parse_date_time_immutable($model->transaction_date),
            invoiceNumber: $model->invoice_number,
            totalProduct: parse_float($model->total_product),
            totalQuantity: parse_float($model->total_quantity),
            totalPrice: parse_float($model->total_price),
            alreadyPaid: parse_float($model->already_paid),
            paymentStatus: PaymentStatusEnum::from($model->payment_status),
            notes: $model->notes,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
