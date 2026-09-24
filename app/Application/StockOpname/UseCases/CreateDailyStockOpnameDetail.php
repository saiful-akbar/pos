<?php

declare(strict_types=1);

namespace App\Application\StockOpname\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\StockOpname\DTOs\DailyStockOpnameDetailDTO;
use App\Application\StockOpname\DTOs\DailyStockOpnameDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Entities\ProductEntity;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\StockOpname\Entities\DailyStockOpnameEntity;
use App\Domain\StockOpname\Repositories\DailyStockOpnameDetailRepositoryInterface;
use App\Domain\StockOpname\Repositories\DailyStockOpnameRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateDailyStockOpnameDetail
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private DailyStockOpnameRepositoryInterface $dailyStockOpnameRepository,
        private DailyStockOpnameDetailRepositoryInterface $dailyStockOpnameDetailRepository,
        private ProductRepositoryInterface $productRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method
     */
    public function execute(string $id, string $productCode, int|float $quantity): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $dailyStockOpname = $this->findDailyStockOpname($id);
            $product = $this->findProduct($productCode);

            // Periksa apakah stok dengan produk yang dikirim sudah ada atau belum.
            $exists = $this->dailyStockOpnameDetailRepository->existsByDailyStockOpnameIdAndProductId(
                dailyStockOpnameId: $dailyStockOpname->id,
                productId: $product->id,
            );

            if ($exists) {
                throw ValidationException::withMessages(messages: [
                    'product_code' => 'Stok dengan produk ini sudah ada.',
                ]);
            }

            // Insert daily stock opname detail.
            $dailyStockOpnameDetail = $this->dailyStockOpnameDetailRepository->create(
                value: new DailyStockOpnameDetailDTO(
                    dailyStockOpnameId: $dailyStockOpname->id,
                    productId: $product->id,
                    quantity: $quantity,
                ),
            );

            // Ambil total
            $totalProduct = $this->dailyStockOpnameDetailRepository->countProductByDailyStockOpnameId($dailyStockOpname->id);
            $totalQuantity = $this->dailyStockOpnameDetailRepository->sumQuantityByDailyStockOpnameId($dailyStockOpname->id);

            // Update daily stock opname
            $this->dailyStockOpnameRepository->update(
                id: $dailyStockOpname->id,
                value: new DailyStockOpnameDTO(
                    userId: $dailyStockOpname->user->id,
                    date: $dailyStockOpname->date,
                    totalProduct: $totalProduct,
                    totalQuantity: $totalQuantity,
                ),
            );

            // Buat catatan aktifitas
            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: "Menambah produk pada daily stock opname detail dengan id: {$dailyStockOpnameDetail->id}",
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $dailyStockOpnameDetail->toArray(),
                ),
            ));

            DB::connection(pos_connection())->commit();
        } catch (\Throwable $e) {
            DB::connection(pos_connection())->rollBack();

            // Simpan catatan error.
            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data daily stock opname berdasarkan id.
     */
    private function findDailyStockOpname(string $id): DailyStockOpnameEntity
    {
        $result = $this->dailyStockOpnameRepository->findById($id);

        if ($result === null) {
            throw ValidationException::withMessages(messages: [
                'message' => 'Stock opname harian tidak ditemukan.',
            ]);
        }

        return $result;
    }

    /**
     * Mengambil data produk berdasarkan kode atau barcode.
     */
    private function findProduct(string $codeOrBarcode): ProductEntity
    {
        $result = $this->productRepository->findByCodeOrBarcode(
            value: $codeOrBarcode,
        );

        if ($result === null) {
            throw ValidationException::withMessages(messages: [
                'product_code' => 'Produk tidak ditemukan.',
            ]);
        }

        return $result;
    }
}
