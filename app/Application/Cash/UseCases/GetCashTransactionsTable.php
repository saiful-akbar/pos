<?php

declare(strict_types=1);

namespace App\Application\Cash\UseCases;

use App\Application\Cash\DTOs\CashReportFilterDTO;
use App\Domain\Cash\Repositories\CashTransactionRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetCashTransactionsTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CashTransactionRepositoryInterface $cashTransactionRepository,
    ) {}

    /**
     * Execute method (DataTables server side).
     */
    public function execute(CashReportFilterDTO $filter): JsonResponse
    {
        return DataTables::query($this->cashTransactionRepository->getQuery(filter: $filter))->toJson();
    }
}
