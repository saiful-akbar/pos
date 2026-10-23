<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\StockReportFilterDTO;
use App\Domain\Report\Repositories\StockReportRepositoryInterface;
use Illuminate\Database\Query\Builder;

class GetStockReportMutations
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
    public function execute(StockReportFilterDTO $filter, ?string $productId = null): Builder
    {
        return $this->stockReportRepository->getMutationQuery(filter: $filter, productId: $productId);
    }
}
