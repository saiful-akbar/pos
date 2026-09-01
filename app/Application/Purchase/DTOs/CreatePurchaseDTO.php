<?php

declare(strict_types=1);

namespace App\Application\Purchase\DTOs;

use App\Base\DTO;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use DateTimeInterface;
use Override;

class CreatePurchaseDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $userId,
        public readonly string $supplierId,
        public readonly DateTimeInterface $purchaseDate,
        public readonly ?string $invoiceNumber,
        public readonly int $totalQuantity,
        public readonly int $totalPrice,
        public readonly PaymentStatusEnum $paymentStatus,
        public readonly ?string $notes,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'supplier_id' => $this->supplierId,
            'purchase_date' => $this->purchaseDate->format('Y-m-d H:i:s'),
            'invoice_number' => $this->invoiceNumber,
            'total_quantity' => $this->totalQuantity,
            'total_price' => $this->totalPrice,
            'payment_status' => $this->paymentStatus->value,
            'notes' => $this->notes,
        ];
    }
}
