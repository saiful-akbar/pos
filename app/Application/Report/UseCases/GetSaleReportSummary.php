<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\SaleReportFilterDTO;
use App\Domain\Report\Entities\SaleReportSummaryEntity;
use App\Domain\Report\Repositories\SaleReportRepositoryInterface;

class GetSaleReportSummary
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReportRepositoryInterface $saleReportRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(SaleReportFilterDTO $filter): SaleReportSummaryEntity
    {
        return $this->saleReportRepository->getSummary(filter: $filter);
    }
}
