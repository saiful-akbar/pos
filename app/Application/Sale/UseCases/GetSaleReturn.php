<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\Sale\DTOs\GetSaleReturnDTO;
use App\Domain\Sale\Entities\SaleReturnEntity;
use App\Domain\Sale\Repositories\SaleReturnRepositoryInterface;

class GetSaleReturn
{
    /**
     * Create a new class instance.
     */
    public function __construct(private SaleReturnRepositoryInterface $saleReturnRepository) {}

    /**
     * Execute method.
     *
     * @return SaleReturnEntity[]
     */
    public function execute(GetSaleReturnDTO $params): array
    {
        return $this->saleReturnRepository->get($params);
    }
}
