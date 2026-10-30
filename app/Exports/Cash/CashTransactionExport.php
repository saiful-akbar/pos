<?php

declare(strict_types=1);

namespace App\Exports\Cash;

use App\Application\Cash\DTOs\CashReportFilterDTO;
use App\Exports\Concerns\EscapesFormula;
use Carbon\Carbon;
use Generator;
use Illuminate\Database\Query\Builder;
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

class CashTransactionExport implements FromGenerator, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithEvents, WithHeadings, WithTitle
{
    use EscapesFormula;

    /**
     * Baris awal header tabel (baris di atasnya berisi judul, periode, dan ringkasan).
     */
    private const HEADING_ROW = 5;

    /**
     * Create a new class instance.
     *
     * @param array{incoming: string, outgoing: string, count: int} $totals
     */
    public function __construct(
        private Builder $query,
        private array $totals,
        private CashReportFilterDTO $filter,
        private Carbon $generatedAt,
    ) {}

    /**
     * Judul sheet.
     */
    #[Override]
    public function title(): string
    {
        return 'Transaksi Kas';
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
        return ['No', 'Tanggal', 'No. Transaksi', 'Jenis', 'Nominal', 'Catatan', 'Nomor Referensi', 'Pencatat'];
    }

    /**
     * Data baris; transaksi dibaca dengan cursor agar tidak dimuat sekaligus ke memori.
     */
    #[Override]
    public function generator(): Generator
    {
        $number = 0;

        foreach ($this->query->cursor() as $row) {
            yield [
                ++$number,
                Carbon::parse($row->transaction_date)->format('d-m-Y'),
                $this->safeText($row->transaction_number),
                $row->type === 'INCOMING' ? 'Kas Masuk' : 'Kas Keluar',
                bcadd((string) $row->amount, '0', 2),
                $this->safeText($row->notes ?? ''),
                $this->safeText($row->reference_number ?? ''),
                $this->safeText($row->user_name ?? '-'),
            ];
        }
    }

    /**
     * Format kolom angka.
     */
    #[Override]
    public function columnFormats(): array
    {
        return ['E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1];
    }

    /**
     * Menulis judul, periode, dan ringkasan di atas tabel.
     */
    #[Override]
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();

                $lines = [
                    1 => 'Daftar Transaksi Kas',
                    2 => 'Periode: '.$this->filter->startDate->format('d-m-Y').' s/d '.$this->filter->endDate->format('d-m-Y')
                        .' | Jenis: '.($this->filter->type?->label() ?? 'Semua'),
                    3 => 'Total Kas Masuk: '.number_format((float) $this->totals['incoming'], 2)
                        .' | Total Kas Keluar: '.number_format((float) $this->totals['outgoing'], 2)
                        .' | Dibuat pada: '.$this->generatedAt->format('d-m-Y H:i'),
                ];

                foreach ($lines as $row => $text) {
                    $sheet->setCellValueExplicit('A'.$row, $text, DataType::TYPE_STRING);
                }

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A'.self::HEADING_ROW.':H'.self::HEADING_ROW)->getFont()->setBold(true);
            },
        ];
    }
}
