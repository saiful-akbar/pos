<?php

declare(strict_types=1);

namespace App\Domain\Unit\Repositories;

use App\Application\Unit\DTOs\UnitDTO;
use App\Domain\Unit\Entities\UnitEntity;
use Illuminate\Database\Eloquent\Builder;

interface UnitRepositoryInterface
{
    /**
     * Mengambil daftar data dalam format query builder.
     */
    public function getQuery(): Builder;

    /**
     * Menambah (insert).
     */
    public function create(UnitDTO $value): UnitEntity;

    /**
     * Mengambil data berdasarkan id.
     */
    public function findById(string $id): ?UnitEntity;

    /**
     * Mengambil data unit berdasarkan kode.
     */
    public function findByCode(string $code): ?UnitEntity;

    /**
     * Update
     */
    public function update(string $id, UnitDTO $value): int;

    /**
     * Soft delete
     */
    public function remove(string $id): int;

    /**
     * Mengambil semua daftar
     *
     * @return UnitEntity[]
     */
    public function getAll(?bool $isActive = null): array;
}
