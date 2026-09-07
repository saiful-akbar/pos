<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Domain\Sale\Entities\SaleCartEntity;
use App\Domain\Sale\Repositories\SaleCartRepositoryInterface;

class FindSaleCartByCurrentUser
{
    /**
     * Create a new class instance.
     */
    public function __construct(private SaleCartRepositoryInterface $saleCartRepository) {}

    /**
     * Execute method.
     */
    public function execute(): ?SaleCartEntity
    {
        return $this->saleCartRepository->findByUserId(
            userId: user()->id,
        );
    }
}
