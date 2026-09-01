<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Application\Purchase\DTOs\AddProductPurchaseDTO;
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
        private PurchaseCartRepositoryInterface $purchaseRepository,
        private ErrorLogRepositoryInterface $errorRepository,
        private ProductRepositoryInterface $productRepository,
    ) {}

    /**
     * Execute method.
     *
     * @param string $productCode
     * @return void
     */
    public function execute(string $productCode): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $product = $this->productRepository->findByCodeOrBarcode(
                code: $productCode,
            );

            if (is_null($product)) {
                throw ValidationException::withMessages(messages: [
                    'product_code' => 'kode atau barcode produk tidak terdaftar.'
                ]);
            }

            $this->purchaseRepository->addProduct(dto: new AddProductPurchaseDTO(
                productId: $product->id,
                price: $product->purchasePrice,
                quantity: 1,
            ));

            $this->purchaseRepository->updateTotalAmount();

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
