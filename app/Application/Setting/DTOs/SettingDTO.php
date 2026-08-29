<?php

declare(strict_types=1);

namespace App\Application\Setting\DTOs;

use App\Base\DTO;
use Override;

class SettingDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $appName,
        public readonly string $storeName,
        public readonly ?string $storeAddress = null,
        public readonly ?string $id = null,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'app_name' => $this->appName,
            'store_name' => $this->storeName,
            'store_address' => $this->storeAddress,
        ];
    }
}
