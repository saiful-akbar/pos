<?php

declare(strict_types=1);

namespace App\Application\Customer\DTOs;

use App\Base\DTO;
use Override;

class CreateCustomerDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $phoneNumber,
        public readonly ?string $address,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'phone_number' => $this->phoneNumber,
            'address' => $this->address,
        ];
    }
}
