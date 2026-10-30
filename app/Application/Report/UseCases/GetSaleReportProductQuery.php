<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\SaleReportFilterDTO;
use App\Domain\Report\Repositories\SaleReportRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class GetSaleReportProductQuery
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReportRepositoryInterface $reportRepository,
    ) {}

    /**
     * Execute method (query terurut untuk export excel).
     */
    public function execute(SaleReportFilterDTO $filter): Builder
    {
        return $this->reportRepository
            ->getProductQuery(filter: $filter)
            ->orderBy('sales.transaction_date')
            ->orderBy('sales.invoice_number')
            ->orderBy('products.code');
    }
}
