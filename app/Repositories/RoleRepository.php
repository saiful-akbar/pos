<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Role\DTOs\CreateRoleDTO;
use App\Application\Role\DTOs\UpdateRoleDTO;
use App\Base\Repository;
use App\Domain\Role\Entities\RoleEntity;
use App\Domain\Role\Repositories\RoleRepositoryInterface;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Override;

class RoleRepository extends Repository implements RoleRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Role());
    }

    /**
     * Mengambil semua daftar role dalam bentuk query builder.
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
     * Membuat (menyimpan) data role baru.
     */
    #[Override]
    public function create(CreateRoleDTO $dto): RoleEntity
    {
        DB::beginTransaction();

        try {
            $now = now();
            $role = $this->model->query()->create($dto->toArray());

            foreach ($dto->modules as $id => $module) {
                $role->modules()->attach($id, [
                    'create' => parse_bool($module['create'] ?? false),
                    'read' => parse_bool($module['read'] ?? false),
                    'update' => parse_bool($module['update'] ?? false),
                    'remove' => parse_bool($module['remove'] ?? false),
                    'delete' => parse_bool($module['delete'] ?? false),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            DB::commit();

            return $this->mapModelTpRoleEntity($role);
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data role berdasrkan id.
     */
    #[Override]
    public function findById(string $id): ?RoleEntity
    {
        try {
            $role = $this->model->find($id);

            return $role === null ? null : $this->mapModelTpRoleEntity($role);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Update role.
     */
    #[Override]
    public function update(UpdateRoleDTO $dto): void
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
     * Mengambil semua daftar
     *
     * @return RoleEntity[]
     */
    #[Override]
    public function getActive(): array
    {
        try {
            return $this->model->query()
                ->orderBy('name', 'asc')
                ->get()
                ->map(fn ($role) => $this->mapModelTpRoleEntity($role))
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
     * Mapping model ke class RoleEntity.
     */
    private function mapModelTpRoleEntity(Role $model): RoleEntity
    {
        return new RoleEntity(
            id: $model->id,
            name: $model->name,
            description: $model->description,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
