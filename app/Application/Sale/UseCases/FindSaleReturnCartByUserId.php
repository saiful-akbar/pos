<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Domain\Sale\Entities\SaleReturnCartEntity;
use App\Domain\Sale\Repositories\SaleReturnCartRepositoryInterface;

class FindSaleReturnCartByUserId
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnCartRepositoryInterface $cartRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $userId): ?SaleReturnCartEntity
    {
        return $this->cartRepository->find(
            column: 'user_id',
            value: $userId,
        );
    }
}
