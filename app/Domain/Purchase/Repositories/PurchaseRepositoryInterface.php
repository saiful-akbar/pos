<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Repositories;

use App\Application\Purchase\DTOs\PurchaseDTO;
use App\Domain\Purchase\Entities\PurchaseEntity;

interface PurchaseRepositoryInterface
{
    /**
     * Create (insert)
     */
    public function create(PurchaseDTO $value): PurchaseEntity;
}
