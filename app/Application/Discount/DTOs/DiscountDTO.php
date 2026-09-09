<?php

declare(strict_types=1);

namespace App\Application\Discount\DTOs;

use App\Base\DTO;
use DateTimeInterface;
use Override;

class DiscountDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly int|float $discount,
        public readonly string $name,
        public readonly DateTimeInterface $effectiveDate,
        public readonly ?DateTimeInterface $expirationDate = null,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'discount' => $this->discount,
            'name' => $this->name,
            'effective_date' => $this->effectiveDate->format('Y-m-d'),
            'expiration_date' => $this->expirationDate?->format('Y-m-d'),
        ];
    }
}
