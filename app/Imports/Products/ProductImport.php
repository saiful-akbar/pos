<?php

declare(strict_types=1);

namespace App\Imports\Products;

use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\ActivityLog\UseCases\CreateActivity;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Override;

class ProductImport implements ToCollection, WithStartRow, WithValidation
{
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
                'file' => 'Terdapat duplikat kode produk: ' . $duplicateCodes->implode(', '),
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

        // 4. Ambil semua barcode produk
        $barcodes = $rows->pluck(1);

        // 5. Periksa duplikat barcode produk.
        $duplicateBarcodes = $barcodes->duplicates()->unique()->values();

        if ($duplicateBarcodes->isNotEmpty()) {
            throw ValidationException::withMessages([
                'file' => 'Terdapat duplikat barcode produk: ' . $duplicateBarcodes->implode(', '),
            ]);
        }

        // 6. Periksa apakah barcode sudah ada pada database.
        $existsBarcode = Product::query()
            ->whereIn('barcode', $barcodes->unique()->values())
            ->exists();

        if ($existsBarcode) {
            throw ValidationException::withMessages([
                'file' => 'Terdapat barcode produk yang sudah terdaftar.',
            ]);
        }

        // 7. Ambil id category
        $categories = Category::query()
            ->select('id', 'code')
            ->whereIn('code', $rows->pluck(3)->unique()->values())
            ->where('is_active', '=', 1)
            ->get();

        // 8. Ambil id unit
        $units = Unit::query()
            ->select('id', 'code')
            ->whereIn('code', $rows->pluck(4)->unique()->values())
            ->where('is_active', '=', 1)
            ->get();

        foreach ($rows as $row) {
            $categoryCode = strtoupper(trim($row[3]));
            $unitCode = strtoupper(trim($row[4]));

            // Periksa apakah kategori ada atau tidak
            if (! $categories->contains('code', $categoryCode)) {
                throw ValidationException::withMessages([
                    'file' => "Kode kategori tidak terdaftar: $categoryCode",
                ]);
            }

            // Periksa apakah unit ada atau tidak
            if (! $units->contains('code', $unitCode)) {
                throw ValidationException::withMessages([
                    'file' => "Kode unit tidak terdaftar: $unitCode",
                ]);
            }

            $category = $categories->firstWhere('code', $categoryCode);
            $unit = $units->firstWhere('code', $unitCode);

            Product::query()->create([
                'code' => $row[0],
                'barcode' => $row[1],
                'name' => $row[2],
                'category_id' => $category['id'],
                'unit_id' => $unit['id'],
                'purchase_price' => $row[5],
                'selling_price' => $row[6],
                'discount' => $row[7],
                'current_stock' => $row[8],
                'minimum_stock' => $row[9],
                'is_active' => $row[10],
            ]);
        }

        app(CreateActivity::class)->execute(new CreateActivityDTO(
            userId: user()->id,
            logName: "Import excel untuk menambah produk baru",
            event: EventActivityEnum::CREATED,
        ));
    }

    /**
     * Aturan validasi.
     */
    #[Override]
    public function rules(): array
    {
        return [
            '*.0' => ['required', 'string', 'max:50'], // code
            '*.1' => ['required', 'string', 'max:50'], // barcode
            '*.2' => ['nullable', 'string', 'max:500'], // name
            '*.3' => ['required', 'string'], // kode kategory
            '*.4' => ['required', 'string'], // kode unit
            '*.5' => ['nullable', 'numeric', 'min:0'], // harga beli
            '*.6' => ['nullable', 'numeric', 'min:0'], // harga jual
            '*.7' => ['nullable', 'numeric', 'min:0', 'max:100'], // diskon
            '*.8' => ['nullable', 'numeric', 'min:0'], // stok tersedia
            '*.9' => ['nullable', 'numeric', 'min:0'], // minimum stok
            '*.10' => ['required', 'in:1,0'], // status keaktifan
        ];
    }

    /**
     * Merubah nama kolom untuk pesan error validasi.
     */
    public function customValidationAttributes(): array
    {
        return [
            '*.0' => 'Kode Produk',
            '*.1' => 'Barcode',
            '*.2' => 'Nama',
            '*.3' => 'Kode Kategori',
            '*.4' => 'Kode Unit',
            '*.5' => 'Harga Beli',
            '*.6' => 'Harga Jual',
            '*.7' => 'Diskon',
            '*.8' => 'Stok Tersedia',
            '*.9' => 'Stok Minimum',
            '*.10' => 'Status',
        ];
    }

    /**
     * Awal baris yang akan diambil.
     */
    #[Override]
    public function startRow(): int
    {
        return 3;
    }
}
