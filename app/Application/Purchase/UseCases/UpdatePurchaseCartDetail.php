<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Application\Purchase\DTOs\UpdatePurchaseCartDetailDTO;
use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;

class UpdatePurchaseCartDetail
{
    /**
     * Create a new class instance.
     */
    public function __construct(private PurchaseCartRepositoryInterface $purchaseCartRepository) {}

    /**
     * Exceute method
     */
    public function execute(UpdatePurchaseCartDetailDTO $dto): void
    {
        $this->purchaseCartRepository->updateDetail(dto: $dto);
    }
}
