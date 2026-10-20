<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseDetailRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchasePaymentRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RemovePurchase
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchaseRepositoryInterface $purchaseRepository,
        private PurchaseDetailRepositoryInterface $purchaseDetailRepository,
        private PurchasePaymentRepositoryInterface $purchasePaymentRepository,
        private ProductRepositoryInterface $productRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $id): void
    {
        DB::beginTransaction();

        try {
            // Ambil data pembelian.
            $purchase = $this->purchaseRepository->findById(
                id: $id,
            );

            // Periksa data pembelian.
            if ($purchase === null) {
                throw ValidationException::withMessages(messages: [
                    'message' => 'Transaksi pembelian tidak ditemukan.',
                ]);
            }

            // Ambil daftar detail produk pemeblian.
            $purchaseDetails = $this->purchaseDetailRepository->getByPurchaseId(
                purchaseId: $purchase->id,
            );

            // Update stok tersedia (current_stock) pada produk.
            foreach ($purchaseDetails as $purchaseDetail) {
                $this->productRepository->updateCurrentStock(
                    id: $purchaseDetail->product->id,
                    value: $purchaseDetail->product->currentStock - ($purchaseDetail->quantity * $purchaseDetail->multiplesQuantity),
                );
            }

            // Hapus pembelian.
            $this->purchaseRepository->remove(
                id: $purchase->id,
            );

            // Hapus detail pembelian.
            $this->purchaseDetailRepository->removeByPurchaseId(
                purchaseId: $purchase->id,
            );

            // Hapus pembayaran pembelian
            $this->purchasePaymentRepository->removeByPurchaseId(
                purchaseId: $purchase->id,
            );

            // Buat catatan aktifitas.
            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Menghapus (soft delete) pembelian',
                event: EventActivityEnum::REMOVED,
                attributeChanges: new AttributeActivityDTO(
                    old: $purchase->toArray(),
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
