<?php

declare(strict_types=1);

namespace App\Exports\Customers;

use App\Application\Customer\UseCases\GetAllCustomers;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Override;

class CustomerExport implements FromArray, ShouldAutoSize, WithHeadings
{
    /**
     * Header row.
     */
    #[Override]
    public function headings(): array
    {
        return [
            'Nama',
            'Nomor Telepon',
            'Alamat',
            'Waktu Dibuat',
        ];
    }

    /**
     * Export from array.
     */
    #[Override]
    public function array(): array
    {
        $customers = [];

        foreach (app(GetAllCustomers::class)->execute() as $customer) {
            $customers[] = [
                'name' => $customer->name,
                'phone_number' => $customer->phoneNumber,
                'address' => $customer->address,
                'created_at' => $customer->createdAt?->format('Y-m-d H:i:s'),
            ];
        }

        return $customers;
    }
}
