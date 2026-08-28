<?php

declare(strict_types=1);

namespace App\Domain\ActivityLog\Enums;

enum EventActivityEnum: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case REMOVED = 'removed';
    case RESTORED = 'restored';
    case DELETED = 'deleted';
}
