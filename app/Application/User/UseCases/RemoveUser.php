<?php

declare(strict_types=1);

namespace App\Application\User\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RemoveUser
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private UserRepositoryInterface $userRepository,
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
            $user = $this->userRepository->findById($id);

            if ($user === null) {
                throw ValidationException::withMessages([
                    'message' => 'User tidak ditemukan.',
                ]);
            }

            $removed = $this->userRepository->removeById($user->id);

            $this->activityRepository->create(new CreateActivityDTO(
                userId: user()->id,
                logName: 'Menghapus user',
                event: EventActivityEnum::REMOVED,
                attributeChanges: new AttributeActivityDTO(
                    old: $user->toArray(),
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
