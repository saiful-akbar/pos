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
     * Awal bari yang akan diambil.
     */
    #[Override]
    public function startRow(): int
    {
        return 3;
    }

    /**
     * Aturan validasi.
     */
    #[Override]
    public function rules(): array
    {
        return [
            '*.0' => ['required', 'string'],
            '*.1' => ['required', 'numeric', 'min:0', 'max:999'],
        ];
    }

    /**
     * Merubah nama attribute untuk pesan error.
     */
    public function customValidationAttributes(): array
    {
        return [
            '*.0' => 'Kode produk',
            '*.1' => 'Diskon',
        ];
    }

    /**
     * Pesan error validasi.
     */
    public function customValidationMessages(): array
    {
        return [
            '*.0.required' => 'Kolom kode produk wajib diisi.',
            '*.0.string' => 'Kolom kode produk harus bertipe teks.',
            '*.1.required' => 'Kolom diskon wajib diisi.',
            '*.1.numeric' => 'Kolom diskon harus bertipe angka.',
            '*.1.min' => 'Kolom diskon tidak boleh bernilai kurang dari 0.',
            '*.1.max' => 'Kolom diskon tidak boleh bernilai lebih dari 999.',
        ];
    }

    /**
     * Import dari collection.
     */
    #[Override]
    public function collection(Collection $rows): void
    {
        // Ambil semua kode produk dari daftar import.
        $codes = $rows->pluck(0);

        // Ambil data kode produk dari database
        $products = Product::query()
            ->select('code')
            ->whereIn('code', $codes->unique()->values())
            ->get();

        // Periksa apakah ada kode produk yang tidak sesuai
        // antara data import dengan database
        $differeceCodes = array_diff(
            $codes->unique()->values()->toArray(),
            $products->pluck('code')->values()->toArray(),
        );

        if (count($differeceCodes) > 0) {
            throw ValidationException::withMessages(messages: [
                'file' => 'Terdapat kode produk yang tidak terdaftar: '.implode(', ', $differeceCodes),
            ]);
        }

        // Update diskon
        foreach ($rows as $row) {
            Product::query()
                ->where('code', $row[0])
                ->update(['discount' => $row[1]]);
        }

        // Buat catatan aktifitas.
        app(CreateActivity::class)->execute(dto: new CreateActivityDTO(
            userId: user()->id,
            logName: 'Import update diskon produk.',
            event: EventActivityEnum::UPDATED,
        ));
    }
}
