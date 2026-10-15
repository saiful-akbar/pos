<?php

declare(strict_types=1);

namespace App\Application\Product\UseCases;

use App\Application\Product\DTOs\ProductDTO;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Entities\ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadImageProduct
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(ProductEntity $product, UploadedFile $file): void
    {
        try {
            if ($product->image !== null) {
                Storage::disk('public')->delete($product->image);
            }

            $path = Storage::disk('public')->putFile('images/products', $file);

            $this->productRepository->update(
                id: $product->id,
                value: new ProductDTO(
                    categoryId: $product->category->id,
                    code: $product->code,
                    name: $product->name,
                    image: $path,
                    minimumStock: $product->minimumStock,
                    currentStock: $product->currentStock,
                    isActive: $product->isActive,
                ),
            );
        } catch (\Throwable $e) {
            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
