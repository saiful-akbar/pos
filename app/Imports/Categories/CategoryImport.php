<?php

declare(strict_types=1);

namespace App\Imports\Categories;

use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\ActivityLog\UseCases\CreateActivity;
use App\Application\ErrorLog\UseCases\CreateErrorLog;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Override;

class CategoryImport implements ToCollection, WithStartRow, WithValidation
{
    /**
     * Collection.
     */
    #[Override]
    public function collection(Collection $rows): void
    {
        /**
         * 1. Ambil seluruh code dari Excel
         */
        $codes = $rows
            ->pluck(0)
            ->map(fn ($code) => trim((string) $code))
            ->filter();

        /**
         * 2. Cek duplicate di dalam Excel
         */
        $duplicates = $codes
            ->duplicates()
            ->unique()
            ->values();

        if ($duplicates->isNotEmpty()) {
            throw ValidationException::withMessages([
                'file' => 'Kode berikut muncul lebih dari satu kali di Excel: '.$duplicates->implode(', '),
            ]);
        }

        /**
         * 3. Cek code yang sudah ada di database
         */
        $existsCodes = Category::query()
            ->whereIn('code', $codes->unique()->values())
            ->pluck('code');

        if ($existsCodes->isNotEmpty()) {
            throw ValidationException::withMessages([
                'file' => 'Kode berikut sudah terdaftar: '.$existsCodes->implode(', '),
            ]);
        }

        /**
         * 4. Simpan seluruh data dalam transaction
         */
        DB::connection(pos_connection())->beginTransaction();

        try {
            foreach ($rows as $row) {
                Category::query()->create([
                    'code' => strtoupper(trim((string) $row[0])),
                    'name' => trim((string) $row[1]),
                    'is_active' => (int) $row[2] === 1,
                ]);
            }

            app(CreateActivity::class)->execute(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Impor excel untuk menambah kategori produk baru',
                event: EventActivityEnum::CREATED,
            ));

            DB::connection(pos_connection())->commit();
        } catch (\Throwable $e) {
            DB::connection(pos_connection())->rollBack();

            app(CreateErrorLog::class)->execute(
                function: __METHOD__,
                message: $e->getMessage(),
            );
        }
    }

    /**
     * Aturan validasi.
     */
    #[Override]
    public function rules(): array
    {
        return [
            '0' => ['required', 'string', 'max:100'],
            '1' => ['required', 'string', 'max:200'],
            '2' => ['required', 'in:0,1'],
        ];
    }

    /**
     * Merubah nama kolom untuk pesan error validasi.
     */
    public function customValidationAttributes(): array
    {
        return [
            '0' => 'kode',
            '1' => 'nama',
            '2' => 'status',
        ];
    }

    #[Override]
    public function startRow(): int
    {
        return 3;
    }
}
