<?php

declare(strict_types=1);

namespace App\Application\Purchase\DTOs;

use App\Base\DTO;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use DateTimeInterface;
use Override;

class UpdatePurchaseDTO extends DTO
{
    /**
     * Constructor.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        public readonly string $supplierId,
        public readonly DateTimeInterface $purchaseDate,
        public readonly ?string $invoiceNumber,
        public readonly int $totalQuantity,
        public readonly int $totalPrice,
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
            'invoice_number' => $this->invoiceNumber,
            'purchase_date' => $this->purchaseDate->format('Y-m-d'),
            'total_quantity' => $this->totalQuantity,
            'total_price' => $this->totalPrice,
            'payment_status' => $this->paymentStatus->value,
            'notes' => $this->notes,
        ];
    }
}
