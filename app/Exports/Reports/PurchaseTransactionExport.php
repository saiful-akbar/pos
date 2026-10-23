<?php

declare(strict_types=1);

namespace App\Exports\Reports;

use App\Domain\Report\Entities\PurchaseTransactionReportEntity;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Override;

class PurchaseTransactionExport implements Export, WithMultipleSheets
{
    /**
     * Create a new class instance.
     */
    public function __construct(private PurchaseTransactionReportEntity $transaction) {}

    /**
     * Sheet detail produk dan sheet daftar pembayaran.
     */
    #[Override]
    public function sheets(): array
    {
        return [
            new PurchaseTransactionDetailSheet($this->transaction),
            new PurchaseTransactionPaymentSheet($this->transaction),
        ];
    }
}
