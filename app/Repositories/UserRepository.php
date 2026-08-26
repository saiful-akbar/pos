<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\User\DTOs\CreateUserDTO;
use App\Base\Repository;
use App\Domain\User\Entities\UserEntity;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\ValueObjects\RoleUserValue;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Override;

class UserRepository extends Repository implements UserRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new User());
    }

    /**
     * Mengambil data user dalam form query builder
     * untuk dataTable.
     */
    #[Override]
    public function GetQuery(): Builder
    {
        try {
            $columns = [
                'users.id as id',
                'users.username as username',
                'users.full_name as full_name',
                'users.photo as photo',
                'users.created_at as created_at',
                'users.updated_at as updated_at',
                'users.deleted_at as deleted_at',
                'roles.id as role_id',
                'roles.name as role_name',
                'roles.description as role_description',
            ];

            return $this->model->query()
                ->from('users')
                ->select($columns)
                ->withTrashed()
                ->leftJoin('roles', 'users.role_id', '=', 'roles.id');
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data user berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?UserEntity
    {
        try {
            $user = $this->model->query()
                ->with('role')
                ->find($id);

            return $user === null ? null : $this->mapModelToUserEntity($user);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Pemetaan model ke UserEntity.
     */
    private function mapModelToUserEntity(User $model): UserEntity
    {
        return new UserEntity(
            id: $model->id,
            username: $model->username,
            fullName: $model->full_name,
            photo: $model->photo,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
            role: new RoleUserValue(
                id: $model->role->id,
                name: $model->role->name,
                description: $model->role->description,
                createdAt: $model->role->created_at,
                updatedAt: $model->role->updated_at,
                deletedAt: $model->role->deleted_at,
            ),
        );
    }

    /**
     * Menambah user baru.
     *
     * @param CreateUserDTO $dto
     * @return UserEntity
     */
    #[Override]
    public function create(CreateUserDTO $dto): UserEntity
    {
        try {
            $user = $this->model->query()->create($dto->toArray());

            return $this->mapModelToUserEntity($user);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
