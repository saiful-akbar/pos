<?php

declare(strict_types=1);

namespace App\Domain\Sale\Entities;

use App\Base\Entity;
use App\Domain\Sale\ValueObjects\CustomerSaleValue;
use App\Domain\Sale\ValueObjects\UserSaleValue;
use App\Models\Sale;
use DateTimeInterface;
use Override;

class SaleEntity extends Entity
{
    /**
     * @param 'cash'|'qris'|'debit'|'credit' $paymentMethod
     */
    public function __construct(
        public string $id,
        public UserSaleValue $user,
        public ?CustomerSaleValue $customer,
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
        public ?DateTimeInterface $createdAt,
        public ?DateTimeInterface $updatedAt,
        public ?DateTimeInterface $deletedAt,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user->toArray(),
            'customer' => $this->customer?->toArray(),
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
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }

    public static function fromModel(Sale $model): self
    {
        return new self(
            id: $model->id,
            user: UserSaleValue::fromModel($model->user),
            customer: $model->customer !== null ? CustomerSaleValue::fromModel($model->customer) : null,
            invoiceNumber: $model->invoice_number,
            transactionDate: parse_date_time_immutable($model->transaction_date),
            totalProduct: parse_float($model->total_product),
            totalQuantity: parse_float($model->total_quantity),
            totalAmount: parse_float($model->total_amount),
            discount: parse_float($model->discount),
            grandTotal: parse_float($model->grand_total),
            paidAmount: parse_float($model->paid_amount),
            changeAmount: parse_float($model->change_amount),
            paymentMethod: $model->payment_method,
            notes: $model->notes,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
