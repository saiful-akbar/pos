<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Application\Purchase\DTOs\GetPurchasesDTO;
use App\Domain\Purchase\Entities\PurchaseEntity;
use App\Domain\Purchase\Repositories\PurchaseRepositoryInterface;

class GetPurchases
{
    /**
     * Create a new class instance.
     */
    public function __construct(private PurchaseRepositoryInterface $purchaseRepository) {}

    /**
     * Mengambil daftar penjualan.
     *
     * @param GetPurchasesDTO $dto
     * @return PurchaseEntity[]
     */
    public function execute(GetPurchasesDTO $dto): array
    {
        return $this->purchaseRepository->get(dto: $dto);
    }
}
