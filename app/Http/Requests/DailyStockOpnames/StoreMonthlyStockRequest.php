<?php

declare(strict_types=1);

namespace App\Http\Requests\DailyStockOpnames;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreMonthlyStockRequest extends FormRequest
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
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('m');

        return [
            'year' => ['required', 'numeric', "max:$currentYear"],
            'month' => ['required', 'numeric', "max:$currentMonth"],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Meruban nama attribute untuk pesan error.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'year' => 'tahun',
            'month' => 'bulan',
            'notes' => 'catatan',
        ];
    }
}
