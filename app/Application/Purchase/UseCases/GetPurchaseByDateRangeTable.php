<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Domain\Purchase\Enums\PaymentStatusEnum;
use App\Domain\Purchase\Repositories\PurchaseRepositoryInterface;
use DateTimeInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetPurchaseByDateRangeTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(private PurchaseRepositoryInterface $purchaseRepository) {}

    /**
     * Execute method.
     */
    public function execute(DateTimeInterface $fromDate, DateTimeInterface $toDate, ?PaymentStatusEnum $paymentStatus = null): JsonResponse
    {
        $table = DataTables::eloquent(
            builder: $this->purchaseRepository->getByDateRangeQuery(
                fromDate: $fromDate,
                toDate: $toDate,
                paymentStatus: $paymentStatus,
            )
        );

        return $table->toJson();
    }
}
