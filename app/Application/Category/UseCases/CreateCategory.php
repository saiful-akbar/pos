<?php

declare(strict_types=1);

namespace App\Application\Category\UseCases;

use App\Application\Category\DTOs\CreateCategoryDTO;
use App\Domain\Category\Entities\CategoryEntity;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreateCategory
{
    /**
     * Create a new class instance.
     */
    public function __construct(private CategoryRepositoryInterface $repository) {}

    /**
     * Execute method.
     */
    public function execute(CreateCategoryDTO $dto): CategoryEntity
    {
        return DB::transaction(fn () => $this->repository->create($dto));
    }
}
