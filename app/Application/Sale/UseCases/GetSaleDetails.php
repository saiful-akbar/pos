<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\Sale\DTOs\GetSalesDTO;
use App\Domain\Sale\Repositories\SaleDetailRepositoryInterface;

class GetSaleDetails
{
    /**
     * Create a new class instance.
     */
    public function __construct(private SaleDetailRepositoryInterface $saleDetailRepository) {}

    /**
     * Mengambil daftar detail penjualan.
     */
    public function execute(GetSalesDTO $params): array
    {
        return $this->saleDetailRepository->get($params);
    }
}
