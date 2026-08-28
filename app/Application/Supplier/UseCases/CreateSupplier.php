<?php

declare(strict_types=1);

namespace App\Application\Supplier\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Supplier\DTOs\CreateSupplierDTO;
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
     * Execute method.
     */
    public function execute(CreateSupplierDTO $dto): SupplierEntity
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $supplier = $this->supplierRepository->create(
                code: app(GenerateSupplierCode::class)->execute(),
                dto: $dto,
            );

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Membuat supplier baru',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $supplier->toArray(),
                ),
            ));

            DB::connection(pos_connection())->commit();

            return $supplier;
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
