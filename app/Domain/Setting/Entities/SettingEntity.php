<?php

declare(strict_types=1);

namespace App\Domain\Setting\Entities;

use App\Base\Entity;
use Override;

class SettingEntity extends Entity
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $appName,
        public readonly string $appVersion,
        public readonly string $storeName,
        public readonly ?string $storeLogo,
        public readonly ?string $storeAddress,
        public readonly ?string $backgroundImage,
    ) {}

    /**
     * Koversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'app_name' => $this->appName,
            'app_version' => $this->appVersion,
            'store_name' => $this->storeName,
            'store_logo' => $this->storeLogo,
            'store_address' => $this->storeAddress,
            'background_image' => $this->backgroundImage,
        ];
    }
}
