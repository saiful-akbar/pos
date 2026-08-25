<?php

declare(strict_types=1);

namespace App\Application\Role\UseCases;

use App\Domain\Role\Repositories\RoleRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetAllRolesTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(private RoleRepositoryInterface $repository) {}

    /**
     * Execute
     */
    public function execute(): JsonResponse
    {
        return DataTables::eloquent($this->repository->getAllQuery())->toJson();
    }
}
