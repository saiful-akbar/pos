<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Domain\Sale\Entities\SaleReturnEntity;
use App\Domain\Sale\Repositories\SaleReturnRepositoryInterface;
use DateTimeInterface;

class GetSaleReturnsByDateRange
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnRepositoryInterface $saleReturnRepository,
    ) {}

    /**
     * Execute method.
     *
     * @return array<SaleReturnEntity>
     */
    public function execute(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        return $this->saleReturnRepository->getByDateRange(
            startDate: $startDate,
            endDate: $endDate,
        );
    }
}
