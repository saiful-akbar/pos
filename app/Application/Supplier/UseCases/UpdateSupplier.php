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

class UpdateSupplier
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
    public function execute(SupplierEntity $supplier, SupplierDTO $value): void
    {
        DB::beginTransaction();

        try {
            $this->supplierRepository->update(
                id: $supplier->id,
                value: $value,
            );

            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Memperbarui (update) data supplier',
                    event: EventActivityEnum::UPDATED,
                    attributeChanges: new AttributeActivityDTO(
                        old: $supplier->toArray(),
                        attributes: $value->toArray(),
                    )
                )
            );

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
