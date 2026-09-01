<?php

declare(strict_types=1);

namespace App\Exports\Products;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Override;

class TemplateProductExport implements Export, ShouldAutoSize, WithHeadings
{
    /**
     * Heading
     */
    #[Override]
    public function headings(): array
    {
        return [
            [
                'Kolom yang ditandai * wajib diisi',
            ],
            [
                'Kode Produk *',
                'Barcode *',
                'Nama (Deskripsi)',
                'Kode Kategori *',
                'Kode Unit *',
                'Harga Beli',
                'Harga Jual',
                'Diskon (%)',
                'Stok Tersedia',
                'Stok Minimum',
                'Status (1 = aktif, 0 = tidak aktif)',
            ],
        ];
    }
}
