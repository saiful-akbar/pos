<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Sale\Entities\SaleReturnCartEntity;
use App\Domain\Sale\Repositories\SaleReturnCartRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteSaleReturnCartByCurrentUser
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnCartRepositoryInterface $returnCartRepository,
        private ErrorLogRepositoryInterface $errorRepository,
        private ActivityLogRepositoryInterface $activityRepository,
    ) {}

    /**
     * Execute method
     */
    public function execute(): int
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $cart = $this->findReturnCart();
            $deleted = $this->returnCartRepository->deleteById(id: $cart->id);

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: "Membatalkan transaksi pengembalian penjualan dengan id: {$cart->id}",
                event: EventActivityEnum::DELETED,
                attributeChanges: new AttributeActivityDTO(
                    old: $cart->toArray(),
                ),
            ));

            DB::connection(pos_connection())->commit();

            return $deleted;
        } catch (\Throwable $e) {
            DB::connection(pos_connection())->rollBack();

            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data sale_return_carts.
     */
    private function findReturnCart(): SaleReturnCartEntity
    {
        $result = $this->returnCartRepository->findByUserId(
            userId: user()->id,
        );

        if ($result === null) {
            throw ValidationException::withMessages(messages: [
                'invoice_number' => 'Transaksi pengembalian penjualan tidak ditemukan.',
            ]);
        }

        return $result;
    }
}
