<?php

declare(strict_types=1);

namespace App\Application\User\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\User\DTOs\UpdateUserDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\User\Entities\UserEntity;
use App\Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateUser
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ActivityLogRepositoryInterface $activityRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(Request $request, UserEntity $user): void
    {
        DB::transaction(function () use ($request, $user): void {
            $password = null;
            $photo = null;

            if ($request->hasFile('photo')) {
                if ($user->photo !== null) {
                    Storage::disk('public')->delete($user->photo);
                }

                $photo = $request->file('photo')->store('images/users', 'public');
            }

            if ($request->validated('password') !== null) {
                $password = bcrypt($request->validated('password'));
            }

            $dto = new UpdateUserDTO(
                id: $user->id,
                roleId: $request->validated('role'),
                fullName: $request->validated('full_name'),
                username: $request->validated('username'),
                password: $password,
                photo: $photo,
                isActive: $request->boolean('status')
            );

            $this->userRepository->update($dto);

            $this->activityRepository->create(new CreateActivityDTO(
                userId: user()->id,
                logName: "Memperbarui user pada id: {$user->id}",
                event: EventActivityEnum::UPDATED,
                attributeChanges: new AttributeActivityDTO(
                    old: $user->toArray(),
                    attributes: $dto->toArray(),
                ),
            ));
        });
    }
}
