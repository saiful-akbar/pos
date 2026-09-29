<?php

declare(strict_types=1);

namespace App\Application\Sale\DTOs;

use App\Base\DTO;
use DateTimeInterface;
use Override;

class GetSaleReturnDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly ?DateTimeInterface $fromDate = null,
        public readonly ?DateTimeInterface $toDate = null,
        public readonly ?string $returnNumber = null,
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
            'return_number' => $this->returnNumber,
            'invoice_number' => $this->invoiceNumber,
        ];
    }

    /**
     * Memeriksa apakah semua atribut bernilai null.
     */
    public function allAttributesIsNull(): bool
    {
        return ($this->fromDate === null || $this->toDate === null) && $this->returnNumber === null && $this->invoiceNumber === null;
    }

    /**
     * Memeriksa apakah atribute tanggal bernilai null atau tidak.
     */
    public function datesIsNotNull(): bool
    {
        return $this->fromDate !== null && $this->toDate !== null;
    }

    /**
     * Memeriksa apakah attribute invoiceNumber bernilai null.
     */
    public function invoiceNumberIsNotNull(): bool
    {
        return $this->invoiceNumber !== null;
    }

    /**
     * Memeriksa apakah attribute returnNumber bernilai null.
     */
    public function returnNumberIsNotNull(): bool
    {
        return $this->returnNumber !== null;
    }
}
