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
use Illuminate\Validation\ValidationException;

class UpdateCashTransaction
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
     * Memperbarui transaksi kas; data lama dan baru dicatat pada log aktifitas (audit).
     */
    public function execute(string $id, CashTransactionDTO $value): CashTransactionEntity
    {
        DB::beginTransaction();

        try {
            // 1. Ambil transaksi; error jika tidak ditemukan.
            $old = $this->cashTransactionRepository->findById(id: $id);

            if ($old === null) {
                abort(404, 'Transaksi kas tidak ditemukan.');
            }

            // Transaksi otomatis tidak boleh diubah manual; koreksi dilakukan lewat transaksi sumbernya.
            if ($old->isAutomatic()) {
                throw ValidationException::withMessages(messages: [
                    'message' => 'Transaksi kas otomatis dari transaksi penjualan, pembelian, atau retur tidak dapat diubah secara manual.',
                ]);
            }

            // 2. Perbarui transaksi.
            $this->cashTransactionRepository->update(id: $old->id, value: $value);

            $updated = $this->cashTransactionRepository->findById(id: $old->id);

            // 3. Catat aktifitas beserta data sebelum dan sesudah perubahan.
            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Memperbarui transaksi kas',
                    event: EventActivityEnum::UPDATED,
                    attributeChanges: new AttributeActivityDTO(
                        old: $old->toArray(),
                        attributes: $updated?->toArray(),
                    ),
                ),
            );

            DB::commit();

            return $updated ?? $old;
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
