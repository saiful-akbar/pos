<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Application\Purchase\DTOs\GetPurchasesDTO;
use App\Domain\Purchase\Entities\PurchaseDetailEntity;
use App\Domain\Purchase\Repositories\PurchaseDetailRepositoryInterface;

class GetPurchaseDetailsByDate
{
    /**
     * Create a new class instance.
     */
    public function __construct(private PurchaseDetailRepositoryInterface $purchaseDetailRepository) {}

    /**
     * Execute method
     *
     * @return PurchaseDetailEntity[]
     */
    public function execute(GetPurchasesDTO $params): array
    {
        return $this->purchaseDetailRepository->getByDate($params);
    }
}
