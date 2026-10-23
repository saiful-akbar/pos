<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Application\Report\DTOs\SaleReturnReportFilterDTO;
use App\Domain\Report\Repositories\SaleReturnReportRepositoryInterface;

class GetSaleReturnReportAnalysis
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnReportRepositoryInterface $saleReturnReportRepository,
    ) {}

    /**
     * Execute method.
     *
     * @return array{products: array<int, array<string, mixed>>, categories: array<int, array<string, mixed>>}
     */
    public function execute(SaleReturnReportFilterDTO $filter): array
    {
        return $this->saleReturnReportRepository->getAnalysis(filter: $filter);
    }
}
