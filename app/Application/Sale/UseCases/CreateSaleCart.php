<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Sale\DTOs\SaleCartDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Sale\Entities\SaleCartEntity;
use App\Domain\Sale\Repositories\SaleCartRepositoryInterface;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

class CreateSaleCart
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public SaleCartRepositoryInterface $cartRepository,
        public ActivityLogRepositoryInterface $activityRepository,
        public ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(): SaleCartEntity
    {
        DB::beginTransaction();

        try {
            $cart = $this->cartRepository->create(
                value: new SaleCartDTO(
                    userId: user()->id,
                    customerId: null,
                    transactionDate: new DateTimeImmutable(),
                    totalProduct: 0,
                    totalQuantity: 0,
                    totalAmount: 0,
                    discount: 0,
                    grandTotal: 0,
                ),
            );

            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Membuat keranjang transaksi penjualan baru',
                    event: EventActivityEnum::CREATED,
                    attributeChanges: new AttributeActivityDTO(
                        attributes: $cart->toArray(),
                    ),
                ),
            );

            DB::commit();

            return $cart;
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
