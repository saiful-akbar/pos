<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Sale\Repositories\SaleCartRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DeleteSaleCartById
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
    public function execute(string $id): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $cart = $this->cartRepository->findById(id: $id);

            if ($cart === null) {
                abort(404);
            }

            $this->cartRepository->deleteById(id: $id);

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: "Membatalkan transaksi penjualan dengan id: {$cart->id}",
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $cart->toArray(),
                ),
            ));

            DB::connection(pos_connection())->commit();
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
