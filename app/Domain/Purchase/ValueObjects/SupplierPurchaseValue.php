<?php

declare(strict_types=1);

namespace App\Domain\Purchase\ValueObjects;

use App\Base\Entity;
use Override;

class SupplierPurchaseValue extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $code,
        public readonly string $name,
        public readonly ?string $phoneNumber,
        public readonly ?string $address,
        public readonly ?string $notes,
        public readonly bool $isActive,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'phone_number' => $this->phoneNumber,
            'address' => $this->address,
            'notes' => $this->notes,
            'is_active' => $this->isActive,
        ];
    }
}
