<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Purchase\DTOs\PurchaseDetailDTO;
use App\Application\Purchase\DTOs\PurchaseDTO;
use App\Application\Purchase\DTOs\PurchasePaymentDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Purchase\Enums\PaymentMethodEnum;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use App\Domain\Purchase\Repositories\PurchaseCartDetailRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseDetailRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchasePaymentRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseRepositoryInterface;
use App\Http\Requests\Purchases\CreatePurchaseRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreatePurchase
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchaseRepositoryInterface $purchaseRepository,
        private PurchaseDetailRepositoryInterface $purchaseDetailRepository,
        private PurchaseCartRepositoryInterface $purchaseCartRepository,
        private PurchaseCartDetailRepositoryInterface $purchaseCartDetailRepository,
        private PurchasePaymentRepositoryInterface $purchasePaymentRepository,
        private ProductRepositoryInterface $productRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(CreatePurchaseRequest $request): void
    {
        DB::beginTransaction();

        try {
            $supplierId = $request->validated('supplier_id');
            $transactionDate = $request->date('transaction_date');
            $invoiceNumber = $request->validated('invoice_number');
            $paymentMethod = $request->enum('payment_method', PaymentMethodEnum::class);
            $paidAmount = $request->float('paid_amount');
            $notes = $request->validated('notes');

            // Ambil data keranjang pembelian.
            $purchaseCart = $this->purchaseCartRepository->findByUserId(
                userId: user()->id,
            );

            // Ambil data daftar produk pada keranjang pembelian.
            $purchaseCartDetails = $this->purchaseCartDetailRepository->getByPurchaseCartId(
                purchaseCartId: $purchaseCart->id,
            );

            // Jika daftar produk belum ada kirimkan pesan error.
            if (count($purchaseCartDetails) <= 0) {
                throw ValidationException::withMessages(messages: [
                    'message' => 'Belum ada produk yang ditambahkan.',
                ]);
            }

            // Buat status pembayaran.
            $paymentStatus = PaymentStatusEnum::UNPAID;

            if ($paidAmount > 0 && $paidAmount < $purchaseCart->totalPrice) {
                $paymentStatus = PaymentStatusEnum::PARTIAL;
            }

            if ($paidAmount > 0 && $paidAmount >= $purchaseCart->totalPrice) {
                $paymentStatus = PaymentStatusEnum::PAID;
            }

            // Insert data pembelian.
            $purchase = $this->purchaseRepository->create(
                value: new PurchaseDTO(
                    userId: user()->id,
                    supplierId: $supplierId,
                    transactionDate: $transactionDate,
                    invoiceNumber: $invoiceNumber,
                    totalProduct: $purchaseCart->totalProduct,
                    totalQuantity: $purchaseCart->totalQuantity,
                    totalPrice: $purchaseCart->totalPrice,
                    paymentStatus: $paymentStatus,
                    notes: $notes,
                ),
            );

            // Insert data detail pembelian
            foreach ($purchaseCartDetails as $purchaseCartDetail) {
                $purchaseDetail = $this->purchaseDetailRepository->create(
                    value: new PurchaseDetailDTO(
                        purchaseId: $purchase->id,
                        productId: $purchaseCartDetail->product->id,
                        unitId: $purchaseCartDetail->unit->id,
                        multiplesQuantity: $purchaseCartDetail->multiplesQuantity,
                        quantity: $purchaseCartDetail->quantity,
                        price: $purchaseCartDetail->price,
                        subtotal: $purchaseCartDetail->subtotal,
                    ),
                );

                // Update stok tersedia (current_stock) pada produk.
                $this->productRepository->updateCurrentStock(
                    id: $purchaseDetail->product->id,
                    value: $purchaseDetail->product->currentStock + ($purchaseDetail->quantity * $purchaseDetail->multiplesQuantity),
                );
            }

            // Insert data pembeyaran pembelian.
            if ($paymentMethod !== null) {
                $this->purchasePaymentRepository->create(
                    value: new PurchasePaymentDTO(
                        purchaseId: $purchase->id,
                        userId: user()->id,
                        paymentDate: $transactionDate,
                        paymentMethod: $paymentMethod,
                        amount: $paidAmount,
                        referenceNumber: null,
                        notes: null,
                    ),
                );
            }

            // Hapus purchase cart
            $this->purchaseCartRepository->delete(
                id: $purchaseCart->id,
            );

            // Insert data catatan aktifitas.
            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Menyimpan transaksi pembelian',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $purchase->toArray(),
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
