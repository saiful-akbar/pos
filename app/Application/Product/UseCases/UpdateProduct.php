<?php

declare(strict_types=1);

namespace App\Application\Product\UseCases;

use App\Application\Product\DTOs\UpdateProductDTO;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UpdateProduct
{
    /**
     * Create a new class instance.
     */
    public function __construct(private ProductRepositoryInterface $repository) {}

    /**
     * Execute method.
     */
    public function execute(UpdateProductDTO $dto): void
    {
        DB::transaction(fn () => $this->repository->update($dto));
    }
}
