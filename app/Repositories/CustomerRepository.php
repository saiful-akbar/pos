<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Customer\DTOs\CustomerDTO;
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
     * Mengambil daftar dalam format query builder.
     */
    #[Override]
    public function getQuery(): Builder
    {
        try {
            return $this->model->query();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data berdasarkan id
     */
    #[Override]
    public function findById(string $id): ?CustomerEntity
    {
        try {
            $result = $this->model->query()->find($id);

            if ($result === null) {
                return null;
            }

            return $this->mapModelToCustomerEntity(
                model: $result,
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
     * Menambah (create)
     */
    #[Override]
    public function create(CustomerDTO $value): CustomerEntity
    {
        try {
            $result = $this->model->query()->create($value->toArray());

            return $this->mapModelToCustomerEntity(
                model: $result,
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
     * Update
     */
    #[Override]
    public function update(string $id, CustomerDTO $value): int
    {
        try {
            return $this->model->query()
                ->where('id', $id)
                ->update($value->toArray());
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Remove (soft delete).
     */
    #[Override]
    public function remove(string $id): int
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
     * Pemetaan class Modal ke class CustomerEntity.
     */
    private function mapModelToCustomerEntity(Customer $model): CustomerEntity
    {
        return new CustomerEntity(
            id: $model->id,
            name: $model->name,
            phoneNumber: $model->phone_number,
            address: $model->address,
            discount: parse_float($model->discount),
            isActive: $model->is_active === 1,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at
        );
    }
}
