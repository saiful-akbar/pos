<?php

declare(strict_types=1);

namespace App\Exports\Reports;

use App\Exports\Concerns\EscapesFormula;
use Carbon\Carbon;
use DateTimeInterface;
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

/**
 * Dasar export laporan per produk (satu baris per detail produk); kolom ditentukan oleh turunannya.
 */
abstract class ProductReportExport implements FromQuery, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithEvents, WithHeadings, WithMapping
{
    use EscapesFormula;

    /**
     * Baris awal header tabel (baris di atasnya berisi judul, periode, dan filter).
     */
    private const HEADING_ROW = 5;

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
        private DateTimeInterface $startDate,
        private DateTimeInterface $endDate,
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
     * Baris header: No, Tanggal, lalu kolom khusus laporan.
     */
    #[Override]
    public function headings(): array
    {
        return ['No', 'Tanggal', ...$this->columnTitles()];
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
            ...$this->mapRow($row),
        ];
    }

    /**
     * Format kolom tanggal dan angka.
     */
    #[Override]
    public function columnFormats(): array
    {
        return ['B' => 'dd-mm-yyyy', ...$this->numberFormats()];
    }

    /**
     * Menulis judul, periode, dan filter di atas tabel.
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
                    1 => $this->title(),
                    2 => 'Periode: '.$this->startDate->format('d-m-Y').' s/d '.$this->endDate->format('d-m-Y'),
                    3 => $filters !== '' ? 'Filter: '.$filters : 'Filter: Semua',
                ];

                // Tulis sebagai string eksplisit agar tidak diinterpretasikan sebagai formula.
                foreach ($lines as $row => $text) {
                    $sheet->setCellValueExplicit('A'.$row, $text, DataType::TYPE_STRING);
                }

                $lastColumn = chr(ord('A') + count($this->headings()) - 1);
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A'.self::HEADING_ROW.':'.$lastColumn.self::HEADING_ROW)->getFont()->setBold(true);
            },
        ];
    }

    /**
     * Judul laporan.
     */
    abstract protected function title(): string;

    /**
     * Daftar judul kolom (tanpa kolom nomor urut).
     *
     * @return string[]
     */
    abstract protected function columnTitles(): array;

    /**
     * Memetakan satu baris data ke nilai kolom (tanpa nomor urut dan tanggal).
     *
     * @return array<int, mixed>
     */
    abstract protected function mapRow(object $row): array;

    /**
     * Format kolom angka (huruf kolom => format).
     *
     * @return array<string, string>
     */
    abstract protected function numberFormats(): array;

    /**
     * Format nilai desimal 2 digit (string numerik) agar nilai 0 tetap tertulis pada sel excel.
     */
    protected function decimal(mixed $value): string
    {
        return number_format((float) $value, 2, '.', '');
    }
}
