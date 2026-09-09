<?php

namespace App\Exports\Discounts;

use App\Application\Discount\UseCases\GetAllDiscounts;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Override;

class DiscountExport implements WithHeadings, ShouldAutoSize, FromArray
{
    /**
     * Header
     *
     * @return array
     */
    #[Override]
    public function headings(): array
    {
        return [
            'Diskon',
            'Nama (Deskripsi)',
            'Tanggak Efektif',
            'Tanggal Kadaluarsa',
            'Waktu Dibuat',
            'Waktu Diperbarui',
        ];
    }

    #[Override]
    public function array(): array
    {
        $result = [];
        $discounts = app(GetAllDiscounts::class)->execute();

        foreach ($discounts as $discount) {
            $result[] = [
                'discount' => $discount->discount . '%',
                'name' => $discount->name,
                'effective_date' => $discount->effectiveDate->format('Y-m-d'),
                'expiration_date' => $discount->expirationDate?->format('Y-m-d'),
                'created_at' => $discount->createdAt?->format('Y-m-d H:i:s'),
                'updated_at' => $discount->updatedAt?->format('Y-m-d H:i:s'),
            ];
        }

        return $result;
    }
}
