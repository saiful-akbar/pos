<?php

declare(strict_types=1);

namespace Tests\Feature\Cash;

use App\Application\Purchase\DTOs\PurchaseCartDTO;
use App\Application\Purchase\UseCases\CreatePurchaseCart;
use App\Application\Purchase\UseCases\CreatePurchaseCartDetail;
use App\Application\Sale\UseCases\CreateSaleCart;
use App\Application\Sale\UseCases\CreateSaleCartDetail;
use App\Application\Sale\UseCases\CreateSaleReturnCart;
use App\Application\Sale\UseCases\CreateSaleReturnCartDetail;
use App\Models\CashTransaction;
use App\Models\Product;
use App\Models\PurchasePayment;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\Supplier;
use App\Models\User;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Pengujian pencatatan kas otomatis dari penjualan, pembelian, dan retur. Berjalan pada MySQL "pos"
 * di dalam transaksi yang di-rollback (lihat phpunit.mysql.xml).
 */
class CashAutomationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Koneksi yang dibungkus transaksi.
     *
     * @var string[]
     */
    protected array $connectionsToTransact = ['pos'];

    private User $admin;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::query()
            ->whereHas('role', fn ($query) => $query->where('name', 'Admin'))
            ->firstOrFail();

        $this->product = Product::query()->where('current_stock', '>', 5)->firstOrFail();

        $this->actingAs($this->admin);
    }

    public function test_sale_creates_incoming_cash_for_every_payment_method(): void
    {
        foreach (['cash', 'qris'] as $method) {
            $sale = $this->createSale($method);

            $cash = CashTransaction::query()->where('source_type', 'sale')->where('source_id', $sale->id)->firstOrFail();

            $this->assertSame('INCOMING', $cash->type);
            $this->assertSame(number_format((float) $sale->grand_total, 2, '.', ''), (string) $cash->amount);
            $this->assertSame($sale->invoice_number, $cash->reference_number);
            $this->assertSame('Kas masuk dari penjualan', $cash->notes);
        }
    }

    public function test_failed_sale_does_not_create_cash(): void
    {
        $before = CashTransaction::query()->count();

        $cart = app(CreateSaleCart::class)->execute();
        app(CreateSaleCartDetail::class)->execute(cartId: $cart->id, productCode: $this->product->code);

        // Pembayaran kurang menggagalkan transaksi.
        $this->postJson(route('transactions.sales.store'), [
            'payment_method' => 'cash',
            'paid_amount' => 0,
        ], ['X-Requested-With' => 'XMLHttpRequest'])->assertStatus(422);

        $this->assertSame($before, CashTransaction::query()->count());
    }

    public function test_return_creates_outgoing_cash_only_when_refunded(): void
    {
        $sale = $this->createSale();

        foreach ([false, true] as $refund) {
            $cart = app(CreateSaleReturnCart::class)->execute(invoiceNumber: $sale->invoice_number);
            app(CreateSaleReturnCartDetail::class)->execute(cartId: $cart->id, productCode: $this->product->code);

            $this->post(route('transactions.returns.carts.complete', ['cartId' => $cart->id]), [
                'refund_cash' => $refund ? '1' : '0',
            ])->assertSessionHasNoErrors();

            $returnId = SaleReturn::query()->latest('created_at')->latest('id')->firstOrFail();
            $cash = CashTransaction::query()->where('source_type', 'sale_return')->where('source_id', $returnId->id)->first();

            if ($refund) {
                $this->assertNotNull($cash);
                $this->assertSame('OUTGOING', $cash->type);
                $this->assertSame($returnId->return_number, $cash->reference_number);
            } else {
                $this->assertNull($cash);
            }
        }
    }

    public function test_purchase_payments_create_and_remove_outgoing_cash(): void
    {
        $supplier = Supplier::query()->firstOrFail();

        $cart = app(CreatePurchaseCart::class)->execute(new PurchaseCartDTO(
            userId: $this->admin->id,
            transactionDate: new DateTimeImmutable('today'),
            totalProduct: 0,
            totalQuantity: 0,
            totalPrice: 0,
        ));
        app(CreatePurchaseCartDetail::class)->execute(purchaseCart: $cart, productCode: $this->product->code);

        // Bayar sebagian saat transaksi.
        $this->post(route('transactions.purchases.store'), [
            'supplier_id' => $supplier->id,
            'transaction_date' => date('Y-m-d'),
            'invoice_number' => 'TEST-PO-1',
            'payment_method' => 'cash',
            'amount_paid' => 10,
        ])->assertSessionHasNoErrors();

        $initial = PurchasePayment::query()->latest('created_at')->latest('id')->firstOrFail();
        $cash = CashTransaction::query()->where('source_type', 'purchase_payment')->where('source_id', $initial->id)->firstOrFail();

        $this->assertSame('OUTGOING', $cash->type);
        $this->assertSame('10.00', (string) $cash->amount);
        $this->assertSame('TEST-PO-1', $cash->reference_number);

        // Cicilan berikutnya.
        $this->postJson(route('transactions.purchases.payments.store', ['purchaseId' => $initial->purchase_id]), [
            'payment_date' => date('Y-m-d'),
            'payment_method' => 'cash',
            'amount' => 5,
        ], ['X-Requested-With' => 'XMLHttpRequest'])->assertSuccessful();

        $second = PurchasePayment::query()->where('purchase_id', $initial->purchase_id)->where('id', '!=', $initial->id)->firstOrFail();
        $this->assertSame(1, CashTransaction::query()->where('source_id', $second->id)->count());

        // Menghapus cicilan menghapus kas keluarnya saja.
        $this->deleteJson(route('transactions.purchases.payments.delete', ['purchaseId' => $initial->purchase_id, 'paymentId' => $second->id]), [], ['X-Requested-With' => 'XMLHttpRequest'])->assertSuccessful();

        $this->assertSame(0, CashTransaction::query()->where('source_id', $second->id)->count());
        $this->assertSame(1, CashTransaction::query()->where('source_id', $initial->id)->count());
    }

    public function test_automatic_cash_cannot_be_updated_or_deleted_manually(): void
    {
        $sale = $this->createSale();
        $cash = CashTransaction::query()->where('source_id', $sale->id)->firstOrFail();

        $this->deleteJson(route('cash.transactions.destroy', ['transactionId' => $cash->id]), [], ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertStatus(422);

        $this->patchJson(route('cash.transactions.update', ['transactionId' => $cash->id]), [
            'transaction_date' => date('Y-m-d'),
            'type' => 'INCOMING',
            'amount' => '1.00',
        ], ['X-Requested-With' => 'XMLHttpRequest'])->assertStatus(422);

        $this->assertNull($cash->fresh()->deleted_at);
        $this->assertSame(number_format((float) $sale->grand_total, 2, '.', ''), (string) $cash->fresh()->amount);
    }

    public function test_cash_exports_return_excel_files(): void
    {
        $this->createSale();

        $transactions = $this->get(route('cash.transactions.export', [
            'start_date' => date('Y-m-01'),
            'end_date' => date('Y-m-t'),
        ]));

        $transactions->assertOk();
        $this->assertStringContainsString('transaksi-kas-', $transactions->headers->get('content-disposition'));

        $this->get(route('cash.transactions.export'))->assertSessionHasErrors(['start_date', 'end_date']);

        $reconciliation = $this->get(route('cash.reconciliation.export', ['year' => date('Y')]));

        $reconciliation->assertOk();
        $this->assertStringContainsString('histori-rekonsiliasi-kas-'.date('Y'), $reconciliation->headers->get('content-disposition'));
    }

    /**
     * Membuat penjualan lewat endpoint dan mengembalikan modelnya.
     */
    private function createSale(string $paymentMethod = 'cash'): Sale
    {
        $cart = app(CreateSaleCart::class)->execute();

        app(CreateSaleCartDetail::class)->execute(cartId: $cart->id, productCode: $this->product->code);

        $response = $this->postJson(route('transactions.sales.store'), [
            'payment_method' => $paymentMethod,
            'paid_amount' => 100000000,
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(201);
        $this->assertStringContainsString('/receipt', $response->json('data.receipt_url'));

        return Sale::query()->latest('created_at')->latest('id')->firstOrFail();
    }
}
