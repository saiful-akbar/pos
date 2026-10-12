<?php

declare(strict_types=1);

namespace App\Application\Category\UseCases;

use App\Domain\Category\Entities\CategoryEntity;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;

class GetAllCategories
{
    /**
     * Create a new class instance.
     */
    public function __construct(private CategoryRepositoryInterface $repository) {}

    /**
     * Execute method.
     *
     * @return CategoryEntity[]
     */
    public function execute(?bool $isActive = null): array
    {
        return $this->repository->getAll(
            isActive: $isActive,
        );
    }
}
