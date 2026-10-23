<?php

declare(strict_types=1);

namespace App\Application\Report\UseCases;

use App\Domain\Purchase\Repositories\PurchaseDetailRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchasePaymentRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseRepositoryInterface;
use App\Domain\Report\Entities\PurchaseTransactionReportEntity;

class GetPurchaseTransactionReport
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchaseRepositoryInterface $purchaseRepository,
        private PurchaseDetailRepositoryInterface $purchaseDetailRepository,
        private PurchasePaymentRepositoryInterface $purchasePaymentRepository,
    ) {}

    /**
     * Mengambil satu transaksi pembelian beserta detail produk dan daftar pembayarannya (untuk export excel dan pdf).
     */
    public function execute(string $purchaseId): ?PurchaseTransactionReportEntity
    {
        $purchase = $this->purchaseRepository->findById(id: $purchaseId);

        if ($purchase === null) {
            return null;
        }

        return new PurchaseTransactionReportEntity(
            purchase: $purchase,
            details: $this->purchaseDetailRepository->getByPurchaseId(purchaseId: $purchase->id),
            payments: $this->purchasePaymentRepository->getByPurchaseId(purchaseId: $purchase->id),
        );
    }
}
