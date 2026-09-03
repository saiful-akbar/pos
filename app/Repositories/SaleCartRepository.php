<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\CreateSaleCartDTO;
use App\Base\Repository;
use App\Domain\Sale\Entities\SaleCartEntity;
use App\Domain\Sale\Repositories\SaleCartRepositoryInterface;
use App\Domain\Sale\ValueObjects\UserSaleValue;
use App\Models\SaleCart;
use Override;

class SaleCartRepository extends Repository implements SaleCartRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new SaleCart());
    }

    /**
     * Mengambil data keranjang penjualan berdasarkan id user.
     */
    #[Override]
    public function findByUserId(string $userId): ?SaleCartEntity
    {
        try {
            $data = $this->model->query()
                ->where('user_id', $userId)
                ->first();

            return $data === null ? null : $this->mapModelToSaleCartEntity(model: $data);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Membuat kerangjang belanja baru
     */
    #[Override]
    public function create(CreateSaleCartDTO $dto): SaleCartEntity
    {
        try {
            return $this->mapModelToSaleCartEntity(
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
     * Menghapus keranjang belanja.
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
     * Pemetaan model ke SaleCartEntity.
     */
    private function mapModelToSaleCartEntity(SaleCart $model): SaleCartEntity
    {
        return new SaleCartEntity(
            id: $model->id,
            user: new UserSaleValue(
                id: $model->user->id,
                username: $model->user->username,
                fullName: $model->user->full_name,
                photo: $model->user->photo,
            ),
            isPending: $model->is_pending === 1,
            transactionDate: parse_date_time_immutable($model->trransaction_date),
            subtotal: $model->subtotal,
            discount: $model->discount,
            tax: $model->tax,
            grandTotal: $model->frand_total,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at
        );
    }
}
