<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Domain\Report\Repositories\SaleReportRepositoryInterface;

class GetSaleReportFilterOptions
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReportRepositoryInterface $saleReportRepository,
    ) {}

    /**
     * Execute method.
     *
     * @return array{cashiers: array<int, array{id: string, name: string}>}
     */
    public function execute(): array
    {
        return $this->saleReportRepository->getFilterOptions();
    }
}
