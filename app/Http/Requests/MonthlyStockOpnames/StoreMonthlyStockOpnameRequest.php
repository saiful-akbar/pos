<?php

declare(strict_types=1);

namespace App\Http\Requests\MonthlyStockOpnames;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreMonthlyStockOpnameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'daily_stock_opname_date' => ['required', 'date'],
            'year' => ['required', 'date_format:Y'],
            'month' => ['required', 'date_format:m'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Merubah nama attribute untuk pesan error.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'daily_stock_opname_date' => 'tanggal stok harian',
            'year' => 'tahun',
            'month' => 'bulan',
            'notes' => 'catatan',
        ];
    }
}
