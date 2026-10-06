<?php

declare(strict_types=1);

namespace App\Application\Supplier\UseCases;

use App\Domain\Supplier\Entities\SupplierEntity;
use App\Domain\Supplier\Repositories\SupplierRepositoryInterface;

class FindSupplierById
{
    /**
     * Create a new class instance.
     */
    public function __construct(private SupplierRepositoryInterface $supplierRepository) {}

    /**
     * Execute method.
     */
    public function execute(string $id): ?SupplierEntity
    {
        return $this->supplierRepository->findById(
            id: $id,
        );
    }
}
