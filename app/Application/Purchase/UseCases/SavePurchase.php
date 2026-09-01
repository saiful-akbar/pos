<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Purchase\DTOs\CreatePurchaseDetailDTO;
use App\Application\Purchase\DTOs\CreatePurchaseDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use App\Domain\Purchase\Repositories\PurchaseCartDetailRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseDetailRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseRepositoryInterface;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SavePurchase
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchaseRepositoryInterface $purchaseRepository,
        private PurchaseDetailRepositoryInterface $purchaseDetailRepository,
        private PurchaseCartRepositoryInterface $cartRepository,
        private PurchaseCartDetailRepositoryInterface $cartDetailRepository,
        private ProductRepositoryInterface $productRepository,
        private ErrorLogRepositoryInterface $errorRepository,
        private ActivityLogRepositoryInterface $activityRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(Request $request): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            // ambil data purchase_carts.
            $cart = $this->cartRepository->findByUserId(userId: user()->id);

            // validasi purchase_carts
            if ($cart === null) {
                abort(404);
            }

            $totalPrice = $cart->totalPrice;
            $amountPaid = $request->integer('amount_paid', 0);
            $paymentStatus = PaymentStatusEnum::UNPAID;

            if ($amountPaid > 0 && $amountPaid < $totalPrice) {
                $paymentStatus = PaymentStatusEnum::PARTIAL;
            } elseif ($amountPaid >= $totalPrice) {
                $paymentStatus = PaymentStatusEnum::PAID;
            }

            // insert data purchases
            $purchase = $this->purchaseRepository->create(dto: new CreatePurchaseDTO(
                userId: user()->id,
                supplierId: $request->validated('supplier_id'),
                purchaseDate: new DateTimeImmutable(),
                invoiceNumber: $request->validated('invoice_number'),
                totalQuantity: $cart->totalQuantity,
                totalPrice: $cart->totalPrice,
                paymentStatus: $paymentStatus,
                notes: $request->validated('notes'),
            ));

            // ambil data purchase_cart_details
            $cartDetails = $this->cartDetailRepository->getByCartId(cartId: $cart->id);

            // validasi purchase_cart_details
            if (count($cartDetails) === 0) {
                throw ValidationException::withMessages(messages: [
                    'product_code' => 'Daftar produk kosong'
                ]);
            }

            foreach ($cartDetails as $cartDetail) {
                // insert data purchase_details
                $this->purchaseDetailRepository->create(dto: new CreatePurchaseDetailDTO(
                    purchaseId: $purchase->id,
                    productId: $cartDetail->product->id,
                    quantity: $cartDetail->quantity,
                    purchasePrice: $cartDetail->purchasePrice,
                    subtotalPrice: $cartDetail->subtotalPrice,
                ));

                // ambil data produk berdasarkan id
                $product = $this->productRepository->findById(id: $cartDetail->product->id);

                // update current_stock prosuct.
                $this->productRepository->updateCurrentStockById(
                    id: $product->id,
                    stock: $product->currentStock + $cartDetail->quantity,
                );
            }

            // hapus purchase_carts dan purchase_cart_details.
            $this->cartRepository->deleteByUserId(userId: user()->id);

            // buat catatan aktifitas.
            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Membuat pembelian baru',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $purchase->toArray(),
                )
            ));

            DB::connection(pos_connection())->commit();
        } catch (\Throwable $e) {
            DB::connection(pos_connection())->rollBack();

            // Buat catatan error.
            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
