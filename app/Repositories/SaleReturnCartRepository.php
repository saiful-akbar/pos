<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\SaleReturnCartDTO;
use App\Base\Repository;
use App\Domain\Sale\Entities\SaleReturnCartEntity;
use App\Domain\Sale\Repositories\SaleReturnCartRepositoryInterface;
use App\Domain\Sale\ValueObjects\UserSaleValue;
use App\Models\SaleReturnCart;
use Override;

class SaleReturnCartRepository extends Repository implements SaleReturnCartRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new SaleReturnCart());
    }

    /**
     * Membuat data keranjang pengembalian penjualan.
     */
    #[Override]
    public function create(SaleReturnCartDTO $dto): SaleReturnCartEntity
    {
        try {
            $data = $this->model->query()->create($dto->toArray());

            return $this->mapModelToSaleReturnCartEntity(
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
     * Menghapus permanen data kerangjang pengembalian belanja.
     */
    #[Override]
    public function deleteById(string $id): int
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
     * Memperbarui data keranjang pengambalian belanja.
     */
    #[Override]
    public function update(string $id, SaleReturnCartDTO $dto): int
    {
        try {
            return $this->model->query()
                ->where('id', $id)
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
     * Pemetaan class model ke class SaleReturnCartEntity.
     */
    private function mapModelToSaleReturnCartEntity(SaleReturnCart $model): SaleReturnCartEntity
    {
        return new SaleReturnCartEntity(
            id: $model->id,
            saleId: $model->sale_id,
            user: new UserSaleValue(
                id: $model->user->id,
                username: $model->user->username,
                fullName: $model->user->full_name,
                photo: $model->user->photo
            ),
            transactionDate: parse_date_time_immutable($model->transaction_date),
            totalQuantity: $model->total_quantity,
            totalAmount: $model->total_amount,
            notes: $model->notes,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
