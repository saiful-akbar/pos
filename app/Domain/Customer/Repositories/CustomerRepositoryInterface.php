<?php

declare(strict_types=1);

namespace App\Domain\Customer\Repositories;

use App\Application\Customer\DTOs\CustomerDTO;
use App\Domain\Customer\Entities\CustomerEntity;
use Illuminate\Database\Eloquent\Builder;

interface CustomerRepositoryInterface
{
    /**
     * Mengambil daftar dalam format query builder.
     */
    public function getQuery(): Builder;

    /**
     * Mengambil data berdasarkan id
     */
    public function findById(string $id): ?CustomerEntity;

    /**
     * Menambah (create)
     */
    public function create(CustomerDTO $value): CustomerEntity;

    /**
     * Update
     */
    public function update(string $id, CustomerDTO $value): int;

    /**
     * Remove (soft delete).
     */
    public function remove(string $id): int;
}
