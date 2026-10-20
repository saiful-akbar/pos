<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Domain\Purchase\Entities\PurchaseEntity;
use App\Domain\Purchase\Repositories\PurchaseRepositoryInterface;
use DateTimeInterface;

class GetPurchasesByDate
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchaseRepositoryInterface $purchaseRepository
    ) {}

    /**
     * Execute method.
     *
     * @return PurchaseEntity[]
     */
    public function execute(DateTimeInterface $fromDate, DateTimeInterface $toDate): array
    {
        return $this->purchaseRepository->getByDate(
            fromDate: $fromDate,
            toDate: $toDate,
        );
    }
}
