<?php

declare(strict_types=1);

namespace App\Exports\Cash;

use App\Domain\Cash\Entities\CashMonthSummaryEntity;
use App\Exports\Concerns\EscapesFormula;
use Carbon\Carbon;
use Generator;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Override;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CashReconciliationExport implements FromGenerator, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithEvents, WithHeadings, WithTitle
{
    use EscapesFormula;

    /**
     * Baris awal header tabel (baris di atasnya berisi judul dan tahun).
     */
    private const HEADING_ROW = 4;

    /**
     * Create a new class instance.
     *
     * @param CashMonthSummaryEntity[] $summaries
     */
    public function __construct(
        private array $summaries,
        private int $year,
        private Carbon $generatedAt,
    ) {}

    /**
     * Judul sheet.
     */
    #[Override]
    public function title(): string
    {
        return 'Histori Rekonsiliasi';
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
        return ['Periode', 'Saldo Awal', 'Kas Masuk', 'Kas Keluar', 'Saldo Teoritis Akhir', 'Saldo Aktual Bulan Berikutnya', 'Selisih', 'Status', 'Catatan'];
    }

    /**
     * Data baris per bulan.
     */
    #[Override]
    public function generator(): Generator
    {
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        foreach ($this->summaries as $summary) {
            // Nilai yang belum tersedia dikosongkan (bukan nol).
            yield [
                $months[$summary->month - 1].' '.$summary->year,
                $summary->openingBalance ?? '',
                $summary->incoming,
                $summary->outgoing,
                $summary->theoreticalClosing ?? '',
                $summary->nextOpeningBalance ?? '',
                $summary->difference ?? '',
                $summary->status->label(),
                $this->safeText($summary->notes ?? ''),
            ];
        }
    }

    /**
     * Format kolom angka.
     */
    #[Override]
    public function columnFormats(): array
    {
        $number = NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;

        return ['B' => $number, 'C' => $number, 'D' => $number, 'E' => $number, 'F' => $number, 'G' => $number];
    }

    /**
     * Menulis judul dan tahun di atas tabel.
     */
    #[Override]
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();

                $lines = [
                    1 => 'Histori Rekonsiliasi Kas',
                    2 => 'Tahun: '.$this->year.' | Dibuat pada: '.$this->generatedAt->format('d-m-Y H:i'),
                ];

                foreach ($lines as $row => $text) {
                    $sheet->setCellValueExplicit('A'.$row, $text, DataType::TYPE_STRING);
                }

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A'.self::HEADING_ROW.':I'.self::HEADING_ROW)->getFont()->setBold(true);
            },
        ];
    }
}
