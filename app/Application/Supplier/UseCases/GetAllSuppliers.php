<?php

declare(strict_types=1);

namespace App\Application\Supplier\UseCases;

use App\Domain\Supplier\Entities\SupplierEntity;
use App\Domain\Supplier\Repositories\SupplierRepositoryInterface;

class GetAllSuppliers
{
    /**
     * Create a new class instance.
     */
    public function __construct(private SupplierRepositoryInterface $repository) {}

    /**
     * Execute method.
     *
     * @return SupplierEntity[]
     */
    public function execute(): array
    {
        return $this->repository->getAll();
    }
}
