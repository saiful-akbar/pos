<?php

declare(strict_types=1);

namespace App\Application\Unit\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Unit\Repositories\UnitRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RemoveUnit
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
     * Execute method.
     */
    public function execute(string $id): int
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $unit = $this->unitRepository->findById($id);

            if ($unit === null) {
                throw ValidationException::withMessages([
                    'message' => 'Data unit produk tidak ditemukan.',
                ]);
            }

            $removed = $this->unitRepository->removeById($unit->id);

            $this->activityRepository->create(new CreateActivityDTO(
                userId: user()->id,
                logName: 'Menghapus unit produk',
                event: EventActivityEnum::REMOVED,
                attributeChanges: new AttributeActivityDTO(
                    old: $unit->toArray(),
                )
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
