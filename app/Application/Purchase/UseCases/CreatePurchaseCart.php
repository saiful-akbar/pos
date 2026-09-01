<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Purchase\DTOs\CreatePurchaseCartDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Purchase\Entities\PurchaseCartEntity;
use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

class CreatePurchaseCart
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchaseCartRepositoryInterface $purchaseCartRepository,
        private ErrorLogRepositoryInterface $errorRepository,
        private ActivityLogRepositoryInterface $activityRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(): PurchaseCartEntity
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $cart = $this->purchaseCartRepository->create(dto: new CreatePurchaseCartDTO(
                userId: user()->id,
                purchaseDate: new DateTimeImmutable(),
                totalQuantity: 0,
                totalPrice: 0,
            ));

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Membuat penjualan baru',
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
