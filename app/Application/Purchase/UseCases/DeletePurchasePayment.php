<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Purchase\DTOs\PurchaseDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use App\Domain\Purchase\Repositories\PurchasePaymentRepositoryInterface;
use App\Repositories\PurchaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeletePurchasePayment
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
    public function execute(string $purchaseId, string $paymentId): void
    {
        DB::beginTransaction();

        try {
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

            // Ambil data pembayaran
            $payment = $this->paymentRepository->findById(
                id: $paymentId,
            );

            // Validasi data pembayaran
            if ($purchase === null) {
                throw ValidationException::withMessages(messages: [
                    'message' => 'Pembayaran tidak ditemukan.',
                ]);
            }

            // Hapus pembayaran
            $this->paymentRepository->delete(
                id: $payment->id,
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
                logName: 'Menghapus pembayaran pembelian',
                event: EventActivityEnum::DELETED,
                attributeChanges: new AttributeActivityDTO(
                    old: $payment->toArray(),
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
