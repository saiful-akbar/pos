<?php

declare(strict_types=1);

namespace App\Domain\Cash\ValueObjects;

use App\Base\Entity;
use App\Models\User;
use Override;

class UserCashValue extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $username,
        public readonly string $fullName,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'full_name' => $this->fullName,
        ];
    }

    /**
     * From model.
     */
    public static function fromModel(User $model): self
    {
        return new self(
            id: $model->id,
            username: $model->username,
            fullName: $model->full_name,
        );
    }
}
