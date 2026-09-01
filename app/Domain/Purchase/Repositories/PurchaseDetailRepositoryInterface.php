<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Repositories;

use App\Application\Purchase\DTOs\CreatePurchaseDetailDTO;
use App\Domain\Purchase\Entities\PurchaseDetailEntity;

interface PurchaseDetailRepositoryInterface
{
    /**
     * Membuat (insert) data purchase detail.
     */
    public function create(CreatePurchaseDetailDTO $dto): PurchaseDetailEntity;
}
