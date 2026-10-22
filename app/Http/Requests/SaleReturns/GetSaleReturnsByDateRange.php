<?php

declare(strict_types=1);

namespace App\Http\Requests\SaleReturns;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class GetSaleReturnsByDateRange extends FormRequest
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
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $required = $this->ajax() ? 'required' : 'nullable';

        return [
            'start_date' => [
                $required,
                'date',
            ],
            'end_date' => [
                $required,
                'date',
                'after_or_equal:start_date',
            ],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'start_date' => 'tanggal mulai',
            'end_date' => 'tanggal akhir',
        ];
    }
}
