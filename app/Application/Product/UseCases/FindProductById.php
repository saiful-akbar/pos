<?php

declare(strict_types=1);

namespace App\Application\Product\UseCases;

use App\Domain\Product\Entities\ProductDetailEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;

class FindProductById
{
    /**
     * Create a new class instance.
     */
    public function __construct(private ProductRepositoryInterface $repository) {}

    /**
     * Execute method.
     */
    public function execute(string $id): ?ProductDetailEntity
    {
        return $this->repository->findById($id);
    }
}
