<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\SaleReturnReportFilterDTO;
use App\Domain\Report\Repositories\SaleReturnReportRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class GetSaleReturnReportQuery
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnReportRepositoryInterface $saleReturnReportRepository,
    ) {}

    /**
     * Execute method (query terurut untuk export excel dan pdf).
     */
    public function execute(SaleReturnReportFilterDTO $filter): Builder
    {
        return $this->saleReturnReportRepository
            ->getQuery(filter: $filter)
            ->orderBy('sale_returns.transaction_date')
            ->orderBy('sale_returns.return_number');
    }
}
