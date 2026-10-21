<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Domain\Sale\Entities\SaleCartEntity;
use App\Domain\Sale\Repositories\SaleCartRepositoryInterface;

class FindSaleCartByUserId
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleCartRepositoryInterface $cartRepository
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $userId): ?SaleCartEntity
    {
        return $this->cartRepository->find(
            column: 'user_id',
            value: $userId,
        );
    }
}
