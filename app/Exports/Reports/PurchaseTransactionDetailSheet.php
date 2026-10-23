<?php

declare(strict_types=1);

namespace App\Exports\Reports;

use App\Domain\Purchase\Entities\PurchaseDetailEntity;
use App\Domain\Report\Entities\PurchaseTransactionReportEntity;
use App\Exports\Concerns\EscapesFormula;
use Maatwebsite\Excel\Concerns\FromArray;
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

class PurchaseTransactionDetailSheet implements FromArray, ShouldAutoSize, WithColumnFormatting, WithCustomStartCell, WithEvents, WithHeadings, WithTitle
{
    use EscapesFormula;

    /**
     * Baris awal header tabel (baris di atasnya berisi informasi transaksi).
     */
    private const HEADING_ROW = 9;

    /**
     * Label status pembayaran.
     */
    private const STATUS_LABELS = [
        'unpaid' => 'Belum Dibayar',
        'partial' => 'Dibayar Sebagian',
        'paid' => 'Lunas',
    ];

    /**
     * Create a new class instance.
     */
    public function __construct(private PurchaseTransactionReportEntity $transaction) {}

    /**
     * Judul sheet.
     */
    #[Override]
    public function title(): string
    {
        return 'Detail Produk';
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
        return ['No', 'Kode Produk', 'Nama Produk', 'Kode Unit', 'Nama Unit', 'Qty', 'Kelipatan Qty', 'Harga', 'Subtotal'];
    }

    /**
     * Data detail produk.
     */
    #[Override]
    public function array(): array
    {
        $rows = [];

        foreach ($this->transaction->details as $index => $detail) {
            /** @var PurchaseDetailEntity $detail */
            $rows[] = [
                $index + 1,
                $this->safeText($detail->product->code),
                $this->safeText($detail->product->name),
                $this->safeText($detail->unit->code),
                $this->safeText($detail->unit->name),
                $this->decimal($detail->quantity),
                $this->decimal($detail->multiplesQuantity),
                $this->decimal($detail->price),
                $this->decimal($detail->subtotal),
            ];
        }

        return $rows;
    }

    /**
     * Format kolom angka.
     */
    #[Override]
    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    /**
     * Menulis informasi transaksi di atas tabel.
     */
    #[Override]
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $purchase = $this->transaction->purchase;

                $lines = [
                    1 => 'Detail Transaksi Pembelian',
                    2 => 'Tanggal: '.$purchase->transactionDate->format('d-m-Y').' | Nomor Faktur: '.($purchase->invoiceNumber ?? '-'),
                    3 => 'Pemasok: '.$purchase->supplier->name.' | Petugas: '.$purchase->user->fullName,
                    4 => 'Status Pembayaran: '.(self::STATUS_LABELS[$purchase->paymentStatus->value] ?? $purchase->paymentStatus->value),
                    5 => 'Total Pembelian: '.number_format($purchase->totalPrice, 2),
                    6 => 'Sudah Dibayar: '.number_format($purchase->alreadyPaid, 2).' | Sisa Utang: '.number_format($this->transaction->outstanding(), 2),
                    7 => 'Catatan: '.($purchase->notes ?? '-'),
                ];

                // Tulis sebagai string eksplisit agar tidak diinterpretasikan sebagai formula.
                foreach ($lines as $row => $text) {
                    $sheet->setCellValueExplicit('A'.$row, $text, DataType::TYPE_STRING);
                }

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A'.self::HEADING_ROW.':I'.self::HEADING_ROW)->getFont()->setBold(true);
            },
        ];
    }

    /**
     * Format nilai desimal 2 digit (string numerik) agar nilai 0 tetap tertulis pada sel excel.
     */
    private function decimal(float $value): string
    {
        return number_format($value, 2, '.', '');
    }
}
