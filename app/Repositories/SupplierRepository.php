<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Supplier\DTOs\CreateSupplierDTO;
use App\Application\Supplier\DTOs\UpdateSupplierDTO;
use App\Base\Repository;
use App\Domain\Supplier\Entities\SupplierEntity;
use App\Domain\Supplier\Repositories\SupplierRepositoryInterface;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
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
     * Mengambil daftar supplier dalam format query builder.
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
     * Mengambil data supplier berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?SupplierEntity
    {
        try {
            $data = $this->model->query()->find($id);

            return $data === null ? null : $this->mapModelToSupplierEntity($data);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Membuat supplier baru
     */
    #[Override]
    public function create(string $code, CreateSupplierDTO $dto): SupplierEntity
    {
        try {
            $data = $this->model->query()->create([
                'code' => $code,
                ...$dto->toArray(),
            ]);

            return $this->mapModelToSupplierEntity($data);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Update Supplier.
     */
    #[Override]
    public function update(UpdateSupplierDTO $dto): void
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
     * Mengambil semua data supplier
     *
     * @return SupplierEntity[]
     */
    #[Override]
    public function getAll(): array
    {
        try {
            return $this->model->query()
                ->get()
                ->map(fn ($supplier) => $this->mapModelToSupplierEntity($supplier))
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
     * Mengambil nilai code tertinggi.
     */
    #[Override]
    public function findMaxCode(): ?string
    {
        try {
            return $this->model->query()
                ->withTrashed()
                ->whereYear('created_at', '=', DB::raw('year(current_date)'))
                ->orderBy('code', 'desc')
                ->lockForUpdate()
                ->value('code');
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil daftar supplier yang masih aktif.
     *
     * @return SupplierEntity[]
     */
    #[Override]
    public function getActive(): array
    {
        try {
            return $this->model->query()
                ->where('is_active', 1)
                ->orderBy('name', 'asc')
                ->get()
                ->map(fn ($supplier) => $this->mapModelToSupplierEntity($supplier))
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
     * Pemetaan model ke SupplierEntity.
     */
    private function mapModelToSupplierEntity(Supplier $model): SupplierEntity
    {
        return new SupplierEntity(
            id: $model->id,
            code: $model->code,
            name: $model->name,
            phoneNumber: $model->phone_number,
            address: $model->address,
            notes: $model->notes,
            isActive: $model->is_active === 1,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
