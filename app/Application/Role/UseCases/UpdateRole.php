<?php

declare(strict_types=1);

namespace App\Application\Role\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Role\DTOs\UpdateRoleDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\Role\Repositories\RoleRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UpdateRole
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private RoleRepositoryInterface $repository,
        private ActivityLogRepositoryInterface $activityRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(UpdateRoleDTO $dto): void
    {
        $role = $this->repository->findById($dto->id);

        if (is_null($role)) {
            abort(404);
        }

        DB::connection(pos_connection())->transaction(function () use ($dto, $role): void {
            $this->repository->update($dto);

            $this->activityRepository->create(new CreateActivityDTO(
                userId: user()->id,
                logName: "Memperbarui role pada id: {$dto->id}",
                event: EventActivityEnum::UPDATED,
                attributeChanges: new AttributeActivityDTO(
                    old: $role?->toArray(),
                    attributes: $dto->toArray(),
                ),
            ));
        });
    }
}
