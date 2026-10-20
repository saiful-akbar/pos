<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Repositories;

use App\Application\Purchase\DTOs\PurchaseDetailDTO;
use App\Domain\Purchase\Entities\PurchaseDetailEntity;

interface PurchaseDetailRepositoryInterface
{
    /**
     * Create (insert).
     */
    public function create(PurchaseDetailDTO $value): PurchaseDetailEntity;
}
