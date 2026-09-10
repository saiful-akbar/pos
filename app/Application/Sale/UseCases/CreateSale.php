<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Sale\DTOs\CreateSaleDetailDTO;
use App\Application\Sale\DTOs\CreateSaleDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Purchase\Enums\PaymentMethodEnum;
use App\Domain\Sale\Repositories\SaleCartDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleCartRepositoryInterface;
use App\Domain\Sale\Repositories\SaleDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use DateTimeImmutable;
use Illuminate\Http\Request;
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
        private CustomerRepositoryInterface $customerRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method
     */
    public function execute(Request $request): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $customerId = $request->validated('customer_id');
            $paymentMethod = $request->enum('payment_method', PaymentMethodEnum::class);
            $paidAmount = $request->integer('paid_amount');
            $changeAmount = $request->integer('change_amount');
            $notes = $request->validated('notes');
            $cartId = $request->validated('cart_id');

            $customer = null;

            // Jika customer dipilih, ambil dan validasi data customer.
            if ($customerId !== null) {
                $customer = $this->customerRepository->findById(id: $customerId);

                if ($customer === null) {
                    throw ValidationException::withMessages(messages: [
                        'customer' => 'Data pelanggan tidak ditemukan.',
                    ]);
                }
            }

            // Ambil data cart
            $cart = $this->cartRepository->findById(id: $cartId);

            // validasi data cart
            if ($cart === null) {
                throw ValidationException::withMessages(messages: [
                    'message' => 'Transaksi tidak ditemukan.',
                ]);
            }

            // Ambil daftar cart detail
            $cartDetails = $this->cartDetailRepository->getByCartId(cartId: $cart->id);

            // Validasi cart detail
            if (count($cartDetails) === 0) {
                throw ValidationException::withMessages(messages: [
                    'message' => 'Belum ada produk yang dipilih.',
                ]);
            }

            // Generate invoice number
            $invoiceNumber = $this->saleRepository->generateInvoiceNumber();
            $changeAmount = $paidAmount - $cart->totalAmount;

            // Validasi pembayaran
            if ($changeAmount < 0) {
                throw ValidationException::withMessages(messages: [
                    'paid_amount' => 'Jumlah pembayaran kurang.',
                ]);
            }

            // Simpan (insert) data sales
            $sale = $this->saleRepository->create(dto: new CreateSaleDTO(
                userId: user()->id,
                customerId: $customer?->id,
                invoiceNumber: $invoiceNumber,
                transactionDate: new DateTimeImmutable(),
                totalQuantity: $cart->totalQuantity,
                totalAmount: $cart->totalAmount,
                paidAmount: $paidAmount,
                changeAmount: $changeAmount,
                paymentMethod: $paymentMethod,
                notes: $notes,
            ));

            // Simpan (insert) data sale detail
            foreach ($cartDetails as $cartDetail) {
                $this->saleDetailRepository->create(dto: new CreateSaleDetailDTO(
                    saleId: $sale->id,
                    productId: $cartDetail->product->id,
                    quantity: $cartDetail->quantity,
                    purchasePrice: $cartDetail->purchasePrice,
                    sellingPrice: $cartDetail->sellingPrice,
                    discount: $cartDetail->discount,
                    subtotal: $cartDetail->subtotal,
                ));
            }

            // Hapus cart
            $this->cartRepository->deleteById(id: $cart->id);

            // Buat catatan aktifitas
            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Membuat penjualan baru',
                event: EventActivityEnum::CREATED,
                attributeChanges: new AttributeActivityDTO(
                    attributes: $sale->toArray(),
                ),
            ));

            // commit transaction
            DB::connection(pos_connection())->commit();
        } catch (\Throwable $e) {
            DB::connection(pos_connection())->rollBack();

            // Simpan catatan error.
            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
