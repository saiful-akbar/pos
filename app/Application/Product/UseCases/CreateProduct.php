<?php

declare(strict_types=1);

namespace App\Application\Product\UseCases;

use App\Application\Product\DTOs\CreateProductDTO;
use App\Domain\Product\Entities\ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreateProduct
{
    /**
     * Create a new class instance.
     */
    public function __construct(private ProductRepositoryInterface $repository) {}

    /**
     * Execute method.
     */
    public function execute(CreateProductDTO $dto): ProductEntity
    {
        return DB::transaction(fn () => $this->repository->create($dto));
    }
}
