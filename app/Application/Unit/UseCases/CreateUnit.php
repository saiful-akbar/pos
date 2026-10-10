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

final class CreateUnit
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
    public function execute(UnitDTO $value): UnitEntity
    {
        DB::beginTransaction();

        try {
            $created = $this->unitRepository->create(
                value: $value,
            );

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Menambah (insert) data unit (satuan produk)',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $created->toArray(),
                )
            ));

            DB::commit();

            return $created;
        } catch (\Throwable $e) {
            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
