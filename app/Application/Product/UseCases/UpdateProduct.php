<?php

declare(strict_types=1);

namespace App\Application\Product\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Product\DTOs\UpdateProductDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Entities\ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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
    public function execute(string $id, Request $request): void
    {
        $product = app(FindProductById::class)->execute(id: $id);

        if ($product === null) {
            abort(404);
        }

        DB::connection(pos_connection())->beginTransaction();

        try {
            $dto = new UpdateProductDTO(
                id: $product->id,
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
            );

            $this->productRepository->update(dto: $dto);

            if ($request->hasFile('image')) {
                $this->uploadImage(
                    file: $request->file('image'),
                    product: new ProductEntity(
                        id: $product->id,
                        categoryId: $request->validated('category'),
                        unitId: $request->validated('unit'),
                        code: $request->validated('code'),
                        barcode: $request->validated('barcode'),
                        name: $request->validated('name'),
                        purchasePrice: $request->integer('purchase_price'),
                        sellingPrice: $request->integer('selling_price'),
                        discount: $request->integer('discount'),
                        minimumStock: $request->integer('minimum_stock'),
                        currentStock: $request->integer('current_stock'),
                        isActive: $request->boolean('is_active'),
                        image: $product->image,
                    ),
                );
            }

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: "Memperbarui unit produk pada id: {$dto->id}",
                event: EventActivityEnum::UPDATED,
                attributeChanges: new AttributeActivityDTO(
                    old: $product->toArray(),
                    attributes: $dto->toArray(),
                ),
            ));

            DB::connection(pos_connection())->commit();
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
        if (is_string($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $fileName = $product->code.'.'.$file->extension();
        $path = Storage::disk('public')->putFileAs('images/products', $file, $fileName);

        $this->productRepository->updateImage(
            id: $product->id,
            path: $path,
        );
    }
}
