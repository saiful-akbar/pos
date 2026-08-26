<?php

declare(strict_types=1);

namespace App\Application\Category\UseCases;

use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetCategoriesTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(private CategoryRepositoryInterface $repository) {}

    /**
     * Execute method.
     */
    public function execute(): JsonResponse
    {
        return DataTables::eloquent($this->repository->getQuery())->toJson();
    }
}
