<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Purchase\DTOs\CreatePurchasePaymentDTO;
use App\Base\Repository;
use App\Domain\Purchase\Entities\PurchasePaymentEntity;
use App\Domain\Purchase\Enums\PaymentMethodEnum;
use App\Domain\Purchase\Repositories\PurchasePaymentRepositoryInterface;
use App\Domain\Purchase\ValueObjects\UserPurchaseValue;
use App\Models\PurchasePayment;
use Illuminate\Database\Eloquent\Builder;
use Override;

class PurchasePaymentRepository extends Repository implements PurchasePaymentRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new PurchasePayment());
    }

    /**
     * Membuat (insert) data pembayaran pembelian.
     */
    #[Override]
    public function create(CreatePurchasePaymentDTO $dto): PurchasePaymentEntity
    {
        try {
            $data = $this->model->query()->create($dto->toArray());

            return $this->mapModelToPurchasePaymentEntity(
                model: $data->load('user'),
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
     * Mengambil daftar pembayaran pembelian berdasarkan purchase_id.
     *
     * @return PurchasePaymentEntity[]
     */
    #[Override]
    public function getByPurchaseId(string $purchaseId): array
    {
        try {
            return $this->model->query()
                ->with('user')
                ->where('purchase_id', $purchaseId)
                ->get()
                ->map(fn($data) => $this->mapModelToPurchasePaymentEntity($data))
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
     * Mengambil daftar pembayaran berdasarkan id pembelian
     * dalam format query builder.
     */
    #[Override]
    public function getByPurchaseIdQuery(string $purchaseId): Builder
    {
        try {
            return $this->model->query()
                ->with('user')
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
     * Menghapus permanen data pembayaran.
     */
    #[Override]
    public function deleteById(string $id): void
    {
        try {
            $this->model->query()
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
     * Mengambil data pembayaran pembelian berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?PurchasePaymentEntity
    {
        try {
            $data = $this->model->query()->find($id);

            return $data === null ? null : $this->mapModelToPurchasePaymentEntity($data);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghitung total amount berdasarkan pirchase_id.
     *
     * @param string $purchaseId
     * @return integer
     */
    #[Override]
    public function sumAmountByPurchaseId(string $purchaseId): int
    {
        try {
            return (int) $this->model->query()
                ->where('purchase_id', $purchaseId)
                ->sum('amount');
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * pemetaan model ke PurchasePaymentEntity.
     */
    private function mapModelToPurchasePaymentEntity(PurchasePayment $model): PurchasePaymentEntity
    {
        return new PurchasePaymentEntity(
            id: $model->id,
            purchaseId: $model->purchase_id,
            user: new UserPurchaseValue(
                id: $model->user->id,
                username: $model->user->username,
                fullName: $model->user->full_name,
                photo: $model->user->photo,
                isActive: $model->user->is_active === 1
            ),
            paymentDate: parse_date_time_immutable($model->payment_date),
            paymentMethod: PaymentMethodEnum::from($model->payment_method),
            amount: $model->amount,
            referenceNumber: $model->reference_number,
            notes: $model->notes,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at
        );
    }
}
