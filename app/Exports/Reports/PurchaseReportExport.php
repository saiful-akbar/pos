<?php

declare(strict_types=1);

namespace App\Exports\Reports;

use App\Application\Report\DTOs\PurchaseReportFilterDTO;
use App\Domain\Report\Entities\PurchaseReportSummaryEntity;
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

class PurchaseReportExport implements FromQuery, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithEvents, WithHeadings, WithMapping
{
    use EscapesFormula;

    /**
     * Baris awal header tabel (baris di atasnya berisi judul, periode, filter, dan ringkasan).
     */
    private const HEADING_ROW = 7;

    /**
     * Label status pembayaran.
     */
    private const STATUS_LABELS = [
        'unpaid' => 'Belum Dibayar',
        'partial' => 'Dibayar Sebagian',
        'paid' => 'Lunas',
    ];

    /**
     * Nomor urut baris.
     */
    private int $number = 0;

    /**
     * Create a new class instance.
     */
    public function __construct(
        private Builder $query,
        private PurchaseReportFilterDTO $filter,
        private PurchaseReportSummaryEntity $summary,
        private ?string $supplierName = null,
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
            'Nomor Faktur',
            'Pemasok',
            'Total Produk',
            'Total Kuantitas',
            'Total Pembelian',
            'Sudah Dibayar',
            'Sisa Utang',
            'Status Pembayaran',
            'Petugas',
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
            $this->safeText($row->invoice_number ?? '-'),
            $this->safeText($row->supplier_name ?? '-'),
            // Angka dikirim sebagai string desimal dari database (bernilai 0 tetap tertulis, tidak menjadi sel kosong).
            $this->decimal($row->total_product),
            $this->decimal($row->total_quantity),
            $this->decimal($row->total_price),
            $this->decimal($row->already_paid),
            $this->decimal($row->outstanding),
            self::STATUS_LABELS[$row->payment_status] ?? $row->payment_status,
            $this->safeText($row->user_name ?? '-'),
        ];
    }

    /**
     * Format kolom tanggal dan angka.
     */
    #[Override]
    public function columnFormats(): array
    {
        return [
            'B' => 'dd-mm-yyyy',
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
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

                $status = $this->filter->paymentStatus === null
                    ? 'Semua'
                    : (self::STATUS_LABELS[$this->filter->paymentStatus->value] ?? $this->filter->paymentStatus->value);

                $lines = [
                    1 => 'Laporan Pembelian',
                    2 => 'Periode: '.$this->filter->startDate->format('d-m-Y').' s/d '.$this->filter->endDate->format('d-m-Y'),
                    3 => 'Pemasok: '.($this->supplierName ?? 'Semua')
                        .' | Status: '.$status
                        .' | Nomor Faktur: '.($this->filter->invoiceNumber ?? '-'),
                    4 => 'Total Transaksi: '.number_format($this->summary->totalTransactions)
                        .' | Total Pembelian: '.number_format($this->summary->totalPurchase, 2),
                    5 => 'Total Sudah Dibayar: '.number_format($this->summary->totalPaid, 2)
                        .' | Total Sisa Utang: '.number_format($this->summary->totalOutstanding, 2),
                ];

                // Tulis sebagai string eksplisit agar tidak diinterpretasikan sebagai formula.
                foreach ($lines as $row => $text) {
                    $sheet->setCellValueExplicit('A'.$row, $text, DataType::TYPE_STRING);
                }

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A'.self::HEADING_ROW.':K'.self::HEADING_ROW)->getFont()->setBold(true);
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
