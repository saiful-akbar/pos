<?php

declare(strict_types=1);

namespace App\Exports\Reports;

use Override;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SaleProductReportExport extends ProductReportExport
{
    /**
     * Judul laporan.
     */
    #[Override]
    protected function title(): string
    {
        return 'Laporan Penjualan per Produk';
    }

    /**
     * Judul kolom setelah No dan Tanggal.
     */
    #[Override]
    protected function columnTitles(): array
    {
        return ['Nomor Invoice', 'Pelanggan', 'Kode Produk', 'Nama Produk', 'Unit (Satuan)', 'Harga Jual', 'Qty', 'Kelipatan Qty', 'Total Qty (Dasar)', 'Subtotal', 'Diskon (%)', 'Grand Total'];
    }

    /**
     * Memetakan satu baris data.
     */
    #[Override]
    protected function mapRow(object $row): array
    {
        return [
            $this->safeText($row->invoice_number ?? '-'),
            $this->safeText($row->customer_name ?? 'Umum'),
            $this->safeText($row->product_code ?? '-'),
            $this->safeText($row->product_name ?? '-'),
            $this->safeText(trim(($row->unit_name ?? '-').' ('.($row->unit_code ?? '-').')')),
            $this->decimal($row->selling_price),
            $this->decimal($row->quantity),
            $this->decimal($row->multiples_quantity),
            $this->decimal($row->base_quantity),
            $this->decimal($row->subtotal),
            $this->decimal($row->discount),
            $this->decimal($row->grand_total),
        ];
    }

    /**
     * Format kolom angka (Harga Jual sampai Grand Total: kolom H sampai N).
     */
    #[Override]
    protected function numberFormats(): array
    {
        $number = NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;

        return array_fill_keys(['H', 'I', 'J', 'K', 'L', 'M', 'N'], $number);
    }
}
