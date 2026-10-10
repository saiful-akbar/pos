<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Unit\DTOs\UnitDTO;
use App\Base\Repository;
use App\Domain\Unit\Entities\UnitEntity;
use App\Domain\Unit\Repositories\UnitRepositoryInterface;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Override;

final class UnitRepository extends Repository implements UnitRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Unit());
    }

    /**
     * Mengambil daftar data dalam format query builder.
     */
    #[Override]
    public function getQuery(): Builder
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
     * Menambah (insert).
     */
    #[Override]
    public function create(UnitDTO $value): UnitEntity
    {
        try {
            return $this->mapModelToUnitEntity(
                model: $this->model->query()->create($value->toArray()),
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
    public function findById(string $id): ?UnitEntity
    {
        try {
            $result = $this->model->query()->find($id);

            if ($result === null) {
                return null;
            }

            return $this->mapModelToUnitEntity(
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
     * Update
     */
    #[Override]
    public function update(string $id, UnitDTO $value): int
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
     * Soft delete
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
     * Pemetaan class Model ke class UnitEntity.
     */
    private function mapModelToUnitEntity(Unit $model): UnitEntity
    {
        return new UnitEntity(
            id: $model->id,
            code: $model->code,
            name: $model->name,
            isActive: $model->is_active === 1,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
