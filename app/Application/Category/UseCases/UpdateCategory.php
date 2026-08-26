<?php

declare(strict_types=1);

namespace App\Application\Category\UseCases;

use App\Application\Category\DTOs\UpdateCategoryDTO;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UpdateCategory
{
    /**
     * Create a new class instance.
     */
    public function __construct(private CategoryRepositoryInterface $repository) {}

    /**
     * Execute method.
     */
    public function execute(UpdateCategoryDTO $dto): void
    {
        DB::transaction(fn () => $this->repository->update($dto));
    }
}
