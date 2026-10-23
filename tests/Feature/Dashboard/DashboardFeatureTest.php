<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Tests\TestCase;

/**
 * Pengujian Dashboard. Berjalan pada koneksi MySQL "pos" di dalam transaksi yang di-rollback (lihat phpunit.mysql.xml).
 * Tanggal "hari ini" dikunci pada 2031-06-15 sehingga data uji tidak bercampur dengan data nyata.
 */
class DashboardFeatureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Koneksi yang dibungkus transaksi.
     *
     * @var string[]
     */
    protected array $connectionsToTransact = ['pos'];

    private User $admin;

    private Category $category;

    private Unit $unit;

    private int $sequence = 0;

    protected function setUp(): void
    {
        parent::setUp();

        Model::unguard();
        Carbon::setTestNow('2031-06-15 10:00:00');

        // Produk nyata dinonaktifkan (di dalam transaksi) agar tidak memengaruhi daftar stok menipis dan slow moving.
        Product::query()->update(['is_active' => false]);

        $this->admin = User::query()->whereHas('role', fn ($query) => $query->where('name', 'Admin'))->firstOrFail();
        $this->category = Category::query()->firstOrFail();
        $this->unit = Unit::query()->firstOrFail();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_page_shows_shortcuts_to_existing_cart_routes(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertOk();
        $response->assertSee(route('transactions.sales.carts'), false);
        $response->assertSee(route('transactions.purchases.carts'), false);
        $response->assertSee('Transaksi Penjualan');
        $response->assertSee('Transaksi Pembelian');
    }

    public function test_sales_totals_for_today_month_and_year_without_duplication(): void
    {
        $product = $this->product('TST-A');
        $other = $this->product('TST-B');

        // Satu penjualan dengan banyak detail: nilai header tidak boleh berlipat.
        $this->sale('2031-06-15', '3000', [[$product, 1, 1, '1000'], [$other, 1, 1, '1000'], [$product, 1, 1, '1000']]);
        $this->sale('2031-06-02', '500');
        $this->sale('2031-01-10', '100');
        $this->sale('2030-12-31', '999');   // tahun lalu
        $this->sale('2031-06-16', '777');   // lewat hari ini
        $this->sale('2031-06-15', '5000')->delete(); // soft delete

        $totals = $this->ajax()->get('/dashboard/summary')->assertOk()->json('data.totals');

        $this->assertSame('3000.00', $totals['today']);
        $this->assertSame('3500.00', $totals['month']);
        $this->assertSame('3600.00', $totals['year']);
    }

    public function test_totals_are_zero_without_sales(): void
    {
        Carbon::setTestNow('2035-03-10 08:00:00');

        $totals = $this->ajax()->get('/dashboard/summary')->assertOk()->json('data.totals');

        $this->assertSame(['today' => '0.00', 'month' => '0.00', 'year' => '0.00'], $totals);
    }

    public function test_monthly_chart_has_twelve_months_with_zero_for_empty_months(): void
    {
        $product = $this->product('TST-A');

        // 2 x 12 = 24 satuan dasar pada Maret; detail yang dihapus tidak dihitung.
        $this->sale('2031-03-10', '1000', [[$product, 2, 12, '1000']]);
        $deleted = $this->sale('2031-03-11', '2000', [[$product, 5, 1, '2000']]);
        $deleted->delete();
        $this->sale('2031-05-05', '250', [[$product, 1, 1, '250']]);

        SaleDetail::query()->where('sale_id', $this->sale('2031-05-06', '300', [[$product, 7, 1, '300']])->id)->delete();

        $data = $this->ajax()->get('/dashboard/sales?year=2031')->assertOk()->json('data');

        $this->assertCount(12, $data['labels']);
        $this->assertCount(12, $data['quantities']);
        $this->assertCount(12, $data['amounts']);
        $this->assertSame('Januari', $data['labels'][0]);
        $this->assertSame('Desember', $data['labels'][11]);
        $this->assertSame('24.00', $data['quantities'][2]);
        $this->assertSame('1000.00', $data['amounts'][2]);
        $this->assertSame('1.00', $data['quantities'][4]);
        $this->assertSame('550.00', $data['amounts'][4]);
        $this->assertSame('0.00', $data['quantities'][0]);
        $this->assertSame('0.00', $data['amounts'][0]);
        $this->assertTrue($data['has_data']);
    }

    public function test_chart_for_year_without_sales_is_empty_but_complete(): void
    {
        $data = $this->ajax()->get('/dashboard/sales?year=2029')->assertOk()->json('data');

        $this->assertFalse($data['has_data']);
        $this->assertSame(array_fill(0, 12, '0.00'), $data['amounts']);
        $this->assertSame(2029, $data['year']);
    }

    public function test_invalid_year_and_period_are_rejected(): void
    {
        $this->ajax()->get('/dashboard/sales?year=1999')->assertUnprocessable();
        $this->ajax()->get('/dashboard/sales?year=2099')->assertUnprocessable();
        $this->ajax()->get('/dashboard/products?period=bulan-depan')->assertUnprocessable();
    }

    public function test_low_stock_lists_active_products_by_urgency(): void
    {
        $this->product('LOW-OUT', '0', '5');
        $this->product('LOW-BELOW', '2', '10');
        $this->product('LOW-EQUAL', '5', '5');
        $this->product('LOW-OK', '50', '5');
        $this->product('LOW-INACTIVE', '0', '5', false);
        $this->product('LOW-DELETED', '0', '5')->delete();

        $lowStock = $this->ajax()->get('/dashboard/summary')->assertOk()->json('data.low_stock');

        $this->assertSame(3, $lowStock['total']);
        $this->assertSame(['LOW-BELOW', 'LOW-OUT', 'LOW-EQUAL'], array_column($lowStock['items'], 'code'));
        $this->assertSame(['below', 'out', 'reached'], array_column($lowStock['items'], 'status'));
        $this->assertSame('-8.00', $lowStock['items'][0]['difference']);
    }

    public function test_fast_moving_is_ordered_by_quantity_and_follows_period(): void
    {
        $many = $this->product('FAST-MANY');   // banyak transaksi, kuantitas kecil
        $big = $this->product('FAST-BIG');     // satu transaksi, kuantitas besar (konversi satuan)
        $old = $this->product('FAST-OLD');

        foreach ([1, 2, 3] as $day) {
            $this->sale(sprintf('2031-06-%02d', $day), '10', [[$many, 1, 1, '10']]);
        }

        $this->sale('2031-06-10', '9000', [[$big, 2, 12, '9000']]);   // 24 satuan dasar
        $this->sale('2031-05-25', '500', [[$old, 100, 1, '500']]);    // di luar bulan ini, di dalam 30 hari
        $this->sale('2031-06-12', '10', [[$many, 1, 1, '10']])->delete(); // soft delete

        $month = $this->ajax()->get('/dashboard/products?period=month')->assertOk()->json('data');
        $this->assertSame(['FAST-BIG', 'FAST-MANY'], array_column($month['fast_moving'], 'code'));
        $this->assertSame('24.00', $month['fast_moving'][0]['quantity']);
        $this->assertSame('9000.00', $month['fast_moving'][0]['value']);
        $this->assertSame('3.00', $month['fast_moving'][1]['quantity']);

        $days = $this->ajax()->get('/dashboard/products?period=30days')->assertOk()->json('data');
        $this->assertSame('FAST-OLD', $days['fast_moving'][0]['code']);

        $year = $this->ajax()->get('/dashboard/products?period=year')->assertOk()->json('data');
        $this->assertContains('FAST-OLD', array_column($year['fast_moving'], 'code'));
        $this->assertSame('2031-01-01', $year['from']);
    }

    public function test_slow_moving_prioritizes_unsold_products_and_includes_them(): void
    {
        $low = $this->product('SLOW-LOW');
        $good = $this->product('SLOW-GOOD');
        $this->product('SLOW-NONE');                     // tanpa penjualan sama sekali
        $this->product('SLOW-EMPTY', '0');               // stok kosong: tidak layak
        $this->product('SLOW-INACTIVE', '10', '0', false);
        $this->product('SLOW-DELETED')->delete();

        $this->sale('2031-06-05', '10', [[$low, 1, 1, '10']]);
        $this->sale('2031-06-06', '900', [[$good, 90, 1, '900']]);

        $slow = $this->ajax()->get('/dashboard/products?period=month')->assertOk()->json('data.slow_moving');

        $this->assertSame(['SLOW-NONE', 'SLOW-LOW', 'SLOW-GOOD'], array_column($slow, 'code'));
        $this->assertSame([false, true, true], array_column($slow, 'sold'));
        $this->assertSame('0.00', $slow[0]['quantity']);
        $this->assertSame('0.00', $slow[0]['value']);
    }

    public function test_products_endpoint_returns_empty_lists_when_no_sales(): void
    {
        Carbon::setTestNow('2035-03-10 08:00:00');

        $data = $this->ajax()->get('/dashboard/products?period=month')->assertOk()->json('data');

        $this->assertSame([], $data['fast_moving']);
    }

    public function test_dashboard_queries_do_not_grow_with_data(): void
    {
        $queries = function (): int {
            DB::connection('pos')->flushQueryLog();
            DB::connection('pos')->enableQueryLog();

            $this->ajax()->get('/dashboard/summary')->assertOk();
            $this->ajax()->get('/dashboard/sales?year=2031')->assertOk();
            $this->ajax()->get('/dashboard/products?period=year')->assertOk();

            return count(DB::connection('pos')->getQueryLog());
        };

        $product = $this->product('N1-A', '1', '5');
        $this->sale('2031-06-01', '10', [[$product, 1, 1, '10']]);
        $few = $queries();

        foreach (range(1, 25) as $i) {
            $item = $this->product('N1-X'.$i, '1', '5');
            $this->sale('2031-06-02', '10', [[$item, 1, 1, '10'], [$product, 1, 1, '10']]);
        }

        $this->assertSame($few, $queries());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_user_without_module_access_is_denied_data_and_shortcuts(): void
    {
        $role = Role::query()->create(['name' => 'TanpaAkses'.random_int(1000, 9999)]);
        $user = $this->admin->replicate();
        $user->username = 'tmp_dashboard_tanpa_akses';
        $user->role_id = $role->id;
        $user->save();

        $headers = ['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'];

        $page = $this->actingAs($user)->get('/dashboard');
        $page->assertOk();
        $page->assertDontSee(route('transactions.sales.carts'), false);

        $this->actingAs($user)->withHeaders($headers)->get('/dashboard/sales?year=2031')->assertForbidden();
        $this->actingAs($user)->withHeaders($headers)->get('/dashboard/products')->assertForbidden();
        $this->actingAs($user)->withHeaders($headers)->get('/dashboard/summary')->assertOk()->assertJsonPath('data', []);
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect();
    }

    private function product(string $code, string $stock = '10', string $minimum = '0', bool $active = true): Product
    {
        return Product::query()->create([
            'category_id' => $this->category->id,
            'code' => $code,
            'name' => 'Produk '.$code,
            'minimum_stock' => $minimum,
            'current_stock' => $stock,
            'is_active' => $active,
        ]);
    }

    private function sale(string $date, string $grandTotal, array $details = []): Sale
    {
        $this->sequence++;

        $sale = Sale::query()->create([
            'user_id' => $this->admin->id,
            'invoice_number' => sprintf('INV3199-%05d', $this->sequence),
            'transaction_date' => $date,
            'total_product' => count($details),
            'total_quantity' => 1,
            'total_amount' => $grandTotal,
            'discount' => 0,
            'grand_total' => $grandTotal,
            'paid_amount' => $grandTotal,
            'change_amount' => 0,
            'payment_method' => 'cash',
        ]);

        foreach ($details as [$product, $quantity, $multiples, $value]) {
            SaleDetail::query()->create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'unit_id' => $this->unit->id,
                'multiples_quantity' => $multiples,
                'purchase_price' => 1,
                'selling_price' => 1,
                'quantity' => $quantity,
                'subtotal' => $value,
                'discount' => 0,
                'grand_total' => $value,
            ]);
        }

        return $sale;
    }

    private function ajax(): static
    {
        return $this->actingAs($this->admin)->withHeaders(['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json']);
    }
}
