<?php

declare(strict_types=1);

namespace App\Application\Cash\DTOs;

use App\Base\DTO;
use App\Domain\Cash\Enums\CashTransactionTypeEnum;
use DateTimeInterface;
use Override;

class CashTransactionDTO extends DTO
{
    /**
     * Create a new class instance.
     *
     * Nominal berupa string desimal agar tidak melewati bilangan floating point.
     */
    public function __construct(
        public readonly string $userId,
        public readonly DateTimeInterface $transactionDate,
        public readonly CashTransactionTypeEnum $type,
        public readonly string $amount,
        public readonly ?string $notes,
        public readonly ?string $referenceNumber,
    ) {}

    /**
     * Konversi data object ke array (nomor transaksi dibuat oleh repository).
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'transaction_date' => $this->transactionDate->format('Y-m-d'),
            'type' => $this->type->value,
            'amount' => $this->amount,
            'notes' => $this->notes,
            'reference_number' => $this->referenceNumber,
        ];
    }
}
