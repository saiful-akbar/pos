<?php

declare(strict_types=1);

namespace App\Application\Rreport\UseCases;

use App\Application\Report\DTOs\GetMonitoringMonthlyDTO;
use App\Domain\Report\Entities\MonitoringMonthlyStockEntity;
use App\Domain\Report\Repositories\ReportRepositoryInterface;

class GetMonitoringMonthlyStock
{
    /**
     * Create a new class instance.
     */
    public function __construct(private ReportRepositoryInterface $reportRepository) {}

    /**
     * Execute method.
     *
     * @return MonitoringMonthlyStockEntity[]
     */
    public function execute(GetMonitoringMonthlyDTO $params): array
    {
        return $this->reportRepository->getMonitoringMonthlyStock($params);
    }
}
