<?php

declare(strict_types=1);

namespace App\Application\Cash\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Cash\DTOs\CashTransactionDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\Cash\Entities\CashTransactionEntity;
use App\Domain\Cash\Repositories\CashTransactionRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreateCashTransaction
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CashTransactionRepositoryInterface $cashTransactionRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Membuat transaksi kas (kas masuk atau kas keluar).
     */
    public function execute(CashTransactionDTO $value): CashTransactionEntity
    {
        DB::beginTransaction();

        try {
            // 1. Simpan transaksi beserta nomor transaksi.
            $transaction = $this->cashTransactionRepository->create(value: $value);

            // 2. Catat aktifitas.
            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Menyimpan transaksi kas',
                    event: EventActivityEnum::CREATED,
                    attributeChanges: new AttributeActivityDTO(
                        attributes: $transaction->toArray(),
                    ),
                ),
            );

            DB::commit();

            return $transaction;
        } catch (\Throwable $e) {
            DB::rollBack();

            // Simpan pesan error.
            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
