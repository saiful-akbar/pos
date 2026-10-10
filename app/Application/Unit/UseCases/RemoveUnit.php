<?php

declare(strict_types=1);

namespace App\Application\Unit\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Unit\Entities\UnitEntity;
use App\Domain\Unit\Repositories\UnitRepositoryInterface;
use Illuminate\Support\Facades\DB;

final class RemoveUnit
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
    public function execute(UnitEntity $unit): void
    {
        DB::beginTransaction();

        try {
            $this->unitRepository->remove(
                id: $unit->id,
            );

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Menghapus (soft delete) data unit (satuan produk)',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    old: $unit->toArray(),
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
