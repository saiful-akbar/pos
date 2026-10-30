<?php

declare(strict_types=1);

namespace App\Exports\Reports;

use Override;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PurchaseProductReportExport extends ProductReportExport
{
    /**
     * Judul laporan.
     */
    #[Override]
    protected function title(): string
    {
        return 'Laporan Pembelian per Produk';
    }

    /**
     * Judul kolom setelah No dan Tanggal.
     */
    #[Override]
    protected function columnTitles(): array
    {
        return ['Nomor Faktur', 'Pemasok', 'Kode Produk', 'Nama Produk', 'Unit (Satuan)', 'Qty', 'Kelipatan Qty', 'Total Qty (Dasar)', 'Harga', 'Subtotal'];
    }

    /**
     * Memetakan satu baris data.
     */
    #[Override]
    protected function mapRow(object $row): array
    {
        return [
            $this->safeText($row->invoice_number ?? '-'),
            $this->safeText($row->supplier_name ?? '-'),
            $this->safeText($row->product_code ?? '-'),
            $this->safeText($row->product_name ?? '-'),
            $this->safeText(trim(($row->unit_name ?? '-').' ('.($row->unit_code ?? '-').')')),
            $this->decimal($row->quantity),
            $this->decimal($row->multiples_quantity),
            $this->decimal($row->base_quantity),
            $this->decimal($row->price),
            $this->decimal($row->subtotal),
        ];
    }

    /**
     * Format kolom angka (Qty sampai Subtotal: kolom H sampai L).
     */
    #[Override]
    protected function numberFormats(): array
    {
        $number = NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;

        return ['H' => $number, 'I' => $number, 'J' => $number, 'K' => $number, 'L' => $number];
    }
}
