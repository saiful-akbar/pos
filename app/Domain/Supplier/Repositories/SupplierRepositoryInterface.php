<?php

declare(strict_types=1);

namespace App\Domain\Supplier\Repositories;

use App\Application\Supplier\DTOs\SupplierDTO;
use App\Domain\Supplier\Entities\SupplierEntity;
use Illuminate\Database\Eloquent\Builder;

interface SupplierRepositoryInterface
{
    /**
     * Mengambil daftar dengan response query builder.
     */
    public function getQuery(): Builder;

    /**
     * Mengambil data berdasarkan id.
     */
    public function findById(string $id): ?SupplierEntity;

    /**
     * Insert data
     */
    public function create(SupplierDTO $value): SupplierEntity;

    /**
     * Update data
     */
    public function update(string $id, SupplierDTO $value): int;

    /**
     * Soft delete
     */
    public function remove(string $id): int;
}
