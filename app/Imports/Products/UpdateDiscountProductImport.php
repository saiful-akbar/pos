<?php

declare(strict_types=1);

namespace App\Imports\Products;

use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\ActivityLog\UseCases\CreateActivity;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Override;

class UpdateDiscountProductImport implements ToCollection, WithStartRow, WithValidation
{
    /**
     * Validasi
     */
    #[Override]
    public function rules(): array
    {
        return [
            '*.0' => ['required', 'string'],
            '*.1' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    /**
     * Merubah nama kolom untuk pesan error validasi.
     */
    public function customValidationAttributes(): array
    {
        return [
            '*.0' => 'Kode Produk',
            '*.1' => 'Diskon',
        ];
    }

    /**
     * Awal baris excel yang akan diambil.
     */
    #[Override]
    public function startRow(): int
    {
        return 3;
    }

    /**
     * Import to collection.
     */
    #[Override]
    public function collection(Collection $rows): void
    {
        $products = Product::query()
            ->select('code')
            ->whereIn('code', $rows->pluck(0)->unique()->values())
            ->get();

        foreach ($rows as $row) {
            $code = strtoupper(trim($row[0]));

            // Jika kode produk yang diimport terdafar, lanjutkan proses update.
            if ($products->contains('code', $code)) {
                Product::query()->where('code', $code)->update(['discount' => $row[1]]);

                continue;
            }

            throw ValidationException::withMessages([
                'file' => 'Kode produk tidak terdaftar: '.$row[0],
            ]);
        }

        app(CreateActivity::class)->execute(new CreateActivityDTO(
            userId: user()->id,
            logName: 'Import excel untuk memperbarui diskon produk',
            event: EventActivityEnum::UPDATED,
        ));
    }
}
