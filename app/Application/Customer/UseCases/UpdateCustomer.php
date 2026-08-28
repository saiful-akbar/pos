<?php

declare(strict_types=1);

namespace App\Application\Customer\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Customer\DTOs\UpdateCustomerDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UpdateCustomer
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
    public function execute(UpdateCustomerDTO $dto): void
    {
        $customer = app(FindCustomerById::class)->execute(id: $dto->id);

        if ($customer === null) {
            abort(404);
        }

        DB::connection(pos_connection())->beginTransaction();

        try {
            $this->customerRepository->update(dto: $dto);

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: "Memperbarui customer produk pada id: {$dto->id}",
                event: EventActivityEnum::UPDATED,
                attributeChanges: new AttributeActivityDTO(
                    old: $customer->toArray(),
                    attributes: $dto->toArray(),
                ),
            ));

            DB::connection(pos_connection())->commit();
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
