<?php

declare(strict_types=1);

namespace App\Application\Discount\UseCases;

use App\Domain\Discount\Entities\DiscountEntity;
use App\Domain\Discount\Repositories\DiscountRepositoryInterface;

class FindDiscountById
{
    /**
     * Create a new class instance.
     */
    public function __construct(private DiscountRepositoryInterface $discountRepository) {}

    /**
     * Execute method.
     */
    public function execute(string $id): ?DiscountEntity
    {
        return $this->discountRepository->findById(id: $id);
    }
}
