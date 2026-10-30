<?php

declare(strict_types=1);

namespace App\Application\Cash\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\Cash\Repositories\CashTransactionRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteCashTransaction
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
     * Menghapus transaksi kas (soft delete); transaksi yang dihapus tidak dihitung pada saldo dan laporan.
     */
    public function execute(string $id): void
    {
        DB::beginTransaction();

        try {
            // 1. Ambil transaksi; error jika tidak ditemukan.
            $transaction = $this->cashTransactionRepository->findById(id: $id);

            if ($transaction === null) {
                abort(404, 'Transaksi kas tidak ditemukan.');
            }

            // Transaksi otomatis tidak boleh diubah manual; koreksi dilakukan lewat transaksi sumbernya.
            if ($transaction->isAutomatic()) {
                throw ValidationException::withMessages(messages: [
                    'message' => 'Transaksi kas otomatis dari transaksi penjualan, pembelian, atau retur tidak dapat dihapus secara manual.',
                ]);
            }

            // 2. Soft delete.
            $this->cashTransactionRepository->delete(id: $transaction->id);

            // 3. Catat aktifitas.
            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Menghapus transaksi kas',
                    event: EventActivityEnum::REMOVED,
                    attributeChanges: new AttributeActivityDTO(
                        old: $transaction->toArray(),
                    ),
                ),
            );

            DB::commit();
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
