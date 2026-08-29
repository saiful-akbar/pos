<?php

namespace App\Exports\Catgories;

use App\Models\Category;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Override;

class CategoryExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * Header row
     *
     * @return array
     */
    #[Override]
    public function headings(): array
    {
        return [
            'Kode Kategori',
            'Nama Kategori',
            'Status',
            'Waktu Dibuat',
            'Waktu Diedit',
        ];
    }

    /**
     * Export from collection.
     *
     * @return Enumerable
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
