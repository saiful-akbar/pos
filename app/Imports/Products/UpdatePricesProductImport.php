<?php

declare(strict_types=1);

namespace App\Imports\Products;

use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\ActivityLog\UseCases\CreateActivity;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Models\Price;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Override;

class UpdatePricesProductImport implements ToCollection, WithStartRow, WithValidation
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
            '*.0' => ['required', 'string'], // Kode produk
            '*.1' => ['required', 'string'], // Kode unit
            '*.2' => ['required', 'numeric', 'min:0'], // Kelipatan quantity
            '*.3' => ['required', 'numeric', 'min:0'], // Harga beli
            '*.4' => ['required', 'numeric', 'min:0'], // Harga jual
        ];
    }

    /**
     * Merubah nama attribute untuk pesan error.
     */
    public function customValidationAttributes(): array
    {
        return [
            '*.0' => 'Kode produk',
            '*.1' => 'Kode unit (satuan)',
            '*.2' => 'Kelipatan quantity',
            '*.3' => 'Harga beli',
            '*.4' => 'Harga jual',
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
            '*.1.required' => 'Kolom kode unit (satuan) wajib diisi.',
            '*.1.string' => 'Kolom kode unit (satuan) harus bertipe teks.',
            '*.2.required' => 'Kolom kelipatan quantity wajib diisi.',
            '*.2.numeric' => 'Kolom kelipatan quantity harus bertipe angka.',
            '*.2.min' => 'Kolom kelipatan quantity tidakboleh kurang dari 0.',
            '*.3.required' => 'Kolom harga beli wajib diisi.',
            '*.3.numeric' => 'Kolom harga beli harus bertipe angka.',
            '*.3.min' => 'Kolom harga beli tidakboleh kurang dari 0.',
            '*.4.required' => 'Kolom harga jual wajib diisi.',
            '*.4.numeric' => 'Kolom harga jual harus bertipe angka.',
            '*.4.min' => 'Kolom harga jual tidakboleh kurang dari 0.',
        ];
    }

    /**
     * Import from collection.
     */
    #[Override]
    public function collection(Collection $rows): void
    {
        // Ambil semua daftar kode produk dari hasil import
        $productCodes = $rows->pluck(0)->unique();

        // Ambil daftar kode produk dari database
        $products = Product::query()
            ->select('id', 'code')
            ->whereIn(DB::raw('UPPER(code)'), $productCodes)
            ->get();

        // Periksa apakah ada kode produk yang tidak sesuai
        // antara data yang diimport dengan database.
        $differeceProductCodes = array_diff($productCodes->toArray(), $products->pluck('code')->toArray());

        if (count($differeceProductCodes) > 0) {
            throw ValidationException::withMessages(messages: [
                'file' => 'Terdapat kode produk yang tidak terdaftar: '.implode(', ', $differeceProductCodes),
            ]);
        }

        // Ambil data kode unit dari hasil import.
        $unitCodes = $rows->pluck(1)->unique();

        // Ambil daftar kode unit dari database.
        $units = Unit::query()
            ->select('id', 'code')
            ->whereIn(DB::raw('UPPER(code)'), $unitCodes)
            ->get();

        // Periksa apakah ada kode unit yang tidak sesuai
        // antara data yang diimport dengan database.
        $differenceUnitCodes = array_diff($unitCodes->toArray(), $units->pluck('code')->toArray());

        if (count($differenceUnitCodes) > 0) {
            throw ValidationException::withMessages(messages: [
                'file' => 'Terdapat kode unit (satuan) yang tidak terdaftar: '.implode(', ', $differenceUnitCodes),
            ]);
        }

        foreach ($rows as $row) {
            $product = $products->firstWhere('code', strtoupper($row[0]));
            $unit = $units->firstWhere('code', strtoupper($row[1]));

            // Ambil data harga berdasarkan kode produk dan kode unit.
            $price = Price::query()
                ->where('product_id', $product->id)
                ->where('unit_id', $unit->id)
                ->first();

            // Jika harga sudah ada update, jika null insert baru.
            if ($price === null) {
                Price::query()->create([
                    'product_id' => $product->id,
                    'unit_id' => $unit->id,
                    'multiples_quantity' => (int) $row[2],
                    'purchase_price' => (int) $row[3],
                    'selling_price' => (int) $row[4],
                ]);

                continue;
            }

            // Update
            Price::query()
                ->where('id', $price->id)
                ->update([
                    'multiples_quantity' => (int) $row[2],
                    'purchase_price' => (int) $row[3],
                    'selling_price' => (int) $row[4],
                ]);
        }

        // Buat catatan aktifitas.
        app(CreateActivity::class)->execute(dto: new CreateActivityDTO(
            userId: user()->id,
            logName: 'Import update harga produk.',
            event: EventActivityEnum::UPDATED,
        ));
    }
}
