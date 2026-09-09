<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Discount\DTOs\DiscountDTO;
use App\Base\Repository;
use App\Domain\Discount\Entities\DiscountEntity;
use App\Domain\Discount\Repositories\DiscountRepositoryInterface;
use App\Models\Discount;
use Illuminate\Database\Eloquent\Builder;
use Override;

class DiscountRepository extends Repository implements DiscountRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Discount());
    }

    /**
     * Mengambil daftar diskon dalam format query builder
     */
    #[Override]
    public function getAllQuery(): Builder
    {
        try {
            return $this->model->query();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil daftar discount hanya yang masih aktif
     * dalam format query builder.
     */
    #[Override]
    public function getActiveQuery(): Builder
    {
        $now = date('Y-m-d H:i:s');

        try {
            return $this->model->query()
                ->where('effective_date', '<=', $now)
                ->where(function (Builder $query) use ($now): void {
                    $query->where('expiration_date', '>=', $now)
                        ->orWhereNull('expiration_date');
                });
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Membuat (insert) diskon baru.
     */
    #[Override]
    public function create(DiscountDTO $dto): DiscountEntity
    {
        try {
            return $this->mapModelToDiscountEntity(
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
     * Memperbarui data diskon.
     */
    #[Override]
    public function update(string $id, DiscountDTO $dto): int
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
     * Mengambil 1 data diskon berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?DiscountEntity
    {
        try {
            $data = $this->model->query()->find($id);

            if ($data === null) {
                return null;
            }

            return $this->mapModelToDiscountEntity(
                model: $data,
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
     * hapus data diskon.
     */
    #[Override]
    public function removeById(string $id): int
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
     * Pemetaan class model ke DiscountEntity.
     */
    private function mapModelToDiscountEntity(Discount $model): DiscountEntity
    {
        return new DiscountEntity(
            id: $model->id,
            discount: parse_float($model->discount),
            name: $model->name,
            effectiveDate: parse_date_time_immutable($model->effective_date),
            expirationDate: parse_date_time_immutable($model->expiration_date),
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
