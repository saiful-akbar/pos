<?php

declare(strict_types=1);

namespace App\Application\Customer\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Customer\DTOs\CreateCustomerDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\Customer\Entities\CustomerEntity;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreateCustomer
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
    public function execute(CreateCustomerDTO $dto): CustomerEntity
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $customer = $this->customerRepository->create($dto);

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Membuat customer baru',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $customer->toArray(),
                ),
            ));

            DB::connection(pos_connection())->commit();

            return $customer;
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
