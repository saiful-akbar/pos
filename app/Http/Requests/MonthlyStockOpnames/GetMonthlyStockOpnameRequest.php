<?php

namespace App\Http\Requests\MonthlyStockOpnames;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class GetMonthlyStockOpnameRequest extends FormRequest
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
     *
     * @return array
     */
    public function rules(): array
    {
        $required = $this->ajax() ? 'required' : 'nullable';

        return [
            'year' => [$required, 'numeric'],
            'month' => [$required, 'numeric'],
        ];
    }

    /**
     * Merubah nama attribute untuk pesan error.
     *
     * @return array
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'year' => 'tahun',
            'month' => 'bulan',
        ];
    }

    /**
     * Kutomisasi pesan error.
     *
     * @return array
     */
    #[Override]
    public function messages(): array
    {
        return [
            'year.required' => 'kolom tahun wajib diisi.',
            'year.numeric' => 'kolom tahun tidak valid.',
            'month.numeric' => 'kolom bulan tidak valid.',
            'month.required' => 'kolom bulan wajib diisi.',
        ];
    }
}
