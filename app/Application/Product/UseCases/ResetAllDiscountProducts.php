<?php

declare(strict_types=1);

namespace App\Application\Product\UseCases;

use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ResetAllDiscountProducts
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(float $value): void
    {
        DB::beginTransaction();

        try {
            $this->productRepository->resetAllDiscounts(
                value: $value,
            );

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Reset diskon produk',
                event: EventActivityEnum::UPDATED,
            ));

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
