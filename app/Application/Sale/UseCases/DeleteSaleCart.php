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

class DeleteSaleCart
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
    public function execute(string $cartId): void
    {
        DB::beginTransaction();

        try {
            $cart = $this->cartRepository->find(
                column: 'id',
                value: $cartId,
            );

            if ($cart === null) {
                abort(404, 'Transaksi penjualan tidak ditemukan.');
            }

            $this->cartRepository->delete(
                id: $cart->id,
            );

            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Membatalkan (menghapus) keranjang transaksi penjualan',
                    event: EventActivityEnum::CREATED,
                    attributeChanges: new AttributeActivityDTO(
                        attributes: $cart->toArray(),
                    ),
                ),
            );

            DB::commit();
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
