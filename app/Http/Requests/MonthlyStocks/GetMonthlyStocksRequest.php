<?php

declare(strict_types=1);

namespace App\Http\Requests\MonthlyStocks;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class GetMonthlyStocksRequest extends FormRequest
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
        // Tahun wajib hanya untuk request ajax (data tabel).
        $required = $this->ajax() ? 'required' : 'nullable';

        return [
            // Tahun tidak boleh melebihi tahun saat ini.
            'year' => [$required, 'integer', 'min:2000', 'max:'.now()->year],
            // Bulan boleh kosong untuk mengambil data dalam setahun.
            'month' => ['nullable', 'integer', 'between:1,12'],
        ];
    }

    /**
     * Merubah nama atribut.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'year' => 'tahun',
            'month' => 'bulan',
        ];
    }
}
