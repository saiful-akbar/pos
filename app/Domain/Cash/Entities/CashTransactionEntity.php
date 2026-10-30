<?php

declare(strict_types=1);

namespace App\Domain\Cash\Entities;

use App\Base\Entity;
use App\Domain\Cash\Enums\CashSourceTypeEnum;
use App\Domain\Cash\Enums\CashTransactionTypeEnum;
use App\Domain\Cash\ValueObjects\UserCashValue;
use App\Models\CashTransaction;
use DateTimeInterface;
use Override;

class CashTransactionEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly UserCashValue $user,
        public readonly string $transactionNumber,
        public readonly DateTimeInterface $transactionDate,
        public readonly CashTransactionTypeEnum $type,
        public readonly string $amount,
        public readonly ?string $notes,
        public readonly ?string $referenceNumber,
        public readonly ?DateTimeInterface $createdAt,
        public readonly ?DateTimeInterface $updatedAt,
        public readonly ?DateTimeInterface $deletedAt,
        public readonly ?CashSourceTypeEnum $sourceType = null,
        public readonly ?string $sourceId = null,
    ) {}

    /**
     * Transaksi otomatis (dibuat dari transaksi penjualan, pembelian, atau retur).
     */
    public function isAutomatic(): bool
    {
        return $this->sourceType !== null;
    }

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user->toArray(),
            'transaction_number' => $this->transactionNumber,
            'transaction_date' => $this->transactionDate->format('Y-m-d'),
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'amount' => $this->amount,
            'notes' => $this->notes,
            'reference_number' => $this->referenceNumber,
            'source_type' => $this->sourceType?->value,
            'source_id' => $this->sourceId,
            'is_automatic' => $this->isAutomatic(),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Membuat instance dari model (relasi user harus sudah dimuat). Nominal disimpan sebagai string desimal.
     */
    public static function fromModel(CashTransaction $model): self
    {
        return new self(
            id: $model->id,
            user: UserCashValue::fromModel($model->user),
            transactionNumber: $model->transaction_number,
            transactionDate: parse_date_time_immutable($model->transaction_date),
            type: CashTransactionTypeEnum::from($model->type),
            amount: (string) $model->amount,
            notes: $model->notes,
            referenceNumber: $model->reference_number,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
            sourceType: $model->source_type !== null ? CashSourceTypeEnum::from($model->source_type) : null,
            sourceId: $model->source_id,
        );
    }
}
