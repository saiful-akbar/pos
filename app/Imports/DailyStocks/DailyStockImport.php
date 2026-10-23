<?php

declare(strict_types=1);

namespace App\Imports\DailyStocks;

use App\Application\Stock\UseCases\ImportDailyStock;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Override;

class DailyStockImport implements ToCollection, WithStartRow, WithValidation
{
    /**
     * Create a new class instance.
     */
    public function __construct(private DateTimeInterface $date, private ?string $notes = null) {}

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
            // Qty minimal 0.01 (tidak boleh 0 atau minus); "gt:0" tidak dipakai karena dianggap membandingkan dengan field lain.
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
     * Mengolah baris excel: normalisasi data lalu jalankan use case import stok harian.
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

        app(ImportDailyStock::class)->execute(
            date: $this->date,
            notes: $this->notes,
            rows: $data,
        );
    }
}
