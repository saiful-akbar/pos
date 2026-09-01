<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Repositories;

use App\Application\Purchase\DTOs\CreatePurchaseDTO;
use App\Domain\Purchase\Entities\PurchaseEntity;

interface PurchaseRepositoryInterface
{
    /**
     * Membuat (insert) data pembalian.
     */
    public function create(CreatePurchaseDTO $dto): PurchaseEntity;
}
