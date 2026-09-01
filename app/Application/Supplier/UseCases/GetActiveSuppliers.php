<?php

declare(strict_types=1);

namespace App\Application\Supplier\UseCases;

use App\Domain\Supplier\Entities\SupplierEntity;
use App\Domain\Supplier\Repositories\SupplierRepositoryInterface;

class GetActiveSuppliers
{
    /**
     * Create a new class instance.
     */
    public function __construct(private SupplierRepositoryInterface $supplierRepository) {}

    /**
     * Mengambil daftar supplier yang masih aktif.
     *
     * @return SupplierEntity[]
     */
    public function execute(): array
    {
        return $this->supplierRepository->getActive();
    }
}
