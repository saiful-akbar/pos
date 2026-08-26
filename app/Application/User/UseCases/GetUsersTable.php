<?php

declare(strict_types=1);

namespace App\Application\User\UseCases;

use App\Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetUsersTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(private UserRepositoryInterface $repository) {}

    /**
     * execute method.
     */
    public function execute(): JsonResponse
    {
        return DataTables::eloquent($this->repository->GetQuery())->toJson();
    }
}
