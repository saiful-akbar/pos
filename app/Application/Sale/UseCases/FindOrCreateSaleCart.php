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

class FindOrCreateSaleCart
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleCartRepositoryInterface $saleCartRepository,
        private ActivityLogRepositoryInterface $activityRrepository,
        private ErrorLogRepositoryInterface $errorRrepository,
    ) {}

    /**
     * Execute method.
     *
     * @return SaleCartEntity
     */
    public function execute(): SaleCartEntity
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $saleCart = $this->saleCartRepository->findByUserId(
                userId: user()->id,
            );

            if (is_null($saleCart)) {
                $saleCart = $this->saleCartRepository->create(dto: new CreateSaleCartDTO(
                    userId: user()->id,
                    isPending: false,
                    transactionDate: new DateTimeImmutable(),
                    subtotal: 0,
                    discount: 0,
                    tax: 0,
                    grandTotal: 0
                ));

                $this->activityRrepository->create(dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Memulai transaksi penjualan baru',
                    event: EventActivityEnum::CREATED,
                    attributeChanges: new AttributeActivityDTO(
                        attributes: $saleCart->toArray(),
                    ),
                ));
            }

            DB::connection(pos_connection())->commit();

            return $saleCart;
        } catch (\Throwable $e) {
            DB::connection(pos_connection())->rollBack();

            $this->errorRrepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
