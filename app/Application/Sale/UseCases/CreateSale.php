<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Cash\DTOs\CashTransactionDTO;
use App\Application\Sale\DTOs\SaleDetailDTO;
use App\Application\Sale\DTOs\SaleDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\Cash\Enums\CashSourceTypeEnum;
use App\Domain\Cash\Enums\CashTransactionTypeEnum;
use App\Domain\Cash\Repositories\CashTransactionRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Sale\Entities\SaleEntity;
use App\Domain\Sale\Repositories\SaleCartDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleCartRepositoryInterface;
use App\Domain\Sale\Repositories\SaleDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use App\Http\Requests\Sales\CreateSaleRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateSale
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
        private SaleDetailRepositoryInterface $saleDetailRepository,
        private SaleCartRepositoryInterface $cartRepository,
        private SaleCartDetailRepositoryInterface $cartDetailRepository,
        private ProductRepositoryInterface $productRepository,
        private CashTransactionRepositoryInterface $cashTransactionRepository,
        public ActivityLogRepositoryInterface $activityRepository,
        public ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(CreateSaleRequest $request): SaleEntity
    {
        DB::beginTransaction();

        try {
            $paymentMethod = $request->validated('payment_method');
            $paidAmount = $request->float('paid_amount');
            $notes = $request->validated('notes');

            // Ambil data keranjang penjualan.
            $cart = $this->cartRepository->find(
                column: 'user_id',
                value: user()->id,
            );

            if ($cart === null) {
                abort(404, 'Transaksi penjualan tidak ditemukan.');
            }

            if ($paidAmount < $cart->grandTotal) {
                throw ValidationException::withMessages(messages: [
                    'paid_amount' => 'Pembayaran kurang',
                ]);
            }

            // insert data penjualan.
            $sale = $this->saleRepository->create(
                value: new SaleDTO(
                    userId: $cart->user->id,
                    customerId: $cart->customer?->id,
                    invoiceNumber: $this->saleRepository->generateInvoiceNumber(),
                    transactionDate: $cart->transactionDate,
                    totalProduct: $cart->totalProduct,
                    totalQuantity: $cart->totalQuantity,
                    totalAmount: $cart->totalAmount,
                    discount: $cart->discount,
                    grandTotal: $cart->grandTotal,
                    paidAmount: $paidAmount,
                    changeAmount: $paidAmount - $cart->grandTotal,
                    paymentMethod: $paymentMethod,
                    notes: $notes,
                ),
            );

            // Ambil daftar detail keranjang penjualan.
            $cartDetails = $this->cartDetailRepository->getBySaleCartId(
                saleCartId: $cart->id,
            );

            // Insert data detail penjualan dan update stok produk
            foreach ($cartDetails as $cartDetail) {
                if ($cartDetail->product->currentStock < ($cartDetail->quantity * $cartDetail->multiplesQuantity)) {
                    throw ValidationException::withMessages(messages: [
                        'product_code' => "Stok produk {$cartDetail->product->name} ({$cartDetail->product->code}) tidak mencukupi.",
                    ]);
                }

                $detail = $this->saleDetailRepository->create(
                    value: new SaleDetailDTO(
                        saleId: $sale->id,
                        productId: $cartDetail->product->id,
                        unitId: $cartDetail->unit->id,
                        multiplesQuantity: $cartDetail->multiplesQuantity,
                        purchasePrice: $cartDetail->purchasePrice,
                        sellingPrice: $cartDetail->sellingPrice,
                        quantity: $cartDetail->quantity,
                        subtotal: $cartDetail->subtotal,
                        discount: $cartDetail->discount,
                        grandTotal: $cartDetail->grandTotal,
                    ),
                );

                // Update stok (current_stock) produk
                $this->productRepository->updateCurrentStock(
                    id: $detail->product->id,
                    value: $detail->product->currentStock - ($detail->quantity * $detail->multiplesQuantity),
                );
            }

            // Catat kas masuk dari penjualan (nominal = grand total penjualan).
            if ($sale->grandTotal > 0) {
                $this->cashTransactionRepository->create(
                    value: new CashTransactionDTO(
                        userId: user()->id,
                        transactionDate: $sale->transactionDate,
                        type: CashTransactionTypeEnum::INCOMING,
                        amount: number_format($sale->grandTotal, 2, '.', ''),
                        notes: 'Kas masuk dari penjualan',
                        referenceNumber: $sale->invoiceNumber,
                        sourceType: CashSourceTypeEnum::SALE,
                        sourceId: $sale->id,
                    ),
                );
            }

            // Hapus data keranjang penjualan
            $this->cartRepository->delete(
                id: $cart->id,
            );

            // Buat catatan aktifitas.
            $this->activityRepository->create(
                dto: new CreateActivityDTO(
                    userId: user()->id,
                    logName: 'Menyimpan transaksi penjualan',
                    event: EventActivityEnum::CREATED,
                    attributeChanges: new AttributeActivityDTO(
                        attributes: $sale->toArray(),
                    ),
                ),
            );

            DB::commit();

            return $sale;
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
