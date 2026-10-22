<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Sale\Repositories\SaleDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RemoveSale
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
        private SaleDetailRepositoryInterface $saleDetailRepository,
        private ActivityLogRepositoryInterface $activityLogRepository,
        private ErrorLogRepositoryInterface $errorLogRepository,
    ) {}

    /**
     * Menghapus penjualan beserta detailnya dan mencatat aktivitas.
     */
    public function execute(string $id): int
    {
        try {
            return DB::transaction(function () use ($id): int {
                // Hapus seluruh detail penjualan.
                $this->saleDetailRepository->deleteBySaleId(
                    saleId: $id,
                );

                // Hapus data utama penjualan.
                $deleted = $this->saleRepository->deleteById(
                    id: $id,
                );

                if ($deleted === 0) {
                    return 0;
                }

                // Catat aktivitas penghapusan penjualan.
                $this->activityLogRepository->create(
                    new CreateActivityDTO(
                        userId: Auth::user()->id,
                        logName: 'sales',
                        event: EventActivityEnum::REMOVED,
                        attributeChanges: new AttributeActivityDTO(
                            old: ['id' => $id],
                        ),
                    ),
                );

                return $deleted;
            });
        } catch (\Throwable $e) {
            $this->errorLogRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
