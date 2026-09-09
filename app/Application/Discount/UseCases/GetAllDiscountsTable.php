<?php

declare(strict_types=1);

namespace App\Application\Discount\UseCases;

use App\Domain\Discount\Repositories\DiscountRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetAllDiscountsTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(private DiscountRepositoryInterface $discountRepository) {}

    /**
     * Execute method.
     */
    public function execute(): JsonResponse
    {
        return DataTables::eloquent($this->discountRepository->getAllQuery())->toJson();
    }
}
