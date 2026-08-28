<?php

declare(strict_types=1);

namespace App\Application\ActivityLog\DTOs;

use App\Base\DTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use Override;

class CreateActivityDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $userId,
        public readonly string $logName,
        public readonly ?EventActivityEnum $event = null,
        public readonly ?AttributeActivityDTO $attributeChanges = null,
    ) {}

    #[Override]
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'log_name' => $this->logName,
            'event' => $this->event?->value,
            'attribute_changes' => $this->attributeChanges?->toArray(),
        ];
    }
}
