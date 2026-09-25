<?php

declare(strict_types=1);

namespace App\Http\Requests\MonthlyStockOpnames;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class UpdateMonthlyStockOpnameRequest extends FormRequest
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
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'daily_stock_opname_date' => 'tanggal stok harian',
            'notes' => 'catatan',
        ];
    }
}
