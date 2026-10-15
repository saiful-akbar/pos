<?php

declare(strict_types=1);

namespace App\Application\Product\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Product\DTOs\ProductDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Entities\ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Http\Requests\Products\UpdateProductRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateProduct
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(ProductEntity $product, UpdateProductRequest $request): void
    {
        DB::beginTransaction();

        try {
            $image = null;

            if ($request->hasFile('image')) {
                if ($product->image !== null) {
                    Storage::disk('public')->delete($product->image);
                }

                $image = Storage::disk('public')->putFile('images/products', $request->file('image'));
            }

            $this->productRepository->update(
                id: $product->id,
                value: new ProductDTO(
                    categoryId: $request->validated('category_id'),
                    code: $request->validated('code'),
                    name: $request->validated('name'),
                    image: $image,
                    minimumStock: $request->float('minimum_stock', 0.00),
                    currentStock: $request->float('current_stock', 0.00),
                    discount: $request->float('discount', 0.00),
                    isActive: $request->boolean('is_active'),
                )
            );

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Update data produk',
                event: EventActivityEnum::UPDATED,
                attributeChanges: new AttributeActivityDTO(
                    old: $product->toArray(),
                    attributes: [
                        'category_id' => $request->validate,
                        'code' => $request->validated('category_id'),
                        'name' => $request->validated('name'),
                        'image' => $image,
                        'minimum_stock' => $request->float('minimum_stock', 0.00),
                        'current_stock' => $request->float('current_stock', 0.00),
                        'discount' => $request->float('discount', 0.00),
                        'is_active' => $request->boolean('is_active'),
                    ],
                ),
            ));

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
