<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\PurchaseReportFilterDTO;
use App\Domain\Report\Entities\PurchaseReportSummaryEntity;
use App\Domain\Report\Repositories\PurchaseReportRepositoryInterface;

class GetPurchaseReportSummary
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchaseReportRepositoryInterface $purchaseReportRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(PurchaseReportFilterDTO $filter): PurchaseReportSummaryEntity
    {
        return $this->purchaseReportRepository->getSummary(filter: $filter);
    }
}
