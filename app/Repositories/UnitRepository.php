<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Unit\DTOs\CreateUnitDTO;
use App\Application\Unit\DTOs\UpdateUnitDTO;
use App\Base\Repository;
use App\Domain\Unit\Entities\UnitEntity;
use App\Domain\Unit\Repositories\UnitRepositoryInterface;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Override;

class UnitRepository extends Repository implements UnitRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Unit());
    }

    /**
     * Mengambil daftar unit dalam format query builder.
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
     * Mengambil data unit berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?UnitEntity
    {
        try {
            $data = $this->model->query()->find($id);

            return $data === null ? null : $this->mapModelToUnitEntity($data);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Membuat unit baru
     */
    #[Override]
    public function create(CreateUnitDTO $dto): UnitEntity
    {
        try {
            return $this->mapModelToUnitEntity(
                $this->model->query()->create($dto->toArray())
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
     * Update Unit.
     */
    #[Override]
    public function update(UpdateUnitDTO $dto): void
    {
        try {
            $this->model->query()
                ->where('id', '=', $dto->id)
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
     * Mengambil semua data unit
     *
     * @return UnitEntity[]
     */
    #[Override]
    public function getAll(): array
    {
        try {
            return $this->model->query()
                ->get()
                ->map(fn ($unit) => $this->mapModelToUnitEntity($unit))
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
     * Pemetaan model ke UnitEntity.
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
