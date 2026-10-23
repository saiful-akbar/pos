<?php

declare(strict_types=1);

namespace App\Http\Requests\Cash;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class GetCashMonthRequest extends FormRequest
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
            'year' => ['required', 'integer', 'min:2000', 'max:'.(now()->year + 1)],
            'month' => ['required', 'integer', 'between:1,12'],
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
