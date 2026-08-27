<?php

declare(strict_types=1);

namespace App\Application\Unit\UseCases;

use App\Domain\Unit\Repositories\UnitRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetUnitsTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(private UnitRepositoryInterface $repository) {}

    /**
     * Execute method.
     */
    public function execute(): JsonResponse
    {
        return DataTables::eloquent($this->repository->getQuery())->toJson();
    }
}
