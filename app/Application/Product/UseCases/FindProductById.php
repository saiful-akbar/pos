<?php

declare(strict_types=1);

namespace App\Application\Product\UseCases;

use App\Domain\Product\Entities\ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;

class FindProductById
{
    /**
     * Create a new class instance.
     */
    public function __construct(private ProductRepositoryInterface $productRepository) {}

    /**
     * Execute method.
     */
    public function execute(string $id): ?ProductEntity
    {
        return $this->productRepository->findById(
            id: $id,
        );
    }
}
