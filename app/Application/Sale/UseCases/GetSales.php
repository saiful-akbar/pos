<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\Sale\DTOs\GetSalesDTO;
use App\Domain\Sale\Entities\SaleEntity;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;

class GetSales
{
    /**
     * Create a new class instance.
     */
    public function __construct(private SaleRepositoryInterface $saleRepository) {}

    /**
     * Execute method
     *
     * @return SaleEntity[]
     */
    public function execute(GetSalesDTO $params): array
    {
        return $this->saleRepository->getByDate($params);
    }
}
