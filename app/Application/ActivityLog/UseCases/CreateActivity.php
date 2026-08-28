<?php

declare(strict_types=1);

namespace App\Application\ActivityLog\UseCases;

use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;

class CreateActivity
{
    /**
     * Create a new class instance.
     */
    public function __construct(private ActivityLogRepositoryInterface $repository) {}

    /**
     * Exxecute method.
     */
    public function execute(CreateActivityDTO $dto): void
    {
        $this->repository->create($dto);
    }
}
