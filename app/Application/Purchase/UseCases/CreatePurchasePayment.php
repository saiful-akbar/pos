<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Purchase\DTOs\CreatePurchasePaymentDTO;
use App\Application\Purchase\DTOs\UpdatePurchaseDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Purchase\Entities\PurchasePaymentEntity;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use App\Domain\Purchase\Repositories\PurchasePaymentRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreatePurchasePayment
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
    public function execute(CreatePurchasePaymentDTO $dto): ?PurchasePaymentEntity
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $payment = $this->paymentRepository->create(dto: $dto);
            $purchase = $this->purchaseRepository->findById(id: $payment->purchaseId);

            $totalAmount = $this->paymentRepository->sumAmountByPurchaseId(purchaseId: $purchase->id);
            $paymentStatus = $purchase->paymentStatus;

            if ($totalAmount <= 0) {
                $paymentStatus = PaymentStatusEnum::UNPAID;
            } elseif ($totalAmount < $purchase->totalPrice) {
                $paymentStatus = PaymentStatusEnum::PARTIAL;
            } elseif ($totalAmount >= $purchase->totalPrice) {
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

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: "Menambahkan pembayaran untuk pembelian dengan id: {$payment->purchaseId}",
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $payment->toArray(),
                ),
            ));

            DB::connection(pos_connection())->commit();

            return $payment;
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
