<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Purchase\DTOs\PurchaseCartDTO;
use App\Base\Repository;
use App\Domain\Purchase\Entities\PurchaseCartEntity;
use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;
use App\Domain\Purchase\ValueObjects\UserPurchaseValue;
use App\Models\PurchaseCart;
use App\Models\User;
use Override;

class PurchaseCartRepository extends Repository implements PurchaseCartRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new PurchaseCart());
    }

    /**
     * Mengambil data berdasarkan user_id.
     */
    #[Override]
    public function findByUserId(string $userId): ?PurchaseCartEntity
    {
        try {
            $result = $this->model->query()
                ->with('user')
                ->where('user_id', $userId)
                ->first();

            if ($result === null) {
                return null;
            }

            return $this->mapModelToPurchaseCartEntity(
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
    public function findById(string $id): ?PurchaseCartEntity
    {
        try {
            $result = $this->model->query()
                ->with('user')
                ->where('id', $id)
                ->first();

            if ($result === null) {
                return null;
            }

            return $this->mapModelToPurchaseCartEntity(
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
     * Create (insert).
     */
    #[Override]
    public function create(PurchaseCartDTO $values): PurchaseCartEntity
    {
        try {
            $result = $this->model->query()->create($values->toArray());

            return $this->mapModelToPurchaseCartEntity(
                model: $result->load('user'),
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
     * Update.
     */
    #[Override]
    public function update(string $id, PurchaseCartDTO $values): int
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
     * Delete.
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
     * Pemetaan class Model ke class PurchaseCartEntity
     */
    private function mapModelToPurchaseCartEntity(PurchaseCart $model): PurchaseCartEntity
    {
        return new PurchaseCartEntity(
            id: $model->id,
            user: $this->mapModelToUserPurchaseValue($model->user),
            transactionDate: parse_date_time_immutable($model->transaction_date),
            totalProduct: parse_float($model->total_product),
            totalQuantity: parse_float($model->total_quantity),
            totalPrice: parse_float($model->total_price),
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
