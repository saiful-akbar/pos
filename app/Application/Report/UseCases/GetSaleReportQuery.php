<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\SaleReportFilterDTO;
use App\Domain\Report\Repositories\SaleReportRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class GetSaleReportQuery
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReportRepositoryInterface $saleReportRepository,
    ) {}

    /**
     * Execute method (query terurut untuk export excel dan pdf).
     */
    public function execute(SaleReportFilterDTO $filter): Builder
    {
        return $this->saleReportRepository
            ->getQuery(filter: $filter)
            ->orderBy('sales.transaction_date')
            ->orderBy('sales.invoice_number');
    }
}
