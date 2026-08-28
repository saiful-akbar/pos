<?php

declare(strict_types=1);

namespace App\Application\Category\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Category\DTOs\CreateCategoryDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\Category\Entities\CategoryEntity;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreateCategory
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(CreateCategoryDTO $dto): CategoryEntity
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $category = $this->categoryRepository->create($dto);

            $this->activityRepository->create(new CreateActivityDTO(
                userId: user()->id,
                logName: 'Membuat kategori produk baru',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $category->toArray(),
                ),
            ));

            DB::connection(pos_connection())->commit();

            return $category;
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
