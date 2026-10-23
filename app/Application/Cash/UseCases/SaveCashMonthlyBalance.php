<?php

declare(strict_types=1);

namespace App\Application\Cash\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Cash\DTOs\CashMonthlyBalanceDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\Cash\Entities\CashMonthlyBalanceEntity;
use App\Domain\Cash\Repositories\CashMonthlyBalanceRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaveCashMonthlyBalance
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CashMonthlyBalanceRepositoryInterface $cashMonthlyBalanceRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Membuat saldo awal periode (hanya jika belum ada) atau memperbarui saldo awal periode yang sama.
     * Perubahan saldo yang sudah ada dicatat beserta nilai lama pada log aktifitas (audit).
     */
    public function execute(CashMonthlyBalanceDTO $value, bool $allowUpdate): CashMonthlyBalanceEntity
    {
        DB::beginTransaction();

        try {
            // 1. Periksa saldo awal yang sudah ada pada periode.
            $existing = $this->cashMonthlyBalanceRepository->findByPeriod(year: $value->year, month: $value->month);

            if ($existing !== null && ! $allowUpdate) {
                throw ValidationException::withMessages(messages: [
                    'opening_balance' => 'Saldo awal periode ini sudah dicatat. Gunakan perubahan saldo untuk memperbaruinya.',
                ]);
            }

            // 2. Perbarui saldo yang ada, atau buat saldo baru.
            if ($existing !== null) {
                $this->cashMonthlyBalanceRepository->update(id: $existing->id, value: $value);
                $balance = $this->cashMonthlyBalanceRepository->findByPeriod(year: $value->year, month: $value->month) ?? $existing;
            } else {
                $balance = $this->cashMonthlyBalanceRepository->create(value: $value);
            }

            // 3. Catat aktifitas; saldo lama disertakan pada perubahan.
            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: $existing !== null ? 'Memperbarui saldo awal kas bulanan' : 'Menyimpan saldo awal kas bulanan',
                    event: $existing !== null ? EventActivityEnum::UPDATED : EventActivityEnum::CREATED,
                    attributeChanges: new AttributeActivityDTO(
                        old: $existing?->toArray(),
                        attributes: $balance->toArray(),
                    ),
                ),
            );

            DB::commit();

            return $balance;
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
