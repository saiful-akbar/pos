<?php

declare(strict_types=1);

namespace App\Application\Price\UseCases;

use App\Domain\Price\Entities\PriceEntity;
use App\Domain\Price\Repositories\PriceRepositoryInterface;
use App\Domain\Product\Entities\ProductEntity;

class GetPricesByProduct
{
    /**
     * Create a new class instance.
     */
    public function __construct(private PriceRepositoryInterface $priceRepository) {}

    /**
     * Execute method
     *
     * @return PriceEntity[]
     */
    public function execute(ProductEntity $product): array
    {
        return $this->priceRepository->getByProductId(
            productId: $product->id,
        );
    }
}
