<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\SaleCartDTO;
use App\Base\Repository;
use App\Domain\Sale\Entities\SaleCartEntity;
use App\Domain\Sale\Repositories\SaleCartRepositoryInterface;
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
     * Mengambil data berdasarkan user_id.
     */
    #[Override]
    public function findByUserId(string $userId): ?SaleCartEntity
    {
        try {
            $result = $this->model->query()
                ->with('user')
                ->where('user_id', $userId)
                ->first();

            return $result === null ? null : SaleCartEntity::fromModel($result);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    #[Override]
    public function find(string $column, string $value): ?SaleCartEntity
    {
        try {
            $result = $this->model->query()
                ->with('user')
                ->where($column, $value)
                ->first();

            return $result === null ? null : SaleCartEntity::fromModel($result);
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
    public function create(SaleCartDTO $value): SaleCartEntity
    {
        try {
            $result = $this->model->query()->create($value->toArray());

            return SaleCartEntity::fromModel($result->load('user'));
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Hapus permanen.
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
     * Update.
     */
    #[Override]
    public function update(string $id, SaleCartDTO $value): int
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
}
