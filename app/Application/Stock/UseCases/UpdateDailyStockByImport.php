<?php

declare(strict_types=1);

namespace App\Application\Stock\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Stock\DTOs\DailyStockDetailDTO;
use App\Application\Stock\DTOs\DailyStockDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Price\Repositories\PriceRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Stock\Entities\DailyStockEntity;
use App\Domain\Stock\Repositories\DailyStockDetailRepositoryInterface;
use App\Domain\Stock\Repositories\DailyStockRepositoryInterface;
use App\Domain\Unit\Repositories\UnitRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateDailyStockByImport
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private DailyStockRepositoryInterface $dailyStockRepository,
        private DailyStockDetailRepositoryInterface $dailyStockDetailRepository,
        private ProductRepositoryInterface $productRepository,
        private UnitRepositoryInterface $unitRepository,
        private PriceRepositoryInterface $priceRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Memperbarui stok harian dari hasil import excel.
     *
     * @param array<int, array{product_code: string, unit_code: string, quantity: float}> $rows
     */
    public function execute(string $dailyStockId, ?string $notes, array $rows): DailyStockEntity
    {
        DB::beginTransaction();

        try {
            // 1. Ambil stok harian (tanggal tidak diubah).
            $dailyStock = $this->dailyStockRepository->findById(
                id: $dailyStockId,
            );

            if ($dailyStock === null) {
                abort(404, 'Data stok harian tidak ditemukan.');
            }

            if ($rows === []) {
                throw ValidationException::withMessages(messages: [
                    'file' => 'File import tidak berisi data.',
                ]);
            }

            // 2. Pastikan quantity setiap baris lebih besar dari 0.
            $invalidQuantities = [];

            foreach ($rows as $row) {
                if ($row['quantity'] <= 0) {
                    $invalidQuantities[] = "{$row['product_code']} ({$row['unit_code']})";
                }
            }

            if ($invalidQuantities !== []) {
                throw ValidationException::withMessages(messages: [
                    'file' => 'Qty harus lebih besar dari 0: '.implode(', ', $invalidQuantities),
                ]);
            }

            // 3. Pastikan tidak ada kombinasi kode produk dan kode unit yang sama pada baris berbeda.
            $seen = [];
            $duplicates = [];

            foreach ($rows as $row) {
                $key = mb_strtolower($row['product_code']).'|'.mb_strtolower($row['unit_code']);

                if (isset($seen[$key])) {
                    $duplicates[$key] = "{$row['product_code']} ({$row['unit_code']})";
                }

                $seen[$key] = true;
            }

            if ($duplicates !== []) {
                throw ValidationException::withMessages(messages: [
                    'file' => 'Terdapat duplikat kode produk dan unit: '.implode(', ', $duplicates),
                ]);
            }

            // 4. Pastikan kode produk dan kode unit terdaftar pada database.
            $products = [];
            $units = [];
            $missingProducts = [];
            $missingUnits = [];

            foreach ($rows as $row) {
                $productCode = $row['product_code'];
                $unitCode = $row['unit_code'];

                // Cache hasil pencarian agar kode yang sama tidak di-query berulang.
                $products[$productCode] ??= $this->productRepository->findByCode(code: $productCode);
                $units[$unitCode] ??= $this->unitRepository->findByCode(code: $unitCode);

                if ($products[$productCode] === null || ! $products[$productCode]->isActive) {
                    $missingProducts[$productCode] = $productCode;
                }

                if ($units[$unitCode] === null || ! $units[$unitCode]->isActive) {
                    $missingUnits[$unitCode] = $unitCode;
                }
            }

            if ($missingProducts !== []) {
                throw ValidationException::withMessages(messages: [
                    'file' => 'Kode produk tidak terdaftar atau tidak aktif: '.implode(', ', $missingProducts),
                ]);
            }

            if ($missingUnits !== []) {
                throw ValidationException::withMessages(messages: [
                    'file' => 'Kode unit tidak terdaftar atau tidak aktif: '.implode(', ', $missingUnits),
                ]);
            }

            // 5. Update quantity jika detail sudah ada; jika belum, pastikan harga terdaftar lalu insert detail baru.
            $updated = 0;
            $inserted = 0;
            $missingPrices = [];
            $newDetails = [];

            foreach ($rows as $row) {
                $product = $products[$row['product_code']];
                $unit = $units[$row['unit_code']];

                $detail = $this->dailyStockDetailRepository->findByDailyStockIdProductIdAndUnitId(
                    dailyStockId: $dailyStock->id,
                    productId: $product->id,
                    unitId: $unit->id,
                );

                // Detail sudah ada: update quantity.
                if ($detail !== null) {
                    $this->dailyStockDetailRepository->updateQuantity(
                        id: $detail->id,
                        quantity: $row['quantity'],
                    );

                    $updated++;

                    continue;
                }

                // Detail belum ada: periksa harga produk dan unit pada tabel prices.
                $price = $this->priceRepository->findByProductIdAndUnitId(
                    productId: $product->id,
                    unitId: $unit->id,
                );

                if ($price === null) {
                    $missingPrices[] = "{$row['product_code']} ({$row['unit_code']})";

                    continue;
                }

                $newDetails[] = new DailyStockDetailDTO(
                    dailyStockId: $dailyStock->id,
                    productId: $product->id,
                    unitId: $unit->id,
                    quantity: $row['quantity'],
                    multiplesQuantity: $price->multiplesQuantity,
                );
            }

            if ($missingPrices !== []) {
                throw ValidationException::withMessages(messages: [
                    'file' => 'Produk dan unit berikut belum memiliki harga: '.implode(', ', $missingPrices),
                ]);
            }

            // 6. Insert detail baru.
            foreach ($newDetails as $newDetail) {
                $this->dailyStockDetailRepository->create(value: $newDetail);

                $inserted++;
            }

            // 7. Hitung ulang total dari seluruh detail lalu update stok harian (tanggal tetap, catatan diperbarui).
            $latest = $this->dailyStockRepository->findById(
                id: $dailyStock->id,
            );

            $totalProduct = (float) count($latest->details);
            $totalQuantity = 0.0;

            foreach ($latest->details as $detail) {
                $totalQuantity += $detail->quantity;
            }

            $this->dailyStockRepository->update(
                id: $dailyStock->id,
                value: new DailyStockDTO(
                    userId: $dailyStock->user->id,
                    date: $dailyStock->date,
                    totalProduct: $totalProduct,
                    totalQuantity: $totalQuantity,
                    notes: $notes,
                ),
            );

            // 8. Buat catatan aktifitas.
            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Import update stok harian',
                    event: EventActivityEnum::UPDATED,
                    attributeChanges: new AttributeActivityDTO(
                        attributes: [
                            'id' => $dailyStock->id,
                            'date' => $dailyStock->date->format('Y-m-d'),
                            'total_product' => $totalProduct,
                            'total_quantity' => $totalQuantity,
                            'notes' => $notes,
                            'updated_rows' => $updated,
                            'inserted_rows' => $inserted,
                        ],
                    ),
                ),
            );

            DB::commit();

            // Kembalikan stok harian terbaru (total sudah diperbarui).
            return $this->dailyStockRepository->findById(id: $dailyStock->id) ?? $latest;
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
