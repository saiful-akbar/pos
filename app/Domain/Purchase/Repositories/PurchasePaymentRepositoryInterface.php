<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Repositories;

use App\Application\Purchase\DTOs\CreatePurchasePaymentDTO;
use App\Domain\Purchase\Entities\PurchasePaymentEntity;

interface PurchasePaymentRepositoryInterface
{
    /**
     * Membuat (insert) data pembayaran pembelian.
     */
    public function create(CreatePurchasePaymentDTO $dto): PurchasePaymentEntity;
}
