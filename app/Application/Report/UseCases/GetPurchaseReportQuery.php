<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\PurchaseReportFilterDTO;
use App\Domain\Report\Repositories\PurchaseReportRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class GetPurchaseReportQuery
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchaseReportRepositoryInterface $purchaseReportRepository,
    ) {}

    /**
     * Execute method (query terurut untuk export excel dan pdf).
     */
    public function execute(PurchaseReportFilterDTO $filter): Builder
    {
        return $this->purchaseReportRepository
            ->getQuery(filter: $filter)
            ->orderBy('purchases.transaction_date')
            ->orderBy('purchases.id');
    }
}
