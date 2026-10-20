<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Entities;

use App\Base\Entity;
use App\Domain\Purchase\Enums\PaymentMethodEnum;
use App\Domain\Purchase\ValueObjects\UserPurchaseValue;
use App\Models\PurchasePayment;
use DateTimeInterface;
use Override;

class PurchasePaymentEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $purchaseId,
        public readonly UserPurchaseValue $user,
        public readonly DateTimeInterface $paymentDate,
        public readonly PaymentMethodEnum $paymentMethod,
        public readonly float $amount,
        public readonly ?string $notes,
        public readonly ?DateTimeInterface $createdAt,
        public readonly ?DateTimeInterface $updatedAt,
        public readonly ?DateTimeInterface $deletedAt,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'purchase_id' => $this->purchaseId,
            'user' => $this->user->toArray(),
            'payment_date' => $this->paymentDate->format('Y-m-d'),
            'payment_method' => $this->paymentMethod->value,
            'amount' => $this->amount,
            'notes' => $this->notes,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Instansiasi from model.
     */
    public static function fromModel(PurchasePayment $model): self
    {
        return new self(
            id: $model->id,
            purchaseId: $model->purchase_id,
            user: UserPurchaseValue::fromModel($model->user),
            paymentDate: parse_date_time_immutable($model->payment_date),
            paymentMethod: PaymentMethodEnum::from($model->payment_method),
            amount: parse_float($model->amount),
            notes: $model->notes,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
