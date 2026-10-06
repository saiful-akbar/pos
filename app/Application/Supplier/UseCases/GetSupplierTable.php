<?php

declare(strict_types=1);

namespace App\Application\Supplier\UseCases;

use App\Domain\Supplier\Repositories\SupplierRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetSupplierTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SupplierRepositoryInterface $supplierRepository
    ) {}

    /**
     * Execute method
     */
    public function execute(): JsonResponse
    {
        return DataTables::eloquent($this->supplierRepository->getQuery())->toJson();
    }
}
