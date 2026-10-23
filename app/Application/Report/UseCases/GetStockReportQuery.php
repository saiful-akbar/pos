<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\StockReportFilterDTO;
use App\Domain\Report\Repositories\StockReportRepositoryInterface;
use Illuminate\Database\Query\Builder;

class GetStockReportQuery
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
    public function execute(StockReportFilterDTO $filter): Builder
    {
        return $this->stockReportRepository->getQuery(filter: $filter)->orderBy('x.code')->orderBy('x.id');
    }
}
