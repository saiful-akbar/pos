<?php

declare(strict_types=1);

namespace App\Exports\Reports;

use App\Application\Report\DTOs\SaleReportFilterDTO;
use App\Domain\Report\Entities\SaleReportSummaryEntity;
use App\Exports\Concerns\EscapesFormula;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Override;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SaleReportExport implements FromQuery, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithEvents, WithHeadings, WithMapping
{
    use EscapesFormula;

    /**
     * Baris awal header tabel (baris di atasnya berisi judul, periode, filter, dan ringkasan).
     */
    private const HEADING_ROW = 8;

    /**
     * Label metode pembayaran.
     */
    private const METHOD_LABELS = [
        'cash' => 'Tunai',
        'qris' => 'QRIS',
        'debit' => 'Debit',
        'credit' => 'Kredit',
    ];

    /**
     * Nomor urut baris.
     */
    private int $number = 0;

    /**
     * Create a new class instance.
     *
     * @param array<string, string> $filterLabels Keterangan filter (label => nilai) untuk ditulis di atas tabel.
     */
    public function __construct(
        private Builder $query,
        private SaleReportFilterDTO $filter,
        private SaleReportSummaryEntity $summary,
        private array $filterLabels = [],
    ) {}

    /**
     * Query laporan (dibaca per potongan oleh library, tidak dimuat sekaligus).
     */
    #[Override]
    public function query(): Builder
    {
        return $this->query;
    }

    /**
     * Sel awal tabel.
     */
    #[Override]
    public function startCell(): string
    {
        return 'A'.self::HEADING_ROW;
    }

    /**
     * Baris header.
     */
    #[Override]
    public function headings(): array
    {
        return [
            'No',
            'Tanggal Transaksi',
            'Nomor Invoice',
            'Pelanggan',
            'Jenis Produk',
            'Qty (Satuan Dasar)',
            'Total Harga',
            'Diskon',
            'Grand Total',
            'Dibayar',
            'Kembalian',
            'Metode Pembayaran',
            'Kasir',
        ];
    }

    /**
     * Memetakan satu baris laporan ke baris excel.
     */
    #[Override]
    public function map($row): array
    {
        return [
            ++$this->number,
            Date::PHPToExcel(Carbon::parse($row->transaction_date)),
            $this->safeText($row->invoice_number),
            $this->safeText($row->customer_name ?? 'Umum'),
            $this->decimal($row->total_product),
            $this->decimal($row->base_quantity),
            $this->decimal($row->items_subtotal),
            $this->decimal($row->discount_amount),
            $this->decimal($row->grand_total),
            $this->decimal($row->paid_amount),
            $this->decimal($row->change_amount),
            self::METHOD_LABELS[$row->payment_method] ?? $row->payment_method,
            $this->safeText($row->cashier_name ?? '-'),
        ];
    }

    /**
     * Format kolom tanggal dan angka.
     */
    #[Override]
    public function columnFormats(): array
    {
        $number = NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;

        return [
            'B' => 'dd-mm-yyyy',
            'E' => $number,
            'F' => $number,
            'G' => $number,
            'H' => $number,
            'I' => $number,
            'J' => $number,
            'K' => $number,
        ];
    }

    /**
     * Menulis judul, periode, filter, dan ringkasan di atas tabel.
     */
    #[Override]
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();

                $filters = collect($this->filterLabels)
                    ->map(fn (string $value, string $label): string => $label.': '.$value)
                    ->implode(' | ');

                $lines = [
                    1 => 'Laporan Penjualan',
                    2 => 'Periode: '.$this->filter->startDate->format('d-m-Y').' s/d '.$this->filter->endDate->format('d-m-Y'),
                    3 => $filters !== '' ? 'Filter: '.$filters : 'Filter: Semua',
                    4 => 'Total Transaksi: '.number_format($this->summary->totalTransactions)
                        .' | Total Penjualan: '.number_format($this->summary->totalSales, 2),
                    5 => 'Total Produk Terjual (satuan dasar): '.number_format($this->summary->totalProductsSold, 2)
                        .' | Total Diskon: '.number_format($this->summary->totalDiscount, 2),
                ];

                // Tulis sebagai string eksplisit agar tidak diinterpretasikan sebagai formula.
                foreach ($lines as $row => $text) {
                    $sheet->setCellValueExplicit('A'.$row, $text, DataType::TYPE_STRING);
                }

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A'.self::HEADING_ROW.':M'.self::HEADING_ROW)->getFont()->setBold(true);
            },
        ];
    }

    /**
     * Format nilai desimal 2 digit (string numerik) agar nilai 0 tetap tertulis pada sel excel.
     */
    private function decimal(mixed $value): string
    {
        return number_format((float) $value, 2, '.', '');
    }
}
