<?php

declare(strict_types=1);

namespace App\Application\Stock\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Stock\Repositories\DailyStockRepositoryInterface;
use App\Domain\Stock\Repositories\MonthlyStockRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteDailyStock
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private DailyStockRepositoryInterface $dailyStockRepository,
        private MonthlyStockRepositoryInterface $monthlyStockRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Menghapus permanen stok harian beserta detailnya.
     */
    public function execute(string $id): void
    {
        DB::beginTransaction();

        try {
            // 1. Ambil stok harian; error jika tidak ditemukan.
            $dailyStock = $this->dailyStockRepository->findById(
                id: $id,
            );

            if ($dailyStock === null) {
                abort(404, 'Data stok harian tidak ditemukan.');
            }

            // 2. Stok harian yang sudah dijadikan stok bulanan tidak boleh dihapus (relasi restrict).
            if ($this->monthlyStockRepository->existsByDailyStockId(dailyStockId: $dailyStock->id)) {
                throw ValidationException::withMessages(messages: [
                    'id' => 'Stok harian tidak bisa dihapus karena sudah dijadikan sebagai stok bulanan.',
                ]);
            }

            // 3. Hapus permanen; detail stok harian ikut terhapus oleh relasi cascade.
            $this->dailyStockRepository->delete(
                id: $dailyStock->id,
            );

            // 4. Buat catatan aktifitas.
            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Menghapus permanen data stok harian',
                    event: EventActivityEnum::DELETED,
                    attributeChanges: new AttributeActivityDTO(
                        old: $dailyStock->toArray(),
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
