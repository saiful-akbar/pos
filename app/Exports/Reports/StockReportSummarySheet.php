<?php

declare(strict_types=1);

namespace App\Exports\Reports;

use App\Application\Report\DTOs\StockReportFilterDTO;
use App\Domain\Report\Entities\StockReportSummaryEntity;
use App\Exports\Concerns\EscapesFormula;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Override;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class StockReportSummarySheet implements FromQuery, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithEvents, WithHeadings, WithMapping, WithTitle
{
    use EscapesFormula;

    /**
     * Label status rekonsiliasi.
     */
    public const STATUS_LABELS = [
        'match' => 'Sesuai',
        'negative' => 'Selisih Kurang',
        'positive' => 'Selisih Lebih',
        'unavailable' => 'Belum Dapat Direkonsiliasi',
    ];

    /**
     * Baris awal header tabel (baris di atasnya berisi judul, periode, filter, dan ringkasan).
     */
    private const HEADING_ROW = 8;

    /**
     * Nomor urut baris.
     */
    private int $number = 0;

    /**
     * Create a new class instance.
     *
     * @param array<string, string> $filterLabels
     */
    public function __construct(
        private Builder $query,
        private StockReportFilterDTO $filter,
        private StockReportSummaryEntity $summary,
        private Carbon $generatedAt,
        private array $filterLabels,
    ) {}

    /**
     * Judul sheet.
     */
    #[Override]
    public function title(): string
    {
        return 'Ringkasan Rekonsiliasi';
    }

    /**
     * Query laporan (dibaca per potongan oleh library).
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
            'No', 'Kode Produk', 'Nama Produk', 'Kategori', 'Satuan Dasar', 'Stok Awal', 'Pembelian',
            'Pengembalian Penjualan', 'Penjualan', 'Book Balance', 'Stok Akhir Aktual', 'Selisih', 'Status',
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
            $this->safeText($row->code),
            $this->safeText($row->name),
            $this->safeText($row->category_name ?? '-'),
            $this->safeText($row->base_unit ?? '-'),
            $this->decimal($row->opening),
            $this->decimal($row->purchase_qty),
            $this->decimal($row->return_qty),
            $this->decimal($row->sale_qty),
            $this->decimal($row->book_balance),
            // Stok akhir dan selisih kosong jika snapshot bulan berikutnya belum tersedia (bukan nol).
            $row->closing === null ? '' : $this->decimal($row->closing),
            $row->difference === null ? '' : $this->decimal($row->difference),
            self::STATUS_LABELS[$row->status] ?? $row->status,
        ];
    }

    /**
     * Format kolom angka.
     */
    #[Override]
    public function columnFormats(): array
    {
        $number = NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;

        return ['F' => $number, 'G' => $number, 'H' => $number, 'I' => $number, 'J' => $number, 'K' => $number, 'L' => $number];
    }

    /**
     * Menulis judul, periode, snapshot, filter, dan ringkasan di atas tabel.
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
                    1 => 'Laporan Stok / Inventory',
                    2 => sprintf('Periode: %02d-%04d | Stok akhir aktual: snapshot stok awal %02d-%04d | Dibuat pada: %s',
                        $this->filter->month, $this->filter->year, $this->filter->nextMonth(), $this->filter->nextYear(),
                        $this->generatedAt->format('d-m-Y H:i')),
                    3 => 'Snapshot stok awal: '.($this->summary->openingAvailable ? 'tersedia' : 'belum tersedia')
                        .' | Snapshot stok akhir: '.($this->summary->closingAvailable ? 'tersedia' : 'belum tersedia'),
                    4 => $filters !== '' ? 'Filter: '.$filters : 'Filter: Semua',
                    5 => 'Produk: '.$this->summary->totalProducts.' | Sesuai: '.$this->summary->matched
                        .' | Selisih kurang: '.$this->summary->negative.' | Selisih lebih: '.$this->summary->positive
                        .' | Belum direkonsiliasi: '.$this->summary->unavailable,
                    6 => 'Semua kuantitas dalam satuan dasar (quantity x kelipatan). Book Balance = Stok Awal + Pembelian + Pengembalian - Penjualan; Selisih = Stok Akhir Aktual - Book Balance.',
                ];

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
