<?php

declare(strict_types=1);

namespace App\Application\Cash\UseCases;

use App\Application\Cash\DTOs\CashReportFilterDTO;
use App\Domain\Cash\Repositories\CashTransactionRepositoryInterface;

class GetCashTransactionTotals
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CashTransactionRepositoryInterface $cashTransactionRepository,
    ) {}

    /**
     * Execute method.
     *
     * @return array{incoming: string, outgoing: string, count: int}
     */
    public function execute(CashReportFilterDTO $filter): array
    {
        return $this->cashTransactionRepository->getTotals(filter: $filter);
    }
}
