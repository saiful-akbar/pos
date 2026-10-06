<?php

declare(strict_types=1);

namespace App\Application\Customer\DTOs;

use App\Base\DTO;
use Override;

class CustomerDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $phoneNumber = null,
        public readonly ?string $address = null,
        public readonly float $discount = 0.00,
        public readonly bool $isActive = true,
    ) {}

    /**
     * Konversi data object ke array
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'phone_number' => $this->phoneNumber,
            'address' => $this->address,
            'discount' => $this->discount,
            'is_active' => $this->isActive,
        ];
    }
}
