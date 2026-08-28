<?php

declare(strict_types=1);

namespace App\Domain\ActivityLog\Repositories;

use App\Application\ActivityLog\DTOs\CreateActivityDTO;

interface ActivityLogRepositoryInterface
{
    /**
     * Membuat catatan aktifitas.
     */
    public function create(CreateActivityDTO $dto): void;
}
