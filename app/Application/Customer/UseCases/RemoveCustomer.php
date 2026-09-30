<?php

declare(strict_types=1);

namespace App\Application\Customer\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RemoveCustomer
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
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
            $customer = $this->customerRepository->findById($id);

            if ($customer === null) {
                throw ValidationException::withMessages([
                    'message' => 'Data pelanggan tidak ditemukan.',
                ]);
            }

            $removed = $this->customerRepository->removeById($customer->id);

            $this->activityRepository->create(new CreateActivityDTO(
                userId: user()->id,
                logName: 'Menghapus pelanggan (customer)',
                event: EventActivityEnum::REMOVED,
                attributeChanges: new AttributeActivityDTO(
                    old: $customer->toArray(),
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
