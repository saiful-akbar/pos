<?php

declare(strict_types=1);

namespace Tests\Feature\Cash;

use App\Application\Cash\DTOs\CashTransactionDTO;
use App\Application\Cash\UseCases\CreateCashTransaction;
use App\Domain\Cash\Enums\CashTransactionTypeEnum;
use App\Models\CashMonthlyBalance;
use App\Models\CashTransaction;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\ModuleSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Testing\TestResponse;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Tests\TestCase;

/**
 * Pengujian fitur Kas. Berjalan pada koneksi MySQL "pos" di dalam transaksi yang di-rollback
 * (lihat phpunit.mysql.xml). Menu Kas dibuat ulang di dalam transaksi agar hak akses mengikuti ModuleSeeder.
 */
class CashFeatureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Koneksi yang dibungkus transaksi.
     *
     * @var string[]
     */
    protected array $connectionsToTransact = ['pos'];

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Bersihkan menu kas lama (di dalam transaksi) lalu seed ulang agar hak akses sesuai ModuleSeeder terbaru.
        $ids = DB::connection('pos')->table('modules')
            ->where('route', 'like', 'cash%')
            ->orWhere('route', 'reports.cash')
            ->pluck('id');

        DB::connection('pos')->table('role_module')->whereIn('module_id', $ids)->delete();
        DB::connection('pos')->table('modules')->whereIn('id', $ids)->delete();

        (new ModuleSeeder())->run();

        $this->admin = User::query()
            ->whereHas('role', fn ($query) => $query->where('name', 'Admin'))
            ->firstOrFail();
    }

    public function test_tables_exist_with_expected_columns(): void
    {
        $this->assertTrue(Schema::connection('pos')->hasColumns('cash_monthly_balances', ['id', 'user_id', 'year', 'month', 'opening_balance', 'notes', 'deleted_at']));
        $this->assertTrue(Schema::connection('pos')->hasColumns('cash_transactions', ['id', 'user_id', 'transaction_number', 'transaction_date', 'type', 'amount', 'notes', 'reference_number', 'deleted_at']));
        $this->assertFalse(Schema::connection('pos')->hasColumn('cash_transactions', 'category_id'));
    }

    public function test_opening_balance_can_be_created_and_updated(): void
    {
        $this->balance(2026, 10, '1000000')->assertCreated();
        $this->assertSame('1000000.00', $this->summary(2026, 10)['opening_balance']);

        $this->ajax($this->admin)->patch('/cash/reconciliation', ['year' => 2026, 'month' => 10, 'opening_balance' => '1250000.50'])->assertOk();
        $this->assertSame('1250000.50', $this->summary(2026, 10)['opening_balance']);
        $this->assertSame(1, CashMonthlyBalance::query()->where('year', 2026)->where('month', 10)->count());
    }

    public function test_period_cannot_have_duplicate_opening_balance(): void
    {
        $this->balance(2026, 10, '1000')->assertCreated();
        $this->balance(2026, 10, '2000')->assertUnprocessable();

        // Pengaman terakhir: indeks unik database menolak baris ganda.
        $this->expectException(QueryException::class);
        CashMonthlyBalance::query()->create(['user_id' => $this->admin->id, 'year' => 2026, 'month' => 10, 'opening_balance' => '5']);
    }

    public function test_soft_deleted_balance_period_is_restored_without_duplicate(): void
    {
        $this->balance(2026, 11, '1000')->assertCreated();
        CashMonthlyBalance::query()->where('year', 2026)->where('month', 11)->delete();

        $this->balance(2026, 11, '3000')->assertCreated();

        $this->assertSame(1, CashMonthlyBalance::withTrashed()->where('year', 2026)->where('month', 11)->count());
        $this->assertSame('3000.00', $this->summary(2026, 11)['opening_balance']);
    }

    public function test_invalid_opening_balance_is_rejected(): void
    {
        $this->balance(2026, 10, '-5')->assertUnprocessable();
        $this->balance(2026, 13, '5')->assertUnprocessable();
    }

    #[DataProvider('invalidTransactions')]
    public function test_invalid_transaction_is_rejected(array $override, string $field): void
    {
        $payload = array_merge(['transaction_date' => '2026-10-05', 'type' => 'INCOMING', 'amount' => '1000'], $override);

        $this->ajax($this->admin)->post('/cash/transactions', $payload)->assertUnprocessable()->assertJsonValidationErrors($field);
    }

    public static function invalidTransactions(): array
    {
        return [
            'nominal nol' => [['amount' => '0'], 'amount'],
            'nominal negatif' => [['amount' => '-5'], 'amount'],
            'nominal bukan angka' => [['amount' => 'abc'], 'amount'],
            'nominal lebih dari 2 desimal' => [['amount' => '1.234'], 'amount'],
            'jenis tidak valid' => [['type' => 'FOO'], 'type'],
            'tanggal tidak valid' => [['transaction_date' => 'kemarin'], 'transaction_date'],
        ];
    }

    public function test_transaction_number_format_resets_each_month_and_is_not_reused(): void
    {
        $first = $this->transaction('2026-10-03', 'INCOMING', '100')->assertCreated()->json('data');
        $second = $this->transaction('2026-10-04', 'OUTGOING', '50')->assertCreated()->json('data');
        $other = $this->transaction('2026-09-20', 'INCOMING', '10')->assertCreated()->json('data');

        $this->assertSame('CSH2610-00001', $first['transaction_number']);
        $this->assertSame('CSH2610-00002', $second['transaction_number']);
        $this->assertSame('CSH2609-00001', $other['transaction_number']);
        $this->assertMatchesRegularExpression('/^CSH\d{4}-\d{5}$/', $first['transaction_number']);

        // Nomor transaksi yang sudah di-soft delete tidak dipakai ulang.
        $this->ajax($this->admin)->delete('/cash/transactions/'.$second['id'])->assertOk();
        $third = $this->transaction('2026-10-05', 'INCOMING', '1')->assertCreated()->json('data');
        $this->assertSame('CSH2610-00003', $third['transaction_number']);
    }

    public function test_recorder_is_the_authenticated_user_not_client_input(): void
    {
        $response = $this->ajax($this->admin)->post('/cash/transactions', [
            'transaction_date' => '2026-10-05', 'type' => 'INCOMING', 'amount' => '100', 'user_id' => 'dari-client',
        ])->assertCreated();

        $this->assertSame($this->admin->id, $response->json('data.user.id'));
    }

    public function test_theoretical_balance_and_reconciliation_statuses(): void
    {
        // Tanpa saldo awal: data belum lengkap (bukan nol).
        $this->assertSame('incomplete', $this->summary(2026, 10)['status']);
        $this->assertNull($this->summary(2026, 10)['theoretical_closing']);

        $this->balance(2026, 10, '1000000')->assertCreated();
        $this->transaction('2026-10-03', 'INCOMING', '500000')->assertCreated();
        $this->transaction('2026-10-09', 'OUTGOING', '300000')->assertCreated();

        // Kas masuk menambah dan kas keluar mengurangi saldo teoritis; tanpa saldo bulan berikutnya: belum direkonsiliasi.
        $october = $this->summary(2026, 10);
        $this->assertSame('1200000.00', $october['theoretical_closing']);
        $this->assertSame('unreconciled', $october['status']);
        $this->assertNull($october['difference']);

        // Selisih negatif.
        $this->balance(2026, 11, '1150000')->assertCreated();
        $october = $this->summary(2026, 10);
        $this->assertSame('difference', $october['status']);
        $this->assertSame('-50000.00', $october['difference']);

        // Selisih positif.
        $this->ajax($this->admin)->patch('/cash/reconciliation', ['year' => 2026, 'month' => 11, 'opening_balance' => '1230000.25'])->assertOk();
        $this->assertSame('30000.25', $this->summary(2026, 10)['difference']);

        // Selisih nol: sesuai.
        $this->ajax($this->admin)->patch('/cash/reconciliation', ['year' => 2026, 'month' => 11, 'opening_balance' => '1200000'])->assertOk();
        $this->assertSame('match', $this->summary(2026, 10)['status']);
        $this->assertSame('0.00', $this->summary(2026, 10)['difference']);
    }

    public function test_soft_deleted_transactions_are_not_counted(): void
    {
        $this->balance(2026, 10, '1000')->assertCreated();
        $kept = $this->transaction('2026-10-03', 'INCOMING', '500')->assertCreated()->json('data');
        $removed = $this->transaction('2026-10-04', 'INCOMING', '9999')->assertCreated()->json('data');

        $this->ajax($this->admin)->delete('/cash/transactions/'.$removed['id'])->assertOk();

        $this->assertSame('500.00', $this->summary(2026, 10)['incoming']);
        $this->assertSame('1500.00', $this->summary(2026, 10)['theoretical_closing']);
        $this->assertNotNull(CashTransaction::withTrashed()->find($removed['id'])->deleted_at);
        $this->assertNull(CashTransaction::query()->find($removed['id']));
        $this->assertNotNull(CashTransaction::query()->find($kept['id']));
    }

    public function test_transaction_filters_and_report_totals_are_not_doubled(): void
    {
        $this->transaction('2026-10-03', 'INCOMING', '500000')->assertCreated();
        $this->transaction('2026-10-09', 'OUTGOING', '300000')->assertCreated();
        $this->transaction('2026-11-02', 'INCOMING', '777')->assertCreated();

        $october = ['start_date' => '2026-10-01', 'end_date' => '2026-10-31'];

        $totals = $this->ajax($this->admin)->get('/reports/cash/totals?'.http_build_query($october))->json('data');
        $this->assertSame(['incoming' => '500000.00', 'outgoing' => '300000.00', 'count' => 2], $totals);

        $onlyOutgoing = $this->ajax($this->admin)->get('/reports/cash/totals?'.http_build_query($october + ['type' => 'OUTGOING']))->json('data');
        $this->assertSame('0.00', $onlyOutgoing['incoming']);
        $this->assertSame(1, $onlyOutgoing['count']);

        $table = $this->ajax($this->admin)->get('/cash/transactions?'.http_build_query($october + ['draw' => 1, 'start' => 0, 'length' => 10]))->json();
        $this->assertCount(2, $table['data']);
    }

    public function test_invalid_date_range_is_rejected(): void
    {
        $this->ajax($this->admin)->get('/reports/cash/totals?start_date=2026-10-31&end_date=2026-10-01')->assertUnprocessable();
    }

    public function test_monthly_report_lists_every_month_including_empty_ones(): void
    {
        $rows = $this->ajax($this->admin)->get('/reports/cash/monthly?start_date=2026-09-01&end_date=2026-11-30')->json('data');

        $this->assertSame([9, 10, 11], array_column($rows, 'month'));
        $this->assertSame(['incomplete', 'incomplete', 'incomplete'], array_column($rows, 'status'));
    }

    public function test_excel_and_pdf_exports_are_valid(): void
    {
        $this->balance(2026, 10, '1000')->assertCreated();
        $this->transaction('2026-10-03', 'INCOMING', '500', '=1+1')->assertCreated();

        $query = http_build_query(['start_date' => '2026-10-01', 'end_date' => '2026-10-31']);

        $excel = $this->actingAs($this->admin)->get('/reports/cash/export/excel?'.$query);
        $excel->assertOk();
        $this->assertStringContainsString('laporan-kas-20261001-20261031.xlsx', $excel->headers->get('content-disposition'));
        $sheets = IOFactory::load($excel->baseResponse->getFile()->getPathname())->getSheetNames();
        $this->assertSame(['Transaksi Kas', 'Rekap Mutasi Bulanan', 'Rekonsiliasi Bulanan'], $sheets);

        foreach (['transactions', 'monthly', 'reconciliation'] as $report) {
            $pdf = $this->actingAs($this->admin)->get('/reports/cash/export/pdf?'.$query.'&report='.$report);
            $pdf->assertOk();
            $this->assertStringStartsWith('%PDF', $pdf->getContent());
        }
    }

    public function test_failed_operation_leaves_no_partial_data(): void
    {
        $before = CashTransaction::query()->count();

        // Tanpa pengguna login, pencatatan log aktifitas gagal setelah transaksi tersimpan: seluruhnya dibatalkan.
        try {
            app(CreateCashTransaction::class)->execute(new CashTransactionDTO(
                userId: $this->admin->id,
                transactionDate: new \DateTimeImmutable('2026-10-05'),
                type: CashTransactionTypeEnum::INCOMING,
                amount: '100.00',
                notes: null,
                referenceNumber: null,
            ));
            $this->fail('Use case seharusnya gagal.');
        } catch (\Throwable) {
            // Diharapkan.
        }

        $this->assertSame($before, CashTransaction::query()->count());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_cashier_can_record_but_not_delete_or_reconcile(): void
    {
        $role = Role::query()->where('name', 'Kasir')->firstOrFail();
        $cashier = $this->admin->replicate();
        $cashier->username = 'tmp_kasir_cash_test';
        $cashier->role_id = $role->id;
        $cashier->save();

        $created = $this->ajax($cashier)->post('/cash/transactions', ['transaction_date' => '2026-10-05', 'type' => 'INCOMING', 'amount' => '100'])->assertCreated()->json('data');

        $this->ajax($cashier)->patch('/cash/transactions/'.$created['id'], ['transaction_date' => '2026-10-05', 'type' => 'INCOMING', 'amount' => '200'])->assertOk();
        $this->ajax($cashier)->delete('/cash/transactions/'.$created['id'])->assertForbidden();
        $this->ajax($cashier)->get('/cash/reconciliation/data?year=2026&month=10')->assertForbidden();
        $this->ajax($cashier)->post('/cash/reconciliation', ['year' => 2026, 'month' => 10, 'opening_balance' => '1'])->assertForbidden();
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_user_without_module_access_is_forbidden(): void
    {
        $role = Role::query()->create(['name' => 'TanpaAkses'.random_int(1000, 9999)]);
        $user = $this->admin->replicate();
        $user->username = 'tmp_tanpa_akses';
        $user->role_id = $role->id;
        $user->save();

        foreach (['/cash/dashboard', '/cash/transactions', '/cash/reconciliation', '/reports/cash'] as $url) {
            $this->actingAs($user)->get($url)->assertForbidden();
        }

        $this->assertGreaterThan(0, Module::query()->where('route', 'cash.transactions')->count());
    }

    private function ajax(User $user): static
    {
        return $this->actingAs($user)->withHeaders(['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json']);
    }

    private function transaction(string $date, string $type, string $amount, ?string $notes = null): TestResponse
    {
        return $this->ajax($this->admin)->post('/cash/transactions', [
            'transaction_date' => $date,
            'type' => $type,
            'amount' => $amount,
            'notes' => $notes,
        ]);
    }

    private function balance(int $year, int $month, string $amount): TestResponse
    {
        return $this->ajax($this->admin)->post('/cash/reconciliation', [
            'year' => $year,
            'month' => $month,
            'opening_balance' => $amount,
        ]);
    }

    private function summary(int $year, int $month): array
    {
        return $this->ajax($this->admin)->get("/cash/reconciliation/data?year={$year}&month={$month}")->json('data');
    }
}
