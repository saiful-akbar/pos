<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreSaleReturnRequest extends FormRequest
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
        return [
            'notes' => ['nullable', 'string', 'max:500']
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'notes' => 'catatan'
        ];
    }
}
