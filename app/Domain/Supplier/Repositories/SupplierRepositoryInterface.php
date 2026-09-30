<?php

declare(strict_types=1);

namespace App\Domain\Supplier\Repositories;

use App\Application\Supplier\DTOs\CreateSupplierDTO;
use App\Application\Supplier\DTOs\UpdateSupplierDTO;
use App\Domain\Supplier\Entities\SupplierEntity;
use Illuminate\Database\Eloquent\Builder;

interface SupplierRepositoryInterface
{
    /**
     * Mengambil daftar supplier dalam format query builder.
     */
    public function getQuery(): Builder;

    /**
     * Mengambil data supplier berdasarkan id.
     */
    public function findById(string $id): ?SupplierEntity;

    /**
     * Membuat supplier baru
     */
    public function create(string $code, CreateSupplierDTO $dto): SupplierEntity;

    /**
     * Update supplier.
     */
    public function update(UpdateSupplierDTO $dto): void;

    /**
     * Mengambil semua data supplier
     *
     * @return SupplierEntity[]
     */
    public function getAll(): array;

    /**
     * Mengambil nilai code tertinggi.
     */
    public function findMaxCode(): ?string;

    /**
     * Mengambil daftar supplier yang masih aktif.
     *
     * @return SupplierEntity[]
     */
    public function getActive(): array;

    /**
     * Soft delete supplier.
     */
    public function removeById(string $id): int;
}
