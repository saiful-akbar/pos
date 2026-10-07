<?php

declare(strict_types=1);

namespace App\Exports\Catgories;

use App\Models\Category;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Override;

class CategoryExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
     * Header row
     */
    #[Override]
    public function headings(): array
    {
        return [
            'Kode',
            'Nama (Deskripsi)',
            'Status',
            'Waktu Dibuat',
            'Waktu Diedit',
        ];
    }

    /**
     * Export from collection.
     */
    #[Override]
    public function collection(): Enumerable
    {
        $columns = [
            'code',
            'name',
            DB::raw("case when is_active then 'Aktif' else 'Tidak Aktif' end"),
            DB::raw("date_format(created_at, '%Y-%m-%d %H:%i:%s')"),
            DB::raw("date_format(updated_at, '%Y-%m-%d %H:%i:%s')"),
        ];

        return Category::query()
            ->select($columns)
            ->orderBy('code', 'asc')
            ->get();
    }
}
