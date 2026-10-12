<?php

declare(strict_types=1);

namespace App\Application\Price\UseCases;

use App\Domain\Price\Entities\PriceEntity;
use App\Domain\Price\Repositories\PriceRepositoryInterface;

class FindPriceById
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PriceRepositoryInterface $priceRepository,
    ) {}

    /**
     * Execute method
     */
    public function execute(string $id): ?PriceEntity
    {
        return $this->priceRepository->findById(
            id: $id,
        );
    }
}
