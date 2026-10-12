<?php

declare(strict_types=1);

namespace App\Domain\Product\Repositories;

use App\Application\Product\DTOs\ProductDTO;
use App\Domain\Product\Entities\ProductEntity;
use Illuminate\Database\Eloquent\Builder;

interface ProductRepositoryInterface
{
    /**
     * Mengambil daftar dalam format query builder.
     */
    public function getQuery(): Builder;

    /**
     * Create (insert).
     */
    public function create(ProductDTO $value): ProductEntity;

    /**
     * Mengambil data berdasarkan id.
     */
    public function findById(string $id): ?ProductEntity;

    /**
     * Update
     */
    public function update(string $id, ProductDTO $value): int;

    /**
     * Soft delete berdasarkan id.
     */
    public function remove(string $id): int;

    /**
     * Update stok saat ini berdasarkan id.
     */
    public function updateCurrentStock(string $id, float $value): int;
}
