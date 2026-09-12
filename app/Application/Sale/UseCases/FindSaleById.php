<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Domain\Sale\Entities\SaleWithDetailEntity;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;

class FindSaleById
{
    /**
     * Create a new class instance.
     */
    public function __construct(private SaleRepositoryInterface $saleRepository) {}

    /**
     * Execute method.
     */
    public function execute(string $id): ?SaleWithDetailEntity
    {
        return $this->saleRepository->findById(id: $id);
    }
}
