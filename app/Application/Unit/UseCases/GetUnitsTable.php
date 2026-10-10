<?php

declare(strict_types=1);

namespace App\Application\Unit\UseCases;

use App\Domain\Unit\Repositories\UnitRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

final class GetUnitsTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private UnitRepositoryInterface $unitRepository
    ) {}

    /**
     * Execute method.
     */
    public function execute(): JsonResponse
    {
        $dataTable = DataTables::eloquent(
            builder: $this->unitRepository->getQuery(),
        );

        return $dataTable->toJson();
    }
}
