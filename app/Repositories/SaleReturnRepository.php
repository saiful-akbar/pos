<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\SaleReturnDTO;
use App\Base\Repository;
use App\Domain\Sale\Entities\SaleReturnEntity;
use App\Domain\Sale\Repositories\SaleReturnRepositoryInterface;
use App\Domain\Sale\ValueObjects\UserSaleValue;
use App\Models\SaleReturn;
use Override;

class SaleReturnRepository extends Repository implements SaleReturnRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new SaleReturn());
    }

    /**
     * Menyimpan data pengembalian penjualan.
     */
    #[Override]
    public function create(SaleReturnDTO $dto): SaleReturnEntity
    {
        try {
            $data = $this->model->query()->create($dto->toArray());

            return $this->mapModelToSaleReturnEntity(
                model: $data->load('user'),
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
     * Pemetaan class model ke class SaleReturnEntity
     */
    private function mapModelToSaleReturnEntity(SaleReturn $model): SaleReturnEntity
    {
        return new SaleReturnEntity(
            id: $model->id,
            saleId: $model->sale_id,
            user: new UserSaleValue(
                id: $model->user->id,
                username: $model->user->username,
                fullName: $model->user->full_name,
                photo: $model->user->photo,
            ),
            transactionDate: parse_date_time_immutable($model->transaction_date),
            totalQuantity: parse_float($model->total_quantity),
            totalAmount: parse_float($model->total_amount),
            notes: $model->notes,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
