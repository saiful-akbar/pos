<?php

declare(strict_types=1);

namespace Tests\Feature\Cash;

use App\Models\SaleDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Pengujian laporan per produk (pembelian, penjualan, pengembalian). Berjalan pada MySQL "pos"
 * di dalam transaksi yang di-rollback (lihat phpunit.mysql.xml).
 */
class ProductReportTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Koneksi yang dibungkus transaksi.
     *
     * @var string[]
     */
    protected array $connectionsToTransact = ['pos'];

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::query()->whereHas('role', fn ($query) => $query->where('name', 'Admin'))->firstOrFail());
    }

    /**
     * @return array<string, array{string, string[]}>
     */
    public static function reports(): array
    {
        return [
            'pembelian' => ['purchases', ['invoice_number', 'supplier_name', 'product_code', 'product_name', 'unit_name', 'quantity', 'multiples_quantity', 'base_quantity', 'price', 'subtotal']],
            'penjualan' => ['sales', ['invoice_number', 'customer_name', 'product_code', 'selling_price', 'quantity', 'multiples_quantity', 'base_quantity', 'subtotal', 'discount', 'grand_total']],
            'pengembalian' => ['returns', ['return_number', 'invoice_number', 'customer_name', 'product_code', 'quantity', 'multiples_quantity', 'base_quantity', 'subtotal', 'discount', 'grand_total']],
        ];
    }

    /**
     * @dataProvider reports
     *
     * @param string[] $columns
     */
    #[DataProvider('reports')]
    public function test_product_table_returns_expected_columns(string $report, array $columns): void
    {
        $response = $this->getJson(route("reports.$report.products", [
            'start_date' => '2020-01-01',
            'end_date' => '2030-12-31',
            'length' => 5,
            'draw' => 1,
        ]), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $response->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);

        $rows = $response->json('data');

        if ($rows !== []) {
            foreach ($columns as $column) {
                $this->assertArrayHasKey($column, $rows[0]);
            }

            $this->assertEqualsWithDelta(
                (float) $rows[0]['quantity'] * (float) $rows[0]['multiples_quantity'],
                (float) $rows[0]['base_quantity'],
                0.001,
            );
        }
    }

    #[DataProvider('reports')]
    public function test_product_excel_export_is_valid(string $report, array $columns): void
    {
        $response = $this->get(route("reports.$report.products.export.excel", [
            'start_date' => '2020-01-01',
            'end_date' => '2030-12-31',
        ]));

        $response->assertOk();
        $this->assertStringContainsString('per-produk', $response->headers->get('content-disposition'));

        $sheet = IOFactory::load($response->getFile()->getPathname())->getActiveSheet();
        $this->assertSame('No', $sheet->getCell('A5')->getValue());
        $this->assertSame('Tanggal', $sheet->getCell('B5')->getValue());
    }

    #[DataProvider('reports')]
    public function test_product_endpoints_require_dates(string $report, array $columns): void
    {
        $this->getJson(route("reports.$report.products"), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertStatus(422);
    }

    public function test_sales_product_filter_only_returns_matching_rows(): void
    {
        $code = SaleDetail::query()->join('products', 'products.id', '=', 'sale_details.product_id')->value('products.code');

        if ($code === null) {
            $this->markTestSkipped('Belum ada data penjualan.');
        }

        $rows = $this->getJson(route('reports.sales.products', [
            'start_date' => '2020-01-01',
            'end_date' => '2030-12-31',
            'product_code' => $code,
            'length' => 100,
            'draw' => 1,
        ]), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk()->json('data');

        $this->assertNotEmpty($rows);
        $this->assertSame([$code], array_values(array_unique(array_column($rows, 'product_code'))));
    }

    #[DataProvider('reports')]
    public function test_report_pages_contain_product_mode_toggle(string $report, array $columns): void
    {
        $this->get(route("reports.$report"))
            ->assertOk()
            ->assertSee('id="modeProducts"', false)
            ->assertSee('id="panelProducts"', false);
    }
}
