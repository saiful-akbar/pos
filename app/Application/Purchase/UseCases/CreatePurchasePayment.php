<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Purchase\DTOs\PurchaseDTO;
use App\Application\Purchase\DTOs\PurchasePaymentDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Purchase\Enums\PaymentMethodEnum;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use App\Domain\Purchase\Repositories\PurchasePaymentRepositoryInterface;
use App\Http\Requests\Purchases\StorePurchasePaymentRequest;
use App\Repositories\PurchaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreatePurchasePayment
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchasePaymentRepositoryInterface $paymentRepository,
        private PurchaseRepository $purchaseRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(StorePurchasePaymentRequest $request, string $purchaseId): void
    {
        DB::beginTransaction();

        try {
            $paymentDate = $request->date('payment_date');
            $paymentMethod = $request->enum('payment_method', PaymentMethodEnum::class);
            $amount = $request->float('amount');
            $notes = $request->validated('notes');

            // Ambil data pembelian
            $purchase = $this->purchaseRepository->findById(
                id: $purchaseId,
            );

            // Validasi data pembelian
            if ($purchase === null) {
                throw ValidationException::withMessages(messages: [
                    'message' => 'Transaksi pembelian tidak ditemukan.',
                ]);
            }

            // Insert data pembayaran.
            $purchasePayment = $this->paymentRepository->create(
                value: new PurchasePaymentDTO(
                    purchaseId: $purchase->id,
                    userId: user()->id,
                    paymentDate: $paymentDate,
                    paymentMethod: $paymentMethod,
                    amount: $amount,
                    notes: $notes,
                ),
            );

            // Hitung jumlah yang sudah dibayarkan berdasarkan purchase_id
            $totalAmount = $this->paymentRepository->sumAmountByPurchaseId(
                purchaseId: $purchase->id,
            );

            // Periksa status pembayaran
            $paymentStatus = $purchase->paymentStatus;

            // Validasi status pembayaran
            if ($totalAmount <= 0) {
                $paymentStatus = PaymentStatusEnum::UNPAID;
            } elseif ($totalAmount < $purchase->totalPrice) {
                $paymentStatus = PaymentStatusEnum::PARTIAL;
            } elseif ($totalAmount >= $purchase->totalPrice) {
                $paymentStatus = PaymentStatusEnum::PAID;
            }

            // Update status pembayaran pada pembelian (purchase)
            $this->purchaseRepository->update(
                id: $purchase->id,
                value: new PurchaseDTO(
                    userId: $purchase->user->id,
                    supplierId: $purchase->supplier->id,
                    transactionDate: $purchase->transactionDate,
                    invoiceNumber: $purchase->invoiceNumber,
                    totalProduct: $purchase->totalProduct,
                    totalQuantity: $purchase->totalQuantity,
                    totalPrice: $purchase->totalPrice,
                    alreadyPaid: $totalAmount,
                    notes: $purchase->notes,
                    paymentStatus: $paymentStatus,
                ),
            );

            // Buat catatan aktifitas
            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Menambah pembayaran pembelian',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $purchasePayment->toArray(),
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
