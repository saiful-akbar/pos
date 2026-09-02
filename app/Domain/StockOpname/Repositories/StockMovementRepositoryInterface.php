<?php

declare(strict_types=1);

namespace App\Domain\StockOpname\Repositories;

use App\Application\StockOpname\DTOs\CreateStockMovementDTO;
use App\Domain\StockOpname\Entities\StockMovementEntity;

interface StockMovementRepositoryInterface
{
    /**
     * Membuat (insert) data pergerak stock.
     */
    public function create(CreateStockMovementDTO $dto): StockMovementEntity;
}
