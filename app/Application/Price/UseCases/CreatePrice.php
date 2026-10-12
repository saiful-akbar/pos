<?php

declare(strict_types=1);

namespace App\Application\Price\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Price\DTOs\PriceDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Price\Entities\PriceEntity;
use App\Domain\Price\Repositories\PriceRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreatePrice
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
     * Execute method
     */
    public function execute(PriceDTO $value): PriceEntity
    {
        DB::beginTransaction();

        try {
            $exists = $this->priceRepository->findByProductIdAndUnitId(
                productId: $value->productId,
                unitId: $value->unitId,
            );

            if ($exists) {
                throw ValidationException::withMessages(messages: [
                    'unit_id' => 'Harga pada unit (satuan) ini sudah ada.',
                ]);
            }

            if ($value->isDefault) {
                $defaultExists = $this->priceRepository->existsDefaultByProductId(
                    productId: $value->productId,
                );

                if ($defaultExists) {
                    throw ValidationException::withMessages(messages: [
                        'is_default' => 'Sudah ada harga yang dijadikan default.',
                    ]);
                }
            }

            $newPrice = $this->priceRepository->create(
                value: $value,
            );

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Menambah (insert) data harga produk',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $newPrice->toArray(),
                )
            ));

            DB::commit();

            return $newPrice;
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
