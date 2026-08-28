<?php

declare(strict_types=1);

namespace App\Application\Role\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\ActivityLog\UseCases\CreateActivity;
use App\Application\Role\DTOs\CreateRoleDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\Role\Repositories\RoleRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreateRole
{
    /**
     * Create a new class instance.
     */
    public function __construct(private RoleRepositoryInterface $repository) {}

    /**
     * Execute method.
     */
    public function execute(CreateRoleDTO $dto): void
    {
        DB::connection(pos_connection())->transaction(function () use ($dto): void {
            $role = $this->repository->create($dto);

            app(CreateActivity::class)->execute(new CreateActivityDTO(
                userId: user()->id,
                logName: 'Membuat role baru',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $role->toArray(),
                ),
            ));
        });
    }
}
