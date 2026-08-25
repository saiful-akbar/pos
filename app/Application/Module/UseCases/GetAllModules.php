<?php

declare(strict_types=1);

namespace App\Application\Module\UseCases;

use App\Domain\Module\Repositories\ModuleRepositoryInterface;

class GetAllModules
{
    /**
     * Create a new class instance.
     */
    public function __construct(private ModuleRepositoryInterface $repository) {}

    /**
     * Execute method.
     *
     * @return ModuleEntity[]
     */
    public function execute(): array
    {
        return $this->repository->getAll();
    }
}
