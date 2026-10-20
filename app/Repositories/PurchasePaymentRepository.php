<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Purchase\DTOs\PurchasePaymentDTO;
use App\Base\Repository;
use App\Domain\Purchase\Entities\PurchasePaymentEntity;
use App\Domain\Purchase\Repositories\PurchasePaymentRepositoryInterface;
use App\Models\PurchasePayment;
use Illuminate\Database\Eloquent\Builder;
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
     * Mengambil daftar pembayaran berdasarkan purchase_id dalam format query builder.
     */
    #[Override]
    public function getQueryByPurchaseId(string $purchaseId): Builder
    {
        try {
            return $this->model->query()
                ->with('user')
                ->where('purchase_id', $purchaseId);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
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

    /**
     * Remove (soft delete) berdasarkan purchase_id.
     */
    #[Override]
    public function removeByPurchaseId(string $purchaseId): int
    {
        try {
            return $this->model->query()
                ->where('purchase_id', $purchaseId)
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
     * Mengambil data berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?PurchasePaymentEntity
    {
        try {
            $result = $this->model->query()
                ->with('user')
                ->find($id);

            if ($result === null) {
                return null;
            }

            return PurchasePaymentEntity::fromModel(
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
     * Hapus permanen berdasarkan id.
     */
    #[Override]
    public function delete(string $id): int
    {
        try {
            return $this->model->query()
                ->where('id', $id)
                ->forceDelete();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghitung jumlah nominal berdasarkan purchase_id.
     */
    #[Override]
    public function sumAmountByPurchaseId(string $purchaseId): float
    {
        try {
            $result = $this->model->query()
                ->where('purchase_id', $purchaseId)
                ->sum('amount');

            return parse_float($result);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
