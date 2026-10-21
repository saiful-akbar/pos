<?php

declare(strict_types=1);

namespace App\Domain\Sale\ValueObjects;

use App\Base\Entity;
use App\Models\Customer;
use Override;

class CustomerSaleValue extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $phoneNumber,
        public readonly ?string $address,
        public readonly float $discount,
        public readonly bool $isActive,
    ) {}

    /**
     * Konversi data object ke array
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone_number' => $this->phoneNumber,
            'address' => $this->address,
            'discount' => $this->discount,
            'isActive' => $this->isActive,
        ];
    }

    /**
     * Membuat instance dari model.
     */
    public static function fromModel(?Customer $model): ?self
    {
        return $model === null ? null : new self(
            id: $model->id,
            name: $model->name,
            phoneNumber: $model->phone_number,
            address: $model->address,
            discount: parse_float($model->discount),
            isActive: $model->is_active === 1,
        );
    }
}
