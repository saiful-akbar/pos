<?php

declare(strict_types=1);

namespace App\Application\User\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\User\DTOs\CreateUserDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\User\Entities\UserEntity;
use App\Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreateUser
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ActivityLogRepositoryInterface $activityRepository,
    ) {}

    /**
     * Execute method
     */
    public function execute(CreateUserDTO $dto): UserEntity
    {
        return DB::connection(pos_connection())->transaction(function () use ($dto): UserEntity {
            $user = $this->userRepository->create($dto);

            $this->activityRepository->create(new CreateActivityDTO(
                userId: user()->id,
                logName: 'Membuat user baru',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $user->toArray(),
                ),
            ));

            return $user;
        });
    }
}
