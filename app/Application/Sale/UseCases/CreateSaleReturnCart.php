<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Sale\DTOs\SaleReturnCartDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Sale\Entities\SaleEntity;
use App\Domain\Sale\Entities\SaleReturnCartEntity;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnCartRepositoryInterface;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateSaleReturnCart
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnCartRepositoryInterface $returnCartRepository,
        private SaleRepositoryInterface $saleRepository,
        private ErrorLogRepositoryInterface $errorRepository,
        private ActivityLogRepositoryInterface $activityRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $invoiceNumber): SaleReturnCartEntity
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $sale = $this->findSaleByInvoice(
                invoiceNumber: $invoiceNumber,
            );

            $returnCart = $this->returnCartRepository->create(dto: new SaleReturnCartDTO(
                saleId: $sale->id,
                userId: user()->id,
                transactionDate: new DateTimeImmutable(),
                totalQuantity: 0,
                totalAmount: 0,
                notes: null,
            ));

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Membuat sesi untuk return penjualan',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $returnCart->toArray(),
                ),
            ));

            DB::connection(pos_connection())->commit();

            return $returnCart;
        } catch (\Throwable $e) {
            DB::connection(pos_connection())->rollBack();

            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data sales berdasarkan nomor foktur.
     */
    private function findSaleByInvoice(string $invoiceNumber): SaleEntity
    {
        $sale = $this->saleRepository->findByInvoice($invoiceNumber);

        if ($sale === null) {
            throw ValidationException::withMessages(messages: [
                'invoice_number' => 'Penjualan tidak ditemukan.',
            ]);
        }

        return $sale;
    }
}
