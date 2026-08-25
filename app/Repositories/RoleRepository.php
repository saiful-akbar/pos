<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Role\DTOs\CreateRoleDTO;
use App\Base\Repository;
use App\Domain\Role\Entities\RoleEntity;
use App\Domain\Role\Repositories\RoleRepositoryInterface;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
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
     *
     * @return Builder
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
     *
     * @param CreateRoleDTO $dto
     * @return RoleEntity
     */
    #[Override]
    public function create(CreateRoleDTO $dto): RoleEntity
    {
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

            return $this->mapModelTpRoleEntity($role);
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
     *
     * @param Role $model
     * @return RoleEntity
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
