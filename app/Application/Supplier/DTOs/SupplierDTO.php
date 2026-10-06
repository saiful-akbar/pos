<?php

declare(strict_types=1);

namespace App\Application\Supplier\DTOs;

use App\Base\DTO;
use Override;

class SupplierDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $phoneNumber,
        public readonly ?string $address,
        public readonly bool $isActive,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'phone_number' => $this->phoneNumber,
            'address' => $this->address,
            'is_active' => $this->isActive,
        ];
    }
}
