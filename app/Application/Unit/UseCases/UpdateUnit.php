<?php

declare(strict_types=1);

namespace App\Application\Unit\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Unit\DTOs\UnitDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Unit\Entities\UnitEntity;
use App\Domain\Unit\Repositories\UnitRepositoryInterface;
use Illuminate\Support\Facades\DB;

final class UpdateUnit
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private UnitRepositoryInterface $unitRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method
     */
    public function execute(UnitEntity $unit, UnitDTO $value): void
    {
        DB::beginTransaction();

        try {
            $this->unitRepository->update(
                id: $unit->id,
                value: $value,
            );

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Memperbarui (update) data unit (satuan produk)',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    old: $unit->toArray(),
                    attributes: $value->toArray(),
                )
            ));

            DB::commit();
        } catch (\Throwable $e) {
            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
