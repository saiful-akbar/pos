<?php

declare(strict_types=1);

namespace App\Application\Discount\UseCases;

use App\Domain\Discount\Entities\DiscountEntity;
use App\Domain\Discount\Repositories\DiscountRepositoryInterface;

class GetAllDiscounts
{
    /**
     * Create a new class instance.
     */
    public function __construct(private DiscountRepositoryInterface $discountRepository) {}

    /**
     * Mengambil semua daftar diskon
     *
     * @return DiscountEntity[]
     */
    public function execute(): array
    {
        return $this->discountRepository->getAll();
    }
}
