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
use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ImportDailyStock
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
     * Execute method.
     *
     * @param  array<int, array{product_code: string, unit_code: string, quantity: float}>  $rows
     */
    public function execute(DateTimeInterface $date, ?string $notes, array $rows): DailyStockEntity
    {
        DB::beginTransaction();

        try {
            // 1. Periksa apakah sudah ada stok harian pada tanggal yang sama.
            if ($this->dailyStockRepository->existsByDate(date: $date)) {
                throw ValidationException::withMessages(messages: [
                    'date' => 'Sudah ada stok harian pada tanggal yang sama.',
                ]);
            }

            if ($rows === []) {
                throw ValidationException::withMessages(messages: [
                    'file' => 'File import tidak berisi data.',
                ]);
            }

            // Pastikan quantity setiap baris lebih besar dari 0 (tidak boleh 0 atau minus).
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

            // 2. Pastikan tidak ada kombinasi kode produk dan kode unit yang sama pada baris berbeda.
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

            // 3. Pastikan kode produk, kode unit, dan harga terdaftar pada database.
            $products = [];
            $units = [];
            $prices = [];
            $missingProducts = [];
            $missingUnits = [];
            $missingPrices = [];

            foreach ($rows as $index => $row) {
                $productCode = $row['product_code'];
                $unitCode = $row['unit_code'];

                // Cache hasil pencarian agar kode yang sama tidak di-query berulang.
                $products[$productCode] ??= $this->productRepository->findByCode(code: $productCode);
                $units[$unitCode] ??= $this->unitRepository->findByCode(code: $unitCode);

                $product = $products[$productCode];
                $unit = $units[$unitCode];

                if ($product === null || ! $product->isActive) {
                    $missingProducts[$productCode] = $productCode;
                }

                if ($unit === null || ! $unit->isActive) {
                    $missingUnits[$unitCode] = $unitCode;
                }

                // Harga hanya bisa diperiksa jika produk dan unit valid.
                if ($product !== null && $unit !== null) {
                    $price = $this->priceRepository->findByProductIdAndUnitId(
                        productId: $product->id,
                        unitId: $unit->id,
                    );

                    if ($price === null) {
                        $missingPrices[] = "{$productCode} ({$unitCode})";
                    } else {
                        $prices[$index] = $price;
                    }
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

            if ($missingPrices !== []) {
                throw ValidationException::withMessages(messages: [
                    'file' => 'Produk dan unit berikut belum memiliki harga: '.implode(', ', $missingPrices),
                ]);
            }

            // 4. Insert data stok harian (total diperbarui setelah detail tersimpan).
            $dailyStock = $this->dailyStockRepository->create(
                value: new DailyStockDTO(
                    userId: user()->id,
                    date: $date,
                    totalProduct: 0,
                    totalQuantity: 0,
                    notes: $notes,
                ),
            );

            // 5. Insert detail stok harian; multiples_quantity diambil dari tabel prices.
            $totalProduct = 0.0;
            $totalQuantity = 0.0;

            foreach ($rows as $index => $row) {
                $this->dailyStockDetailRepository->create(
                    value: new DailyStockDetailDTO(
                        dailyStockId: $dailyStock->id,
                        productId: $prices[$index]->product->id,
                        unitId: $prices[$index]->unit->id,
                        quantity: $row['quantity'],
                        multiplesQuantity: $prices[$index]->multiplesQuantity,
                    ),
                );

                $totalProduct += 1;
                $totalQuantity += $row['quantity'];
            }

            // 6. Update total_product dan total_quantity pada stok harian.
            $this->dailyStockRepository->update(
                id: $dailyStock->id,
                value: new DailyStockDTO(
                    userId: $dailyStock->user->id,
                    date: $dailyStock->date,
                    totalProduct: $totalProduct,
                    totalQuantity: $totalQuantity,
                    notes: $dailyStock->notes,
                ),
            );

            // 7. Buat catatan aktifitas.
            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Import excel stok harian',
                    event: EventActivityEnum::CREATED,
                    attributeChanges: new AttributeActivityDTO(
                        attributes: [
                            'id' => $dailyStock->id,
                            'date' => $date->format('Y-m-d'),
                            'total_product' => $totalProduct,
                            'total_quantity' => $totalQuantity,
                            'notes' => $notes,
                        ],
                    ),
                ),
            );

            DB::commit();

            return $dailyStock;
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
