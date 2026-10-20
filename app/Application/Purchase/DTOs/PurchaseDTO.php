<?php

declare(strict_types=1);

namespace App\Application\Purchase\DTOs;

use App\Base\DTO;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use DateTimeInterface;
use Override;

class PurchaseDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $userId,
        public readonly string $supplierId,
        public readonly DateTimeInterface $transactionDate,
        public readonly ?string $invoiceNumber,
        public readonly float $totalProduct,
        public readonly float $totalQuantity,
        public readonly float $totalPrice,
        public readonly float $alreadyPaid,
        public readonly PaymentStatusEnum $paymentStatus,
        public readonly ?string $notes,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'supplier_id' => $this->supplierId,
            'transaction_date' => $this->transactionDate->format('Y-m-d'),
            'invoice_number' => $this->invoiceNumber,
            'total_product' => $this->totalProduct,
            'total_quantity' => $this->totalQuantity,
            'total_price' => $this->totalPrice,
            'already_paid' => $this->alreadyPaid,
            'payment_status' => $this->paymentStatus->value,
            'notes' => $this->notes,
        ];
    }
}
