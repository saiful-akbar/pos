<?php

declare(strict_types=1);

namespace App\Application\Product\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Product\DTOs\CreateProductDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Entities\ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
    public function execute(Request $request): ProductEntity
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $product = $this->productRepository->create(new CreateProductDTO(
                categoryId: $request->validated('category'),
                unitId: $request->validated('unit'),
                code: $request->validated('code'),
                barcode: $request->validated('barcode'),
                name: $request->validated('name'),
                purchasePrice: $request->float('purchase_price'),
                sellingPrice: $request->float('selling_price'),
                discount: $request->float('discount'),
                minimumStock: $request->float('minimum_stock'),
                currentStock: $request->float('current_stock'),
                isActive: $request->boolean('is_active')
            ));

            if ($request->hasFile('image')) {
                $this->uploadImage(product: $product, file: $request->file('image'));
            }

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Membuat produk baru',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $product->toArray(),
                ),
            ));

            DB::connection(pos_connection())->commit();

            return $product;
        } catch (\Throwable $e) {
            DB::connection(pos_connection())->rollBack();

            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Upload image
     */
    private function uploadImage(ProductEntity $product, UploadedFile $file): void
    {
        if ($product->image !== null) {
            Storage::disk('public')->delete($product->image);
        }

        $fileName = $product->code . '.' . $file->extension();
        $path = Storage::disk('public')->putFileAs('images/products', $file, $fileName);

        $this->productRepository->updateImage(
            id: $product->id,
            path: $path,
        );
    }
}
