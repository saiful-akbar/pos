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

class UpdateSellingPriceProductImport implements ToCollection, WithStartRow, WithValidation
{
    /**
     * Validasi.
     */
    #[Override]
    public function rules(): array
    {
        return [
            '*.0' => ['required', 'string'],
            '*.1' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Nama atribut kusstom.
     */
    public function customValidationAttributes(): array
    {
        return [
            '*.0' => 'kode produk',
            '*.1' => 'harga jual',
        ];
    }

    /**
     * Awal barik yang akan diambil.
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
            ->where('is_active', '=', 1)
            ->whereIn('code', $rows->pluck(0)->unique()->values())
            ->get();

        foreach ($rows as $row) {
            $code = strtoupper(trim($row[0]));
            $sellingPrice = $row[1];

            if ($products->contains('code', $code)) {
                Product::query()
                    ->where('code', '=', $code)
                    ->update(['selling_price' => $sellingPrice]);

                continue;
            }

            throw ValidationException::withMessages([
                'file' => "Kode produk tidak terdaftar: $code",
            ]);
        }

        app(CreateActivity::class)->execute(dto: new CreateActivityDTO(
            userId: user()->id,
            logName: 'Import excel untuk update harga jual produk',
            event: EventActivityEnum::UPDATED,
        ));
    }
}
