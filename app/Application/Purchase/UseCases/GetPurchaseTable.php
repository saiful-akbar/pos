<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Domain\Purchase\Repositories\PurchaseRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetPurchaseTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(private PurchaseRepositoryInterface $purchaseRepository) {}

    /**
     * Execute method.
     */
    public function execute(): JsonResponse
    {
        return DataTables::eloquent($this->purchaseRepository->getQuery())->toJson();
    }
}
