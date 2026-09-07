<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Sale\DTOs\CreateSaleCartDTO;
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
        private SaleCartRepositoryInterface $cartRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method
     */
    public function execute(): SaleCartEntity
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $cart = $this->cartRepository->create(dto: new CreateSaleCartDTO(
                userId: user()->id,
                isPending: false,
                transactionDate: new DateTimeImmutable(),
                totalQuantity: 0,
                subtotal: 0,
                grandTotal: 0,
            ));

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Membuat transaksi penjualan baru',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $cart->toArray(),
                ),
            ));

            DB::connection(pos_connection())->commit();

            return $cart;
        } catch (\Throwable $e) {
            DB::connection(pos_connection())->rollBack();

            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
