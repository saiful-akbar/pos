<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Domain\Sale\Entities\SaleReturnCartEntity;
use App\Domain\Sale\Repositories\SaleReturnCartRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class FindSaleReturnCartByCurrentUser
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnCartRepositoryInterface $returnCartRepository
    ) {}

    /**
     * Execute method.
     */
    public function execute(): ?SaleReturnCartEntity
    {
        return $this->returnCartRepository->findByUserId(
            userId: Auth::user()->id,
        );
    }
}
