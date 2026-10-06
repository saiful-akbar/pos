<?php

declare(strict_types=1);

namespace App\Application\Supplier\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Supplier\Entities\SupplierEntity;
use App\Domain\Supplier\Repositories\SupplierRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RemoveSupplier
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
    public function execute(SupplierEntity $supplier): void
    {
        DB::beginTransaction();

        try {
            $this->supplierRepository->remove(
                id: $supplier->id,
            );

            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Menghapus (soft delete) data supplier',
                    event: EventActivityEnum::REMOVED,
                    attributeChanges: new AttributeActivityDTO(
                        old: $supplier->toArray(),
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
