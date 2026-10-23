<?php

declare(strict_types=1);

namespace App\Http\Requests\Cash;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class GetCashYearRequest extends FormRequest
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
        ];
    }
}
