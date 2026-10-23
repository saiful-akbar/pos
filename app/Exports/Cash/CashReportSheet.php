<?php

declare(strict_types=1);

namespace App\Exports\Cash;

use App\Application\Cash\DTOs\CashReportFilterDTO;
use App\Domain\Cash\Entities\CashMonthSummaryEntity;
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

class CashReportSheet implements FromGenerator, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithEvents, WithHeadings, WithTitle
{
    use EscapesFormula;

    /**
     * Baris awal header tabel (baris di atasnya berisi judul, periode, dan ringkasan).
     */
    private const HEADING_ROW = 5;

    /**
     * Create a new class instance.
     *
     * @param 'transactions'|'monthly'|'reconciliation'             $type
     * @param CashMonthSummaryEntity[]                              $summaries
     * @param array{incoming: string, outgoing: string, count: int} $totals
     */
    public function __construct(
        private string $type,
        private ?Builder $transactionsQuery,
        private array $summaries,
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
        return match ($this->type) {
            'transactions' => 'Transaksi Kas',
            'monthly' => 'Rekap Mutasi Bulanan',
            default => 'Rekonsiliasi Bulanan',
        };
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
        return match ($this->type) {
            'transactions' => ['No', 'Tanggal', 'No. Transaksi', 'Jenis', 'Nominal', 'Catatan', 'Nomor Referensi', 'Pencatat'],
            'monthly' => ['Periode', 'Saldo Awal', 'Kas Masuk', 'Kas Keluar', 'Saldo Teoritis Akhir'],
            default => ['Periode', 'Saldo Awal', 'Kas Masuk', 'Kas Keluar', 'Saldo Teoritis Akhir', 'Saldo Aktual Bulan Berikutnya', 'Selisih', 'Status', 'Catatan'],
        };
    }

    /**
     * Data baris; transaksi dibaca dengan cursor agar tidak dimuat sekaligus ke memori.
     */
    #[Override]
    public function generator(): Generator
    {
        if ($this->type === 'transactions') {
            $number = 0;

            foreach ($this->transactionsQuery->cursor() as $row) {
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

            return;
        }

        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        foreach ($this->summaries as $summary) {
            $period = $months[$summary->month - 1].' '.$summary->year;

            // Nilai yang belum tersedia dikosongkan (bukan nol).
            $base = [$period, $summary->openingBalance ?? '', $summary->incoming, $summary->outgoing, $summary->theoreticalClosing ?? ''];

            yield $this->type === 'monthly'
                ? $base
                : [...$base, $summary->nextOpeningBalance ?? '', $summary->difference ?? '', $summary->status->label(), $this->safeText($summary->notes ?? '')];
        }
    }

    /**
     * Format kolom angka.
     */
    #[Override]
    public function columnFormats(): array
    {
        $number = NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;

        return match ($this->type) {
            'transactions' => ['E' => $number],
            'monthly' => ['B' => $number, 'C' => $number, 'D' => $number, 'E' => $number],
            default => ['B' => $number, 'C' => $number, 'D' => $number, 'E' => $number, 'F' => $number, 'G' => $number],
        };
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
                    1 => 'Laporan Kas - '.$this->title(),
                    2 => 'Periode: '.$this->filter->startDate->format('d-m-Y').' s/d '.$this->filter->endDate->format('d-m-Y')
                        .' | Jenis: '.($this->filter->type?->label() ?? 'Semua'),
                    3 => 'Total Kas Masuk: '.number_format((float) $this->totals['incoming'], 2)
                        .' | Total Kas Keluar: '.number_format((float) $this->totals['outgoing'], 2)
                        .' | Dibuat pada: '.$this->generatedAt->format('d-m-Y H:i'),
                ];

                foreach ($lines as $row => $text) {
                    $sheet->setCellValueExplicit('A'.$row, $text, DataType::TYPE_STRING);
                }

                $lastColumn = chr(ord('A') + count($this->headings()) - 1);
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A'.self::HEADING_ROW.':'.$lastColumn.self::HEADING_ROW)->getFont()->setBold(true);
            },
        ];
    }
}
