<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\PurchaseReportFilterDTO;
use App\Domain\Report\Repositories\PurchaseReportRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class GetPurchaseReportProductQuery
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchaseReportRepositoryInterface $reportRepository,
    ) {}

    /**
     * Execute method (query terurut untuk export excel).
     */
    public function execute(PurchaseReportFilterDTO $filter): Builder
    {
        return $this->reportRepository
            ->getProductQuery(filter: $filter)
            ->orderBy('purchases.transaction_date')
            ->orderBy('purchases.invoice_number')
            ->orderBy('products.code');
    }
}
