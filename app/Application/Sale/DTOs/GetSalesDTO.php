<?php

declare(strict_types=1);

namespace App\Application\Sale\DTOs;

use App\Base\DTO;
use DateTimeInterface;
use Override;

class GetSalesDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly ?DateTimeInterface $fromDate = null,
        public readonly ?DateTimeInterface $toDate = null,
        public readonly ?string $invoiceNumber = null,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'from_date' => $this->fromDate?->format('Y-m-d'),
            'to_date' => $this->toDate?->format('Y-m-d'),
            'invoice_number' => $this->invoiceNumber,
        ];
    }

    /**
     * Memeriksa apakah semua param bernilai null.
     */
    public function allIsNull(): bool
    {
        if ($this->fromDate === null && $this->toArray() === null && $this->invoiceNumber === null) {
            return true;
        }

        return false;
    }

    /**
     * Memeriksa apakah tanggal bernilai null atau tidak.
     */
    public function dateIsNotNull(): bool
    {
        if ($this->fromDate !== null && $this->toDate !== null) {
            return true;
        }

        return false;
    }

    /**
     * Memeriksa apakah nomor faktur bernilai null atau tidak.
     */
    public function invoiceNumberIsNotNull(): bool
    {
        if ($this->invoiceNumber !== null) {
            return true;
        }

        return false;
    }
}
