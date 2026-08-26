<?php

declare(strict_types=1);

namespace App\Domain\Category\Repositories;

use App\Application\Category\DTOs\CreateCategoryDTO;
use App\Application\Category\DTOs\UpdateCategoryDTO;
use App\Domain\Category\Entities\CategoryEntity;
use Illuminate\Database\Eloquent\Builder;

interface CategoryRepositoryInterface
{
    /**
     * Mengambil daftar katgori dalam format query builder.
     */
    public function getQuery(): Builder;

    /**
     * Mengambil data kategori berdasarkan id.
     */
    public function findById(string $id): ?CategoryEntity;

    /**
     * Membuat kategory baru
     */
    public function create(CreateCategoryDTO $dto): CategoryEntity;

    /**
     * Update category.
     */
    public function update(UpdateCategoryDTO $dto): void;

    /**
     * Mengambil semua data kategori
     *
     * @return CategoryEntity[]
     */
    public function getAll(): array;
}
