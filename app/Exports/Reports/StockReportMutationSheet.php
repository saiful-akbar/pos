<?php

declare(strict_types=1);

namespace App\Exports\Reports;

use App\Exports\Concerns\EscapesFormula;
use Carbon\Carbon;
use Generator;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Override;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockReportMutationSheet implements FromGenerator, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    use EscapesFormula;

    /**
     * Label jenis mutasi.
     */
    private const TYPE_LABELS = [
        'purchase' => 'Pembelian',
        'return' => 'Pengembalian Penjualan',
        'sale' => 'Penjualan',
    ];

    /**
     * Create a new class instance.
     */
    public function __construct(private Builder $query) {}

    /**
     * Judul sheet.
     */
    #[Override]
    public function title(): string
    {
        return 'Detail Mutasi';
    }

    /**
     * Baris header.
     */
    #[Override]
    public function headings(): array
    {
        return [
            'Kode Produk', 'Nama Produk', 'Tanggal', 'Jenis Mutasi', 'Referensi', 'Satuan Transaksi',
            'Qty Transaksi', 'Kelipatan', 'Masuk (Dasar)', 'Keluar (Dasar)', 'Saldo Berjalan', 'Petugas',
        ];
    }

    /**
     * Data mutasi dibaca dengan cursor agar tidak dimuat sekaligus ke memori.
     */
    #[Override]
    public function generator(): Generator
    {
        foreach ($this->query->cursor() as $row) {
            yield [
                $this->safeText($row->product_code),
                $this->safeText($row->product_name),
                Carbon::parse($row->date)->format('d-m-Y'),
                self::TYPE_LABELS[$row->type] ?? $row->type,
                $this->safeText($row->reference ?? '(tanpa nomor referensi)'),
                $this->safeText($row->unit_code),
                $this->decimal($row->quantity),
                $this->decimal($row->multiples_quantity),
                $this->decimal($row->in_qty),
                $this->decimal($row->out_qty),
                $this->decimal($row->balance),
                $this->safeText($row->user_name ?? '-'),
            ];
        }
    }

    /**
     * Header tebal.
     */
    #[Override]
    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }

    /**
     * Format nilai desimal 2 digit (string numerik) agar nilai 0 tetap tertulis pada sel excel.
     */
    private function decimal(mixed $value): string
    {
        return number_format((float) $value, 2, '.', '');
    }
}
