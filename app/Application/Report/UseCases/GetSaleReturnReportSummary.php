<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\SaleReturnReportFilterDTO;
use App\Domain\Report\Entities\SaleReturnReportSummaryEntity;
use App\Domain\Report\Repositories\SaleReturnReportRepositoryInterface;

class GetSaleReturnReportSummary
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnReportRepositoryInterface $saleReturnReportRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(SaleReturnReportFilterDTO $filter): SaleReturnReportSummaryEntity
    {
        return $this->saleReturnReportRepository->getSummary(filter: $filter);
    }
}
