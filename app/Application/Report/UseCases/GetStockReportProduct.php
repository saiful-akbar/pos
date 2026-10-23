<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\StockReportFilterDTO;
use App\Domain\Report\Repositories\StockReportRepositoryInterface;

class GetStockReportProduct
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
    /** @return array<string, mixed>|null */
    public function execute(StockReportFilterDTO $filter, string $productId): ?array
    {
        return $this->stockReportRepository->findProductRow(filter: $filter, productId: $productId);
    }
}
