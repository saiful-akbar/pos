<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Sale\DTOs\SaleReturnCartDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
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
        private SaleRepositoryInterface $saleRepository,
        private SaleReturnCartRepositoryInterface $cartRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $invoiceNumber): SaleReturnCartEntity
    {
        DB::beginTransaction();

        try {
            // Ambil data penjualan berdasarkan nomor invoice.
            $sale = $this->saleRepository->find(
                column: 'invoice_number',
                value: $invoiceNumber,
            );

            if ($sale === null) {
                throw ValidationException::withMessages(messages: [
                    'invoice_number' => "Penjualan dengan nomor faktur {$invoiceNumber} tidak ditemukan.",
                ]);
            }

            // Hapus data keranjang pengembalian penjualan milik user yang sedang login jika sudah ada.
            $existingCart = $this->cartRepository->find(
                column: 'user_id',
                value: user()->id,
            );

            if ($existingCart !== null) {
                $this->cartRepository->deleteByUserId(
                    userId: user()->id,
                );
            }

            // Buat data keranjang pengembalian penjualan baru.
            $cart = $this->cartRepository->create(
                value: new SaleReturnCartDTO(
                    userId: user()->id,
                    saleId: $sale->id,
                    transactionDate: new DateTimeImmutable(),
                    totalProduct: 0,
                    totalQuantity: 0,
                    totalAmount: 0,
                    discount: $sale->discount,
                    grandTotal: 0,
                    notes: null,
                ),
            );

            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Membuat keranjang pengembalian penjualan baru',
                    event: EventActivityEnum::CREATED,
                    attributeChanges: new AttributeActivityDTO(
                        attributes: $cart->toArray(),
                    ),
                ),
            );

            DB::commit();

            return $cart;
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
