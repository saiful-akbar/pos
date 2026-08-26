<?php

declare(strict_types=1);

namespace App\Application\Category\UseCases;

use App\Domain\Category\Entities\CategoryEntity;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;

class FindCategoryById
{
    /**
     * Create a new class instance.
     */
    public function __construct(private CategoryRepositoryInterface $repository) {}

    /**
     * Execute method.
     */
    public function execute(string $id): ?CategoryEntity
    {
        return $this->repository->findById($id);
    }
}
