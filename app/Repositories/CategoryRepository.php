<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Category\DTOs\CreateCategoryDTO;
use App\Application\Category\DTOs\UpdateCategoryDTO;
use App\Base\Repository;
use App\Domain\Category\Entities\CategoryEntity;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Override;

class CategoryRepository extends Repository implements CategoryRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Category());
    }

    /**
     * Mengambil daftar katgori dalam format query builder.
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
     * Mengambil data kategori berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?CategoryEntity
    {
        try {
            $data = $this->model->query()->find($id);

            return $data === null ? null : $this->mapModelToCategoryEntity($data);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Membuat kategori baru
     */
    #[Override]
    public function create(CreateCategoryDTO $dto): CategoryEntity
    {
        try {
            return $this->mapModelToCategoryEntity(
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
     * Update kategori.
     */
    #[Override]
    public function update(UpdateCategoryDTO $dto): void
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
     * Mengambil semua data kategori
     *
     * @return CategoryEntity[]
     */
    #[Override]
    public function getAll(): array
    {
        try {
            return $this->model->query()
                ->get()
                ->map(fn ($category) => $this->mapModelToCategoryEntity($category))
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
     * Soft delete by id.
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
     * Pemetaan model ke CategoryEntity.
     */
    private function mapModelToCategoryEntity(Category $model): CategoryEntity
    {
        return new CategoryEntity(
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
