<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Purchase\DTOs\PurchasePaymentDTO;
use App\Base\Repository;
use App\Domain\Purchase\Entities\PurchasePaymentEntity;
use App\Domain\Purchase\Repositories\PurchasePaymentRepositoryInterface;
use App\Models\PurchasePayment;
use Override;

class PurchasePaymentRepository extends Repository implements PurchasePaymentRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new PurchasePayment());
    }

    /**
     * Create (inser)
     */
    #[Override]
    public function create(PurchasePaymentDTO $value): PurchasePaymentEntity
    {
        try {
            $result = $this->model->query()->create($value->toArray());

            return PurchasePaymentEntity::fromModel(
                model: $result->load('user'),
            );
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
