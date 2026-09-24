<?php

declare(strict_types=1);

namespace App\Imports\StockOpnames;

use App\Application\ActivityLog\DTOs\AttributeActivityDTO;
use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\ActivityLog\UseCases\CreateActivity;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Models\DailyStockOpname;
use App\Models\DailyStockOpnameDetail;
use App\Models\Product;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Override;

class DailyStockOpnameImport implements ToCollection, WithStartRow, WithValidation
{
    /**
     * Constructor.
     */
    public function __construct(private DateTimeInterface $date, private ?string $notes) {}

    /**
     * Awal baris yang akan diambil.
     */
    #[Override]
    public function startRow(): int
    {
        return 2;
    }

    /**
     * Aturan validasi.
     */
    #[Override]
    public function rules(): array
    {
        return [
            '*.0' => ['required', 'string', 'max:100'],
            '*.3' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Nama custom attribute (kolom)
     */
    public function customValidationAttributes(): array
    {
        return [
            '*.0' => 'kode produk',
            '*.3' => 'quantity',
        ];
    }

    /**
     * Insert from collection
     */
    #[Override]
    public function collection(Collection $rows): void
    {
        // Ambil semua kode produk.
        $codes = $rows->pluck(0);

        // Periksa duplikat kode produk yang dikirim
        $duplicateCodes = $codes->duplicates()->unique()->values();

        if ($duplicateCodes->isNotEmpty()) {
            throw ValidationException::withMessages(messages: [
                'file' => 'Terdapat duplikat kode produk: '.$duplicateCodes->implode(', '),
            ]);
        }

        // Ambil daftar produk berdasarkan kode yang dikirim
        $products = Product::query()
            ->whereIn('code', $codes->unique()->values())
            ->where('is_active', 1)
            ->get();

        // Periksa kode produk yang tidak terdaftar pada database.
        $differenceCodes = $codes->unique()->values()->diff($products->pluck('code')->unique()->values());

        if ($differenceCodes->isNotEmpty()) {
            throw ValidationException::withMessages(messages: [
                'file' => 'Kode produk tidak terdaftar atau tidak aktif: '.$differenceCodes->implode(', '),
            ]);
        }

        // Periksa apakah sudah ada stock opname pada tanggal yang sama
        $existsStockOpname = DailyStockOpname::query()
            ->where('date', $this->date->format('Y-m-d'))
            ->exists();

        // Jika stock opname sudah ada pada tanggal yang sama kirimkan pesan error.
        if ($existsStockOpname) {
            throw ValidationException::withMessages(messages: [
                'date' => 'Sudah ada stock opname pada tanggal yang sama.',
            ]);
        }

        // Insert data daily stock opname
        $stockOpname = DailyStockOpname::query()->create([
            'user_id' => user()->id,
            'date' => $this->date->format('Y-m-d'),
            'total_product' => 0,
            'total_quantity' => 0,
            'notes' => $this->notes,
        ]);

        $totalProduct = 0;
        $totalQuantity = 0;

        // Insert data stock opname detail
        foreach ($rows as $row) {
            $code = $row[0];
            $quantity = parse_float($row[3]);

            foreach ($products as $product) {
                if ($product->code === $code) {
                    DailyStockOpnameDetail::query()->create([
                        'daily_stock_opname_id' => $stockOpname->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                    ]);

                    $totalProduct += 1;
                    $totalQuantity += $quantity;

                    break;
                }
            }
        }

        // Update total item dan total quantity dailt stock opname
        $stockOpname->total_product = $totalProduct;
        $stockOpname->total_quantity = $totalQuantity;
        $stockOpname->save();

        // Buat catatan aktifitas
        app(CreateActivity::class)->execute(dto: new CreateActivityDTO(
            userId: user()->id,
            logName: 'Import excel daily stock opname',
            event: EventActivityEnum::CREATED,
            attributeChanges: new AttributeActivityDTO(
                attributes: $stockOpname->toArray(),
            )
        ));
    }
}
