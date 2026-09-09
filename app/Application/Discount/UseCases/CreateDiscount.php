<?php

declare(strict_types=1);

namespace App\Application\Discount\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Discount\DTOs\DiscountDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\Discount\Entities\DiscountEntity;
use App\Domain\Discount\Repositories\DiscountRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreateDiscount
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
    public function execute(DiscountDTO $dto): DiscountEntity
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $discount = $this->discountRepository->create(dto: $dto);

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Membuat discount baru',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $discount->toArray(),
                ),
            ));

            DB::connection(pos_connection())->commit();

            return $discount;
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
