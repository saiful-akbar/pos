<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\SaleReturnCartDTO;
use App\Base\Repository;
use App\Domain\Sale\Entities\SaleReturnCartEntity;
use App\Domain\Sale\Repositories\SaleReturnCartRepositoryInterface;
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
     * Mengambil data berdasarkan ID.
     */
    #[Override]
    public function findById(string $id): ?SaleReturnCartEntity
    {
        try {
            // Ambil data keranjang pengembalian beserta relasi user.
            $model = $this->model
                ->newQuery()
                ->with(['user'])
                ->find($id);

            return $model !== null
                ? SaleReturnCartEntity::fromModel($model)
                : null;
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data berdasarkan kolom dan value.
     */
    #[Override]
    public function find(string $column, string|int|float|bool|null $value): ?SaleReturnCartEntity
    {
        try {
            $model = $this->model
                ->newQuery()
                ->with(['user'])
                ->where($column, $value)
                ->first();

            return $model !== null
                ? SaleReturnCartEntity::fromModel($model)
                : null;
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Membuat data sale return cart baru.
     */
    #[Override]
    public function create(SaleReturnCartDTO $value): SaleReturnCartEntity
    {
        try {
            $model = $this->model
                ->newQuery()
                ->create($value->toArray());

            $model->load(['user']);

            return SaleReturnCartEntity::fromModel($model);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghapus data sale return cart berdasarkan user_id.
     */
    #[Override]
    public function deleteByUserId(string $userId): int
    {
        try {
            return $this->model
                ->newQuery()
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
     * Menghapus permanen data sale return cart berdasarkan ID.
     */
    #[Override]
    public function deleteById(string $id): int
    {
        try {
            return $this->model
                ->newQuery()
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
     * Update data sale return cart berdasarkan ID.
     */
    #[Override]
    public function update(string $id, SaleReturnCartDTO $value): int
    {
        try {
            return $this->model
                ->newQuery()
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
}
