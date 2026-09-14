<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Purchase\DTOs\CreatePurchaseCartDTO;
use App\Application\Purchase\DTOs\UpdateQuantityAndPricePurchaseCartDTO;
use App\Base\Repository;
use App\Domain\Purchase\Entities\PurchaseCartEntity;
use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;
use App\Domain\Purchase\ValueObjects\UserPurchaseValue;
use App\Models\PurchaseCart;
use Illuminate\Database\Eloquent\Model;
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
     * Mengambil data cart berdasarkan user id.
     */
    #[Override]
    public function findByUserId(string $userId): ?PurchaseCartEntity
    {
        try {
            $data = $this->model->query()
                ->where('user_id', $userId)
                ->first();

            if ($data === null) {
                return null;
            }

            return $this->mapModelToPurchaseCartEntity(model: $data);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Membuat (insert) data cart baru.
     */
    #[Override]
    public function create(CreatePurchaseCartDTO $dto): PurchaseCartEntity
    {
        try {
            $data = $this->model->query()->create($dto->toArray());

            return $this->mapModelToPurchaseCartEntity(model: $data);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Update total quantity dan total price purchase cart.
     */
    #[Override]
    public function updateQuantityAndPriceById(UpdateQuantityAndPricePurchaseCartDTO $dto): void
    {
        try {
            $this->model->query()
                ->where('id', $dto->id)
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
     * Hapus purchase cart.
     */
    #[Override]
    public function deleteByUserId(string $userId): void
    {
        try {
            $this->model->query()
                ->where('user_id', $userId)
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
     * Pemetaan model ke PurchaseCartEntity
     */
    private function mapModelToPurchaseCartEntity(Model $model): PurchaseCartEntity
    {
        return new PurchaseCartEntity(
            id: $model->id,
            user: new UserPurchaseValue(
                id: $model->user->id,
                username: $model->user->username,
                fullName: $model->user->full_name,
                photo: $model->user->photo,
                isActive: $model->user->is_active === 1
            ),
            purchaseDate: parse_date_time_immutable($model->purchase_date),
            totalQuantity: parse_float($model->total_quantity),
            totalPrice: parse_float($model->total_price),
            createdAt: $model->createdAt,
            updatedAt: $model->updatedAt,
            deletedAt: $model->deletedAt
        );
    }
}
