<?php

declare(strict_types=1);

namespace App\Domain\Unit\Repositories;

use App\Application\Unit\DTOs\CreateUnitDTO;
use App\Application\Unit\DTOs\UpdateUnitDTO;
use App\Domain\Unit\Entities\UnitEntity;
use Illuminate\Database\Eloquent\Builder;

interface UnitRepositoryInterface
{
    /**
     * Mengambil daftar unit dalam format query builder.
     */
    public function getQuery(): Builder;

    /**
     * Mengambil data unit berdasarkan id.
     */
    public function findById(string $id): ?UnitEntity;

    /**
     * Membuat unit baru
     */
    public function create(CreateUnitDTO $dto): UnitEntity;

    /**
     * Update unit.
     */
    public function update(UpdateUnitDTO $dto): void;

    /**
     * Mengambil semua data unit
     *
     * @return UnitEntity[]
     */
    public function getAll(): array;
}
