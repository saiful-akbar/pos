<?php

declare(strict_types=1);

namespace App\Exports\Reports;

use App\Application\Report\DTOs\SaleReturnReportFilterDTO;
use App\Domain\Report\Entities\SaleReturnReportSummaryEntity;
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

class SaleReturnReportExport implements FromQuery, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithEvents, WithHeadings, WithMapping
{
    use EscapesFormula;

    /**
     * Baris awal header tabel (baris di atasnya berisi judul, periode, filter, dan ringkasan).
     */
    private const HEADING_ROW = 9;

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
        private SaleReturnReportFilterDTO $filter,
        private SaleReturnReportSummaryEntity $summary,
        private Carbon $generatedAt,
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
            'Tanggal Pengembalian',
            'Nomor Pengembalian',
            'Invoice Penjualan',
            'Pelanggan',
            'Jenis Produk',
            'Qty (Satuan Dasar)',
            'Total Harga',
            'Diskon',
            'Nilai Pengembalian',
            'Petugas',
            'Catatan',
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
            $this->safeText($row->return_number),
            $this->safeText($row->invoice_number ?? '-'),
            $this->safeText($row->customer_name ?? 'Umum'),
            $this->decimal($row->total_product),
            $this->decimal($row->base_quantity),
            $this->decimal($row->items_subtotal),
            $this->decimal($row->discount_amount),
            $this->decimal($row->grand_total),
            $this->safeText($row->user_name ?? '-'),
            $this->safeText($row->notes ?? ''),
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
            'F' => $number,
            'G' => $number,
            'H' => $number,
            'I' => $number,
            'J' => $number,
        ];
    }

    /**
     * Menulis judul, periode, waktu pembuatan, filter, dan ringkasan di atas tabel.
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
                    1 => 'Laporan Pengembalian Penjualan',
                    2 => 'Periode: '.$this->filter->startDate->format('d-m-Y').' s/d '.$this->filter->endDate->format('d-m-Y'),
                    3 => 'Dibuat pada: '.$this->generatedAt->format('d-m-Y H:i'),
                    4 => $filters !== '' ? 'Filter: '.$filters : 'Filter: Semua',
                    5 => 'Total Pengembalian: '.number_format($this->summary->totalReturns)
                        .' | Total Nilai Pengembalian: '.number_format($this->summary->totalValue, 2),
                    6 => 'Total Qty Dikembalikan (satuan dasar): '.number_format($this->summary->totalQuantity, 2)
                        .' | Jenis Produk: '.number_format($this->summary->totalProducts)
                        .' | Total Diskon: '.number_format($this->summary->totalDiscount, 2),
                ];

                // Tulis sebagai string eksplisit agar tidak diinterpretasikan sebagai formula.
                foreach ($lines as $row => $text) {
                    $sheet->setCellValueExplicit('A'.$row, $text, DataType::TYPE_STRING);
                }

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A'.self::HEADING_ROW.':L'.self::HEADING_ROW)->getFont()->setBold(true);
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
