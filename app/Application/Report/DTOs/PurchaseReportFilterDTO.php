<?php

declare(strict_types=1);

namespace App\Application\Report\DTOs;

use App\Base\DTO;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use DateTimeInterface;
use Override;

class PurchaseReportFilterDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly DateTimeInterface $startDate,
        public readonly DateTimeInterface $endDate,
        public readonly ?string $supplierId = null,
        public readonly ?PaymentStatusEnum $paymentStatus = null,
        public readonly ?string $invoiceNumber = null,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'start_date' => $this->startDate->format('Y-m-d'),
            'end_date' => $this->endDate->format('Y-m-d'),
            'supplier_id' => $this->supplierId,
            'payment_status' => $this->paymentStatus?->value,
            'invoice_number' => $this->invoiceNumber,
        ];
    }
}
