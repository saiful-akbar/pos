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
use App\Http\Requests\Products\StoreProductRequest;
use Illuminate\Support\Facades\DB;

class CreateProduct
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
    public function execute(StoreProductRequest $request): ProductEntity
    {
        DB::beginTransaction();

        try {
            $newProduct = $this->productRepository->create(
                value: new ProductDTO(
                    categoryId: $request->validated('category_id'),
                    code: $request->validated('code'),
                    name: $request->validated('name'),
                    image: null,
                    minimumStock: $request->float('minimum_stock'),
                    currentStock: $request->float('current_stock'),
                    discount: $request->float('discount'),
                    isActive: $request->boolean('is_active'),
                ),
            );

            if ($request->hasFile('image')) {
                app(UploadImageProduct::class)->execute(
                    product: $newProduct,
                    file: $request->file('image'),
                );
            }

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Menambah (insert) data produk baru',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $newProduct->toArray(),
                )
            ));

            DB::commit();

            return $newProduct;
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
