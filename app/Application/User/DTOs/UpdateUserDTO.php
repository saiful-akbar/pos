<?php

declare(strict_types=1);

namespace App\Application\User\DTOs;

use App\Base\DTO;
use Override;

class UpdateUserDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $roleId,
        public readonly string $fullName,
        public readonly bool $isActive,
        public readonly string $username,
        public readonly ?string $password = null,
        public readonly ?string $photo = null,
    ) {}

    #[Override]
    public function toArray(): array
    {
        $data = [
            'role_id' => $this->roleId,
            'full_name' => $this->fullName,
            'username' => $this->username,
            'is_active' => $this->isActive,
        ];

        if ($this->password !== null) {
            $data['password'] = $this->password;
        }

        if ($this->photo !== null) {
            $data['photo'] = $this->photo;
        }

        return $data;
    }
}
