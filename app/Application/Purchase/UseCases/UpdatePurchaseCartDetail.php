<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Application\Purchase\DTOs\PurchaseCartDetailDTO;
use App\Application\Purchase\DTOs\PurchaseCartDTO;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Price\Repositories\PriceRepositoryInterface;
use App\Domain\Purchase\Entities\PurchaseCartDetailEntity;
use App\Domain\Purchase\Entities\PurchaseCartEntity;
use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;
use App\Http\Requests\Purchases\UpdatePurchaseCartDetailRequest;
use App\Repositories\PurchaseCartDetailRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdatePurchaseCartDetail
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchaseCartRepositoryInterface $purchaseCartRepository,
        private PurchaseCartDetailRepository $purchaseCartDetailRepository,
        private PriceRepositoryInterface $productPriceRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(PurchaseCartEntity $purchaseCart, PurchaseCartDetailEntity $purchaseCartDetail, UpdatePurchaseCartDetailRequest $request): void
    {
        DB::beginTransaction();

        try {
            $unitId = $request->validated('unit_id');
            $quantity = $request->float('quantity');

            $price = $this->productPriceRepository->findByProductIdAndUnitId(
                productId: $purchaseCartDetail->product->id,
                unitId: $unitId,
            );

            if ($price === null) {
                throw ValidationException::withMessages(messages: [
                    'unit_id' => 'unit (satuan) tidak ditemukan pada produk ini.',
                ]);
            }

            $this->purchaseCartDetailRepository->update(
                id: $purchaseCartDetail->id,
                values: new PurchaseCartDetailDTO(
                    purchaseCartId: $purchaseCartDetail->purchaseCartId,
                    productId: $purchaseCartDetail->product->id,
                    unitId: $price->unit->id,
                    multiplesQuantity: $price->multiplesQuantity,
                    quantity: $quantity,
                    price: $price->purchasePrice,
                    subtotal: $price->purchasePrice * $quantity,
                ),
            );

            $totalProduct = $this->purchaseCartDetailRepository->countProductByPurchaseCartId(
                purchaseCartId: $purchaseCart->id,
            );

            $totalQuantity = $this->purchaseCartDetailRepository->sumQuantityByPurchaseCartId(
                purchaseCartId: $purchaseCart->id,
            );

            $totalPrice = $this->purchaseCartDetailRepository->sumSubtotalByPurchaseCartId(
                purchaseCartId: $purchaseCart->id,
            );

            $this->purchaseCartRepository->update(
                id: $purchaseCart->id,
                values: new PurchaseCartDTO(
                    userId: $purchaseCart->user->id,
                    transactionDate: $purchaseCart->transactionDate,
                    totalProduct: $totalProduct,
                    totalQuantity: $totalQuantity,
                    totalPrice: $totalPrice,
                ),
            );

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
