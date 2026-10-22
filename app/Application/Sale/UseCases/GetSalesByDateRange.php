<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Domain\Sale\Entities\SaleEntity;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use DateTimeInterface;

class GetSalesByDateRange
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
    ) {}

    /**
     * Execute method.
     *
     * @return array<SaleEntity>
     */
    public function execute(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        return $this->saleRepository->getByDateRange(
            startDate: $startDate,
            endDate: $endDate,
        );
    }
}
