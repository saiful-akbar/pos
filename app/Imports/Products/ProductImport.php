<?php

declare(strict_types=1);

namespace App\Imports\Products;

use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\ActivityLog\UseCases\CreateActivity;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Override;

class ProductImport implements ToCollection, WithStartRow, WithValidation
{
    /**
     * Awal baris yang akan diambil
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
            '*.0' => ['required', 'string', 'max:50'], // code
            '*.1' => ['nullable', 'string', 'max:500'], // name
            '*.2' => ['required', 'string', 'max:50'], // category_code.
            '*.3' => ['nullable', 'numeric', 'min:0', 'max:999'], // discount
            '*.4' => ['nullable', 'numeric', 'min:0'], // minimum_stock
            '*.5' => ['nullable', 'numeric', 'min:0'], // current_stock
            '*.6' => ['required', 'in:1,0'], // is_active
        ];
    }

    /**
     * Merubah nama kolom untuk pesan error validasi.
     */
    public function customValidationAttributes(): array
    {
        return [
            '*.0' => 'Kode Produk',
            '*.1' => 'Nama',
            '*.2' => 'Kode Kategori',
            '*.3' => 'Diskon',
            '*.4' => 'Stok Minimum',
            '*.5' => 'Stok Tersedia',
            '*.6' => 'Status',
        ];
    }

    /**
     * Pesan error validasi custom.
     */
    public function customValidationMessages(): array
    {
        return [
            '*.0.required' => 'Kode Produk wajib diisi.',
            '*.0.string' => 'Kode Produk harus berupa teks.',
            '*.0.max' => 'Kode Produk maksimal 50 karakter.',
            '*.1.string' => 'Nama harus berupa teks.',
            '*.1.max' => 'Nama maksimal 500 karakter.',
            '*.2.required' => 'Kode Kategori wajib diisi.',
            '*.2.string' => 'Kode Kategori harus berupa teks.',
            '*.2.max' => 'Kode Kategori maksimal 50 karakter.',
            '*.3.numeric' => 'Diskon harus berupa angka.',
            '*.3.min' => 'Diskon tidak boleh kurang dari 0.',
            '*.3.max' => 'Diskon tidak boleh lebih dari 999.',
            '*.4.numeric' => 'Stok Minimum harus berupa angka.',
            '*.4.min' => 'Stok Minimum tidak boleh kurang dari 0.',
            '*.5.numeric' => 'Stok Tersedia harus berupa angka.',
            '*.5.min' => 'Stok Tersedia tidak boleh kurang dari 0.',
            '*.6.required' => 'Status wajib diisi.',
            '*.6.in' => 'Status hanya boleh bernilai 1 (aktif) atau 0 (nonaktif).',
        ];
    }

    /**
     * Insert from collection
     */
    #[Override]
    public function collection(Collection $rows): void
    {
        // 1. Ambil seluruh kode produk.
        $codes = $rows->pluck(0);

        // 2. Cek duplikat didalam excel
        $duplicateCodes = $codes->duplicates()->unique()->values();

        if ($duplicateCodes->isNotEmpty()) {
            throw ValidationException::withMessages([
                'file' => 'Terdapat duplikat kode produk: '.$duplicateCodes->implode(', '),
            ]);
        }

        // 3. Periksa duplikat didatabase
        $existsCode = Product::query()
            ->whereIn('code', $codes->unique()->values())
            ->exists();

        if ($existsCode) {
            throw ValidationException::withMessages([
                'file' => 'Terdapat kode produk yang sudah terdaftar',
            ]);
        }

        // 7. Ambil id category
        $categories = Category::query()
            ->select('id', 'code')
            ->whereIn('code', $rows->pluck(2)->unique()->values())
            ->where('is_active', '=', 1)
            ->get();

        foreach ($rows as $row) {
            $categoryCode = strtoupper(trim($row[2]));

            // Periksa apakah kategori ada atau tidak
            if (! $categories->contains('code', $categoryCode)) {
                throw ValidationException::withMessages([
                    'file' => "Kode kategori tidak terdaftar: $categoryCode",
                ]);
            }

            $category = $categories->firstWhere('code', $categoryCode);

            Product::query()->create([
                'code' => $row[0],
                'name' => $row[1],
                'category_id' => $category['id'],
                'discount' => $row[3],
                'minimum_stock' => $row[4],
                'current_stock' => $row[5],
                'is_active' => $row[6],
            ]);
        }

        app(CreateActivity::class)->execute(new CreateActivityDTO(
            userId: user()->id,
            logName: 'Import excel untuk menambah produk baru',
            event: EventActivityEnum::CREATED,
        ));
    }
}
