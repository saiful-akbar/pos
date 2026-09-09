<?php

declare(strict_types=1);

namespace App\Application\Discount\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\Discount\Repositories\DiscountRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RemoveDiscountById
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private DiscountRepositoryInterface $discountRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $id): int
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $discount = $this->discountRepository->findById(
                id: $id,
            );

            if ($discount === null) {
                abort(404);
            }

            $removed = $this->discountRepository->removeById(
                id: $id
            );

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: "Menghapus discount pada id: $id",
                event: EventActivityEnum::DELETED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $discount->toArray(),
                ),
            ));

            DB::connection(pos_connection())->commit();

            return $removed;
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
