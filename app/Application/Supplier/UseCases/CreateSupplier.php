<?php

declare(strict_types=1);

namespace App\Application\Supplier\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Supplier\DTOs\SupplierDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Supplier\Entities\SupplierEntity;
use App\Domain\Supplier\Repositories\SupplierRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreateSupplier
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SupplierRepositoryInterface $supplierRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * execute method.
     */
    public function execute(SupplierDTO $value): SupplierEntity
    {
        DB::beginTransaction();

        try {
            $created = $this->supplierRepository->create(
                value: $value
            );

            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Membuat (insert) data supplier baru',
                    event: EventActivityEnum::CREATED,
                    attributeChanges: new AttributeActivityDTO(
                        attributes: $created->toArray(),
                    )
                )
            );

            DB::commit();

            return $created;
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
