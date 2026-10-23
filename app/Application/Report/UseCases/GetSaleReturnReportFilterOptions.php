<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Domain\Report\Repositories\SaleReturnReportRepositoryInterface;

class GetSaleReturnReportFilterOptions
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnReportRepositoryInterface $saleReturnReportRepository,
    ) {}

    /**
     * Execute method.
     *
     * @return array{cashiers: array<int, array{id: string, name: string}>}
     */
    public function execute(): array
    {
        return $this->saleReturnReportRepository->getFilterOptions();
    }
}
