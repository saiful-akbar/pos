<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Supplier\DTOs\SupplierDTO;
use App\Base\Repository;
use App\Domain\Supplier\Entities\SupplierEntity;
use App\Domain\Supplier\Repositories\SupplierRepositoryInterface;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Override;

class SupplierRepository extends Repository implements SupplierRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Supplier());
    }

    /**
     * Mengambil daftar dengan response query builder.
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
     * Mengambil data berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?SupplierEntity
    {
        try {
            $result = $this->model->query()->find($id);

            if ($result === null) {
                return null;
            }

            return $this->mapModelToSupplierEntity(model: $result);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Insert data
     */
    #[Override]
    public function create(SupplierDTO $value): SupplierEntity
    {
        try {
            $result = $this->model->query()->create($value->toArray());

            return $this->mapModelToSupplierEntity(
                model: $result
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
     * Update data
     */
    #[Override]
    public function update(string $id, SupplierDTO $value): int
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
     * Pemetaan class Model ke class SupplierEntity.
     */
    private function mapModelToSupplierEntity(Supplier $model): SupplierEntity
    {
        return new SupplierEntity(
            id: $model->id,
            name: $model->name,
            phoneNumber: $model->phone_number,
            address: $model->address,
            isActive: $model->is_active === 1,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at
        );
    }
}
