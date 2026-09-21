<?php

declare(strict_types=1);

namespace App\Application\StockOpname\UseCases;

use App\Domain\StockOpname\Entities\DailyStockOpnameDetailEntity;
use App\Domain\StockOpname\Repositories\DailyStockOpnameDetailRepositoryInterface;

class GetDailyStockOpnameDetailByHeaderId
{
    /**
     * Create a new class instance.
     */
    public function __construct(private DailyStockOpnameDetailRepositoryInterface $dailyStockOpnameDetailRepository) {}

    /**
     * Execute method.
     *
     * @return DailyStockOpnameDetailEntity[]
     */
    public function execute(string $headerId): array
    {
        return $this->dailyStockOpnameDetailRepository->getByHeaderId(
            headerId: $headerId,
        );
    }
}
