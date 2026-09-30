<?php

declare(strict_types=1);

namespace App\Domain\Customer\Repositories;

use App\Application\Customer\DTOs\CreateCustomerDTO;
use App\Application\Customer\DTOs\UpdateCustomerDTO;
use App\Domain\Customer\Entities\CustomerEntity;
use Illuminate\Database\Eloquent\Builder;

interface CustomerRepositoryInterface
{
    /**
     * Mengambil daftar customer dalam format query builder.
     */
    public function getQuery(?bool $active = null): Builder;

    /**
     * Mengambil data customer berdasarkan id.
     */
    public function findById(string $id): ?CustomerEntity;

    /**
     * Membuat customer baru
     */
    public function create(CreateCustomerDTO $dto): CustomerEntity;

    /**
     * Update customer.
     */
    public function update(UpdateCustomerDTO $dto): void;

    /**
     * Mengambil semua data customer
     *
     * @return CustomerEntity[]
     */
    public function getAll(): array;

    /**
     * Soft delete
     */
    public function removeById(string $id): int;
}
