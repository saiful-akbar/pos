<?php

declare(strict_types=1);

namespace App\Application\Price\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Price\DTOs\UpdatePriceDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Price\Repositories\PriceRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UpdatePrice
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private PriceRepositoryInterface $priceRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(UpdatePriceDTO $value): void
    {
        DB::beginTransaction();

        try {
            // Kunci seluruh harga produk agar perubahan default tidak berjalan bersamaan.
            $this->priceRepository->lockByProductId(
                productId: $value->productId,
            );

            // Ambil data harga lama (setelah dikunci).
            $price = $this->priceRepository->findById(
                id: $value->id,
            );

            // Harga harus ada dan milik produk yang dimaksud.
            if ($price === null || $price->product->id !== $value->productId) {
                abort(404, 'Harga produk tidak ditemukan.');
            }

            // Jika dijadikan default, harga unit lain menjadi bukan default.
            if ($value->isDefault && ! $price->isDefault) {
                $this->priceRepository->unsetDefaultByProductId(
                    productId: $value->productId,
                    exceptId: $value->id,
                );
            }

            // Update harga.
            $this->priceRepository->update(
                value: $value,
            );

            // Catat aktivitas perubahan.
            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: "Memperbarui (update) data harga produk pada id: {$value->id}",
                event: EventActivityEnum::UPDATED,
                attributeChanges: new AttributeActivityDTO(
                    old: $price->toArray(),
                    attributes: $value->toArray(),
                ),
            ));

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
