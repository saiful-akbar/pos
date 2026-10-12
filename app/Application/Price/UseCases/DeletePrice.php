<?php

declare(strict_types=1);

namespace App\Application\Price\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Price\Entities\PriceEntity;
use App\Domain\Price\Repositories\PriceRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DeletePrice
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
    public function execute(PriceEntity $price): void
    {
        DB::beginTransaction();

        try {
            $this->priceRepository->delete(
                id: $price->id,
            );

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Menghapus (delete) data harga produk',
                event: EventActivityEnum::DELETED,
                attributeChanges: new AttributeActivityDTO(
                    old: $price->toArray(),
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
