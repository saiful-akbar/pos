<?php

declare(strict_types=1);

namespace App\Application\User\DTOs;

use App\Base\Entity;
use Override;

class CreateUserDTO extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $fullName,
        public readonly string $roleId,
        public readonly string $username,
        public readonly string $password,
        public readonly ?string $photo,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'full_name' => $this->fullName,
            'role_id' => $this->roleId,
            'username' => $this->username,
            'password' => $this->password,
            'photo' => $this->photo,
        ];
    }
}
