<?php

declare(strict_types=1);

namespace App\Application\Purchase\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Purchase\Entities\PurchaseDetailEntity;
use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreatePurchaseCart
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PurchaseCartRepositoryInterface $cartRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method
     */
    public function execute(): PurchaseDetailEntity
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $result = $this->cartRepository->create(userId: user()->id);

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Membuat transaksi pembelian baru',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $result->toArray(),
                ),
            ));

            DB::connection(pos_connection())->commit();

            return $result;
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
