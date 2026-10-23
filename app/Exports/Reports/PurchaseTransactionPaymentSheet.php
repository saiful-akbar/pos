<?php

declare(strict_types=1);

namespace App\Exports\Reports;

use App\Domain\Purchase\Entities\PurchasePaymentEntity;
use App\Domain\Report\Entities\PurchaseTransactionReportEntity;
use App\Exports\Concerns\EscapesFormula;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Override;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchaseTransactionPaymentSheet implements FromArray, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithStyles, WithTitle
{
    use EscapesFormula;

    /**
     * Label metode pembayaran.
     */
    private const METHOD_LABELS = [
        'cash' => 'Tunai',
        'transfer' => 'Transfer',
        'credit' => 'Kredit',
        'debit' => 'Debit',
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
        return 'Pembayaran';
    }

    /**
     * Baris header.
     */
    #[Override]
    public function headings(): array
    {
        return ['No', 'Tanggal Pembayaran', 'Metode', 'Nominal', 'Petugas', 'Catatan'];
    }

    /**
     * Data pembayaran.
     */
    #[Override]
    public function array(): array
    {
        $rows = [];

        foreach ($this->transaction->payments as $index => $payment) {
            /** @var PurchasePaymentEntity $payment */
            $rows[] = [
                $index + 1,
                $payment->paymentDate->format('d-m-Y'),
                self::METHOD_LABELS[$payment->paymentMethod->value] ?? $payment->paymentMethod->value,
                number_format($payment->amount, 2, '.', ''),
                $this->safeText($payment->user->fullName),
                $this->safeText($payment->notes),
            ];
        }

        return $rows;
    }

    /**
     * Format kolom nominal.
     */
    #[Override]
    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    /**
     * Header tebal.
     */
    #[Override]
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
