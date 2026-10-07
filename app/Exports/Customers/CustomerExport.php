<?php

declare(strict_types=1);

namespace App\Exports\Customers;

use App\Models\Customer;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Override;

class CustomerExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
     * Header row.
     */
    #[Override]
    public function headings(): array
    {
        return [
            'Nama',
            'Telepon',
            'Alamat',
            'Diskon (%)',
            'Status',
            'Waktu Dibuat',
            'Waktu Diedit',
        ];
    }

    /**
     * Export from collection
     */
    #[Override]
    public function collection(): Enumerable
    {
        $columns = [
            'name',
            'phone_number',
            'address',
            'discount',
            DB::raw("CASE WHEN is_active = 1 THEN 'Aktif' ELSE 'Tidak aktif' END AS status"),
            DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS created"),
            DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i:%s') AS updated"),
        ];

        return Customer::query()
            ->select($columns)
            ->orderBy('name', 'asc')
            ->cursor();
    }
}
