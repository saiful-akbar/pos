<?php

declare(strict_types=1);

namespace App\Application\Sale\DTOs;

use App\Base\DTO;
use DateTimeInterface;
use Override;

class SaleDTO extends DTO
{
    /**
     * @param 'cash'|'qris'|'debit'|'credit' $paymentMethod
     */
    public function __construct(
        public string $userId,
        public ?string $customerId,
        public string $invoiceNumber,
        public DateTimeInterface $transactionDate,
        public float $totalProduct,
        public float $totalQuantity,
        public float $totalAmount,
        public float $discount,
        public float $grandTotal,
        public float $paidAmount,
        public float $changeAmount,
        public string $paymentMethod,
        public ?string $notes,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'customer_id' => $this->customerId,
            'invoice_number' => $this->invoiceNumber,
            'transaction_date' => $this->transactionDate->format('Y-m-d'),
            'total_product' => $this->totalProduct,
            'total_quantity' => $this->totalQuantity,
            'total_amount' => $this->totalAmount,
            'discount' => $this->discount,
            'grand_total' => $this->grandTotal,
            'paid_amount' => $this->paidAmount,
            'change_amount' => $this->changeAmount,
            'payment_method' => $this->paymentMethod,
            'notes' => $this->notes,
        ];
    }
}
