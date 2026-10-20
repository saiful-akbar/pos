<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Repositories;

use App\Application\Purchase\DTOs\PurchasePaymentDTO;
use App\Domain\Purchase\Entities\PurchasePaymentEntity;

interface PurchasePaymentRepositoryInterface
{
    /**
     * Create (insert).
     */
    public function create(PurchasePaymentDTO $value): PurchasePaymentEntity;
}
