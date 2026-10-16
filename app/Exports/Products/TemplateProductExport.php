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
     * Header.
     */
    #[Override]
    public function headings(): array
    {
        return [
            [
                'Kolom dengan tanda * wajib diisi',
            ],
            [
                'Kode Produk *',
                'Nama (Deskripsi) *',
                'Kode Kategori *',
                'Diskon',
                'Minimum Stok',
                'Stok Tersedia',
                'Status (1 = Aktif, 0 = Tidak Aktif)',
            ],
        ];
    }
}
