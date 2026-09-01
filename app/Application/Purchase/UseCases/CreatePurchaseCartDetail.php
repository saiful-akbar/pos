<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Application\Purchase\DTOs\CreatePurchaseCartDetailDTO;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreatePurchaseCartDetail
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchaseCartRepositoryInterface $purchaseCartRepository,
        private ProductRepositoryInterface $productRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method
     */
    public function execute(string $productCode, int $purchasePrice, int $quantity): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $product = $this->productRepository->findByCodeOrBarcode(value: $productCode);

            if ($product === null) {
                throw ValidationException::withMessages(messages: [
                    'product_code' => 'kode atau barcode produk tidak terdaftar.',
                ]);
            }

            $dto = new CreatePurchaseCartDetailDTO(
                productId: $product->id,
                purchasePrice: $purchasePrice,
                quantity: $quantity,
            );

            $this->purchaseCartRepository->createDetail(
                userId: user()->id,
                dto: $dto,
            );

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
}
