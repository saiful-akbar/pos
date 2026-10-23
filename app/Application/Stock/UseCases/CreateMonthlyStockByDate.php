<?php

declare(strict_types=1);

namespace App\Application\Stock\UseCases;

use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Stock\Entities\MonthlyStockEntity;
use App\Domain\Stock\Repositories\DailyStockRepositoryInterface;
use App\Domain\Stock\Repositories\MonthlyStockRepositoryInterface;
use DateTimeInterface;
use Illuminate\Validation\ValidationException;

class CreateMonthlyStockByDate
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private DailyStockRepositoryInterface $dailyStockRepository,
        private MonthlyStockRepositoryInterface $monthlyStockRepository,
        private CreateMonthlyStock $createMonthlyStock,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Memilih stok harian berdasarkan tanggal lalu menjadikannya stok bulanan.
     */
    public function execute(DateTimeInterface $date, int $year, int $month): MonthlyStockEntity
    {
        try {
            // 1. Pastikan stok bulanan pada tahun dan bulan tersebut belum ada.
            if ($this->monthlyStockRepository->existsByYearAndMonth(year: $year, month: $month)) {
                throw ValidationException::withMessages(messages: [
                    'month' => 'Sudah ada stok bulanan pada tahun dan bulan yang dipilih.',
                ]);
            }

            // 2. Cari stok harian berdasarkan tanggal.
            $dailyStock = $this->dailyStockRepository->findByDate(date: $date);

            if ($dailyStock === null) {
                throw ValidationException::withMessages(messages: [
                    'date' => 'Stok harian pada tanggal yang dipilih tidak ditemukan.',
                ]);
            }
        } catch (\Throwable $e) {
            // Simpan pesan error.
            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }

        // 3. Insert stok bulanan beserta detailnya (transaksi dan pencatatan aktifitas/error ada di use case ini).
        return $this->createMonthlyStock->execute(
            dailyStockId: $dailyStock->id,
            year: $year,
            month: $month,
        );
    }
}
