<?php

declare(strict_types=1);

namespace App\Domain\Cash\Entities;

use App\Base\Entity;
use App\Domain\Cash\ValueObjects\UserCashValue;
use App\Models\CashMonthlyBalance;
use DateTimeInterface;
use Override;

class CashMonthlyBalanceEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly UserCashValue $user,
        public readonly int $year,
        public readonly int $month,
        public readonly string $openingBalance,
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
            'user' => $this->user->toArray(),
            'year' => $this->year,
            'month' => $this->month,
            'opening_balance' => $this->openingBalance,
            'notes' => $this->notes,
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Membuat instance dari model (relasi user harus sudah dimuat). Saldo disimpan sebagai string desimal.
     */
    public static function fromModel(CashMonthlyBalance $model): self
    {
        return new self(
            id: $model->id,
            user: UserCashValue::fromModel($model->user),
            year: (int) $model->year,
            month: (int) $model->month,
            openingBalance: (string) $model->opening_balance,
            notes: $model->notes,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
