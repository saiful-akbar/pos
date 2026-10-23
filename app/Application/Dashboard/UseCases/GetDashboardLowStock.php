<?php

declare(strict_types=1);

namespace App\Application\Dashboard\UseCases;

use App\Domain\Dashboard\Entities\LowStockProductEntity;
use App\Domain\Dashboard\Repositories\ProductDashboardRepositoryInterface;

class GetDashboardLowStock
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private ProductDashboardRepositoryInterface $productDashboardRepository,
    ) {}

    /**
     * Produk stok menipis (paling mendesak lebih dahulu) beserta jumlah seluruh produk yang menipis.
     *
     * @return array{items: LowStockProductEntity[], total: int}
     */
    public function execute(int $limit): array
    {
        return [
            'items' => $this->productDashboardRepository->getLowStockProducts(limit: $limit),
            'total' => $this->productDashboardRepository->countLowStockProducts(),
        ];
    }
}
