<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Customer\DTOs\CreateCustomerDTO;
use App\Application\Customer\DTOs\UpdateCustomerDTO;
use App\Base\Repository;
use App\Domain\Customer\Entities\CustomerEntity;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Override;

class CustomerRepository extends Repository implements CustomerRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Customer());
    }

    /**
     * Mengambil daftar customer dalam format query builder.
     */
    #[Override]
    public function getQuery(?bool $active = null): Builder
    {
        try {
            $query = $this->model->query();

            if ($active === true) {
                $query->whereNotNull('deleted_at');
            }

            if ($active === false) {
                $query->whereNull('deleted_at');
            }

            return $query;
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data customer berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?CustomerEntity
    {
        try {
            $data = $this->model->query()->find($id);

            return $data === null ? null : $this->mapModelToCustomerEntity($data);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Membuat customer baru
     */
    #[Override]
    public function create(CreateCustomerDTO $dto): CustomerEntity
    {
        try {
            return $this->mapModelToCustomerEntity(
                model: $this->model->query()->create(
                    attributes: $dto->toArray()
                )
            );
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Update customer.
     */
    #[Override]
    public function update(UpdateCustomerDTO $dto): void
    {
        try {
            $this->model->query()
                ->where('id', '=', $dto->id)
                ->update($dto->toArray());
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil semua data unit
     *
     * @return CustomerEntity[]
     */
    #[Override]
    public function getAll(): array
    {
        try {
            return $this->model->query()
                ->get()
                ->map(fn ($customer) => $this->mapModelToCustomerEntity($customer))
                ->toArray();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Soft delete
     */
    #[Override]
    public function removeById(string $id): int
    {
        try {
            return $this->model->query()
                ->where('id', $id)
                ->delete();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Pemetaan model ke CustomerEntity.
     */
    private function mapModelToCustomerEntity(Customer $model): CustomerEntity
    {
        return new CustomerEntity(
            id: $model->id,
            name: $model->name,
            phoneNumber: $model->phone_number,
            address: $model->address,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
