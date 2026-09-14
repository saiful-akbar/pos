<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Sale\DTOs\CreateSaleDetailDTO;
use App\Application\Sale\DTOs\CreateSaleDTO;
use App\Application\StockOpname\DTOs\CreateStockMovementDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\Customer\Entities\CustomerEntity;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Purchase\Enums\PaymentMethodEnum;
use App\Domain\Sale\Entities\SaleCartDetailEntity;
use App\Domain\Sale\Entities\SaleCartEntity;
use App\Domain\Sale\Entities\SaleEntity;
use App\Domain\Sale\Repositories\SaleCartDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleCartRepositoryInterface;
use App\Domain\Sale\Repositories\SaleDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use App\Domain\StockOpname\Enums\TypeStockMovementEnum;
use App\Domain\StockOpname\Repositories\StockMovementRepositoryInterface;
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
        private StockMovementRepositoryInterface $stockMovementRepository,
        private ProductRepositoryInterface $productRepository,
    ) {}

    /**
     * Execute method
     */
    public function execute(Request $request): SaleEntity
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $paymentMethod = $request->enum('payment_method', PaymentMethodEnum::class);
            $paidAmount = $request->float('paid_amount');
            $changeAmount = $request->float('change_amount');
            $notes = $request->validated('notes');
            $customerId = $request->validated('customer_id');
            $cartId = $request->validated('cart_id');

            $customer = $this->findCustomer(id: $customerId);
            $cart = $this->findCart(id: $cartId);
            $cartDetails = $this->getCartDetails(cartId: $cart->id);
            $changeAmount = $this->calculateChangeAmount(totalAmount: $cart->totalAmount, paidAmount: $paidAmount);
            $invoiceNumber = $this->saleRepository->generateInvoiceNumber();
            $products = $this->productRepository->getActive();

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
                foreach ($products as $product) {
                    if ($product->id === $cartDetail->product->id) {

                        // Validasi quantity produk
                        if ($product->currentStock < $cartDetail->quantity) {
                            throw ValidationException::withMessages(messages: [
                                'message' => "Stok pada {$product->name} tidak cukup.",
                            ]);
                        }

                        // Insert sale detail
                        $this->saleDetailRepository->create(dto: new CreateSaleDetailDTO(
                            saleId: $sale->id,
                            productId: $cartDetail->product->id,
                            quantity: $cartDetail->quantity,
                            purchasePrice: $cartDetail->purchasePrice,
                            sellingPrice: $cartDetail->sellingPrice,
                            discount: $cartDetail->discount,
                            subtotal: $cartDetail->subtotal,
                        ));

                        // Insert stock movement
                        $this->stockMovementRepository->create(dto: new CreateStockMovementDTO(
                            userId: user()->id,
                            productId: $product->id,
                            type: TypeStockMovementEnum::SALE,
                            quantity: $cartDetail->quantity,
                            stockBefore: $product->currentStock,
                            stockAfter: $product->currentStock - $cartDetail->quantity,
                            referenceId: $sale->id,
                            notes: 'Pengurangan stok dari hasil penjualan'
                        ));

                        // Update stock produk
                        $this->productRepository->updateCurrentStockById(
                            id: $product->id,
                            stock: $product->currentStock - $cartDetail->quantity,
                        );
                    }
                }
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

            return $sale;
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

    /**
     * Mengambil data customer.
     */
    private function findCustomer(?string $id): ?CustomerEntity
    {
        if ($id === null) {
            return null;
        }

        $customer = $this->customerRepository->findById(id: $id);

        if ($customer === null) {
            throw ValidationException::withMessages(messages: [
                'customer' => 'Data pelanggan tidak ditemukan.',
            ]);
        }

        return $customer;
    }

    /**
     * Mengambil data sale cart.
     */
    private function findCart(string $id): SaleCartEntity
    {
        $result = $this->cartRepository->findById(id: $id);

        if ($result === null) {
            throw ValidationException::withMessages(messages: [
                'message' => 'Transaksi tidak ditemukan.',
            ]);
        }

        return $result;
    }

    /**
     * Mengambil daftar cart detail berdasarkan cart id.
     *
     * @return SaleCartDetailEntity[]
     */
    private function getCartDetails(string $cartId): array
    {
        $result = $this->cartDetailRepository->getByCartId(cartId: $cartId);

        if (count($result) === 0) {
            throw ValidationException::withMessages(messages: [
                'message' => 'Belum ada produk yang dipilih.',
            ]);
        }

        return $result;
    }

    /**
     * Menghitung kembalian;
     */
    private function calculateChangeAmount(int|float $totalAmount, int|float $paidAmount): int|float
    {
        $change = $paidAmount - $totalAmount;

        if ($change < 0) {
            throw ValidationException::withMessages(messages: [
                'paid_amount' => 'Jumlah pembayaran kurang.',
            ]);
        }

        return $change;
    }
}
