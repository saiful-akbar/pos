<?php

declare(strict_types=1);

namespace App\Application\Supplier\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Supplier\DTOs\UpdateSupplierDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
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
     * Execute method.
     */
    public function execute(UpdateSupplierDTO $dto): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $supplier = app(FindSupplierById::class)->execute(id: $dto->id);

            if ($supplier === null) {
                abort(404);
            }

            $this->supplierRepository->update(dto: $dto);

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: "Memperbarui supplier pada id: {$dto->id}",
                event: EventActivityEnum::UPDATED,
                attributeChanges: new AttributeActivityDTO(
                    old: $supplier->toArray(),
                    attributes: $dto->toArray(),
                ),
            ));

            DB::connection(pos_connection())->commit();
        } catch (\Throwable $e) {
            DB::connection(pos_connection())->rollBack();

            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
