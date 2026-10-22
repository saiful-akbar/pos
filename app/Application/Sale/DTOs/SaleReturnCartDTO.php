<?php

declare(strict_types=1);

namespace App\Application\Sale\DTOs;

use App\Base\DTO;
use DateTimeInterface;
use Override;

class SaleReturnCartDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $userId,
        public readonly string $saleId,
        public readonly DateTimeInterface $transactionDate,
        public readonly float $totalProduct,
        public readonly float $totalQuantity,
        public readonly float $totalAmount,
        public readonly float $discount,
        public readonly float $grandTotal,
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
            'sale_id' => $this->saleId,
            'transaction_date' => $this->transactionDate->format('Y-m-d'),
            'total_product' => $this->totalProduct,
            'total_quantity' => $this->totalQuantity,
            'total_amount' => $this->totalAmount,
            'discount' => $this->discount,
            'grand_total' => $this->grandTotal,
            'notes' => $this->notes,
        ];
    }
}
