<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Domain\Sale\Repositories\SaleReturnCartDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnCartRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetSaleReturnCartDetailsTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnCartDetailRepositoryInterface $returnCartDetailRepository,
        private SaleReturnCartRepositoryInterface $returnCartRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(): JsonResponse
    {
        $returnCart = $this->returnCartRepository->findByUserId(
            userId: user()->id,
        );

        $table = DataTables::eloquent(
            builder: $this->returnCartDetailRepository->getByCartIdQuery(
                cartId: $returnCart->id,
            ),
        );

        return $table->toJson();
    }
}
