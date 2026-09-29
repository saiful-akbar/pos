<?php

declare(strict_types=1);

namespace App\Domain\Report\Repositories;

use App\Application\Report\DTOs\GetMonitoringMonthlyDTO;
use App\Domain\Report\Entities\MonitoringMonthlyStockEntity;

interface ReportRepositoryInterface
{
    /**
     * Mengambil daftar monitoring stok bulanan per produk.
     *
     * @return MonitoringMonthlyStockEntity[]
     */
    public function getMonitoringMonthlyStock(GetMonitoringMonthlyDTO $params): array;
}
