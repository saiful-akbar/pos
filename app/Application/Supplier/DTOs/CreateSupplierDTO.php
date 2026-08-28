<?php

declare(strict_types=1);

namespace App\Application\Supplier\DTOs;

use App\Base\DTO;
use Override;

class CreateSupplierDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $phoneNumber = null,
        public readonly ?string $address = null,
        public readonly ?string $notes = null,
        public readonly bool $isActive = true,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'phone_number' => $this->phoneNumber,
            'address' => $this->address,
            'notes' => $this->notes,
            'is_active' => $this->isActive,
        ];
    }
}
