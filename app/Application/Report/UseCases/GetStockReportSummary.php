<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\StockReportFilterDTO;
use App\Domain\Report\Entities\StockReportSummaryEntity;
use App\Domain\Report\Repositories\StockReportRepositoryInterface;

class GetStockReportSummary
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private StockReportRepositoryInterface $stockReportRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(StockReportFilterDTO $filter): StockReportSummaryEntity
    {
        return $this->stockReportRepository->getSummary(filter: $filter);
    }
}
