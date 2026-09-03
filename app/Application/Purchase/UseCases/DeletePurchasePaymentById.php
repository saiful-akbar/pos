<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Purchase\DTOs\UpdatePurchaseDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use App\Domain\Purchase\Repositories\PurchasePaymentRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DeletePurchasePaymentById
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchasePaymentRepositoryInterface $paymentRepository,
        private PurchaseRepositoryInterface $purchaseRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $id): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            // ambil data payment dan purchase.
            $payment = $this->paymentRepository->findById(id: $id);
            $purchase = $this->purchaseRepository->findById(id: $payment->purchaseId);

            // validasi data payment
            if ($payment === null || $purchase === null) {
                abort(404);
            }

            // hapus data payment.
            $this->paymentRepository->deleteById(id: $id);

            // Ambil total amount payment
            $totalAmount = $this->paymentRepository->sumAmountByPurchaseId(purchaseId: $purchase->id);
            $paymentStatus = $purchase->paymentStatus;

            if ($totalAmount <= 0) {
                $paymentStatus = PaymentStatusEnum::UNPAID;
            } else if ($totalAmount < $purchase->totalPrice) {
                $paymentStatus = PaymentStatusEnum::PARTIAL;
            } else if ($totalAmount >= $purchase->totalPrice) {
                $paymentStatus = PaymentStatusEnum::PAID;
            }

            // Update data purchase;
            $this->purchaseRepository->update(dto: new UpdatePurchaseDTO(
                id: $purchase->id,
                userId: $purchase->user->id,
                supplierId: $purchase->supplier->id,
                purchaseDate: $purchase->purchaseDate,
                invoiceNumber: $purchase->invoiceNumber,
                totalQuantity: $purchase->totalQuantity,
                totalPrice: $purchase->totalPrice,
                paymentStatus: $paymentStatus,
                notes: $purchase->notes,
            ));

            // Buat catatan aktifitas.
            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: "Menhapus pembayaran dari pembelian dengan id: {$payment->purchaseId}",
                event: EventActivityEnum::DELETED,
                attributeChanges: new AttributeActivityDTO(
                    old: $payment->toArray(),
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
}
