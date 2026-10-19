<?php

declare(strict_types=1);

namespace App\Domain\Price\Repositories;

use App\Application\Price\DTOs\PriceDTO;
use App\Domain\Price\Entities\PriceEntity;
use Illuminate\Database\Eloquent\Builder;

interface PriceRepositoryInterface
{
    /**
     * Mengambil daftar dalam format query builder.
     */
    public function getQueryByProductId(string $productId): Builder;

    /**
     * Mengambil data berdasarkan id.
     */
    public function findById(string $id): ?PriceEntity;

    /**
     * Mengambil data berdasarkan product_id dan unit_id.
     */
    public function findByProductIdAndUnitId(string $productId, ?string $unitId = null, ?bool $isDefault = null): ?PriceEntity;

    /**
     * Memeriksa apakah data ada berdasarkan product_id dan unit_id.
     */
    public function existsByProductIdAndUnitId(string $productId, string $unitId, ?bool $isDefault = null): bool;

    /**
     * Memeriksa apakah data terdapat harage default berdasarkan product id.
     */
    public function existsDefaultByProductId(string $productId): bool;

    /**
     * Create (insert).
     */
    public function create(PriceDTO $value): PriceEntity;

    /**
     * Hapus permanen.
     */
    public function delete(string $id): int;

    /**
     * Mengambil daftar berdasarkan product_id.
     *
     * @return PriceEntity[]
     */
    public function getByProductId(string $productId): array;
}
