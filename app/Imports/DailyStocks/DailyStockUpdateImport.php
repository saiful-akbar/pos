<?php

declare(strict_types=1);

namespace App\Imports\DailyStocks;

use App\Application\Stock\UseCases\UpdateDailyStockByImport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Override;

class DailyStockUpdateImport implements ToCollection, WithStartRow, WithValidation
{
    /**
     * Create a new class instance.
     */
    public function __construct(private string $dailyStockId, private ?string $notes = null) {}

    /**
     * Awal baris data (baris 1 adalah header).
     */
    #[Override]
    public function startRow(): int
    {
        return 2;
    }

    /**
     * Aturan validasi tiap baris: 0 = kode produk, 1 = kode unit, 2 = qty.
     */
    #[Override]
    public function rules(): array
    {
        return [
            '*.0' => ['required', 'string', 'max:100'],
            '*.1' => ['required', 'string', 'max:100'],
            // Qty minimal 0.01 (tidak boleh 0 atau minus).
            '*.2' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    /**
     * Nama atribut (kolom) pada pesan error.
     */
    public function customValidationAttributes(): array
    {
        return [
            '*.0' => 'kode produk',
            '*.1' => 'kode unit',
            '*.2' => 'qty',
        ];
    }

    /**
     * Mengolah baris excel: normalisasi data lalu jalankan use case update stok harian.
     */
    #[Override]
    public function collection(Collection $rows): void
    {
        $data = $rows
            ->map(fn ($row): array => [
                'product_code' => trim((string) $row[0]),
                'unit_code' => trim((string) $row[1]),
                'quantity' => parse_float($row[2]),
            ])
            ->values()
            ->all();

        app(UpdateDailyStockByImport::class)->execute(
            dailyStockId: $this->dailyStockId,
            notes: $this->notes,
            rows: $data,
        );
    }
}
