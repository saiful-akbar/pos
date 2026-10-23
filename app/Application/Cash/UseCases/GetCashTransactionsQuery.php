<?php

declare(strict_types=1);

namespace App\Application\Cash\UseCases;

use App\Application\Cash\DTOs\CashReportFilterDTO;
use App\Domain\Cash\Repositories\CashTransactionRepositoryInterface;
use Illuminate\Database\Query\Builder;

class GetCashTransactionsQuery
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CashTransactionRepositoryInterface $cashTransactionRepository,
    ) {}

    /**
     * Execute method (query terurut untuk export excel dan pdf).
     */
    public function execute(CashReportFilterDTO $filter): Builder
    {
        return $this->cashTransactionRepository
            ->getQuery(filter: $filter)
            ->orderBy('cash_transactions.transaction_date')
            ->orderBy('cash_transactions.transaction_number');
    }
}
