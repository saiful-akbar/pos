<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\Sale\DTOs\GetSaleReturnDTO;
use App\Domain\Sale\Entities\SaleReturnDetailEntity;
use App\Domain\Sale\Repositories\SaleReturnDetailRepositoryInterface;

class GetSaleReturnDetail
{
    /**
     * Create a new class instance.
     */
    public function __construct(private SaleReturnDetailRepositoryInterface $saleReturnDetailRepository) {}

    /**
     * Execute method.
     *
     * @return SaleReturnDetailEntity[]
     */
    public function execute(GetSaleReturnDTO $params): array
    {
        return $this->saleReturnDetailRepository->get($params);
    }
}
