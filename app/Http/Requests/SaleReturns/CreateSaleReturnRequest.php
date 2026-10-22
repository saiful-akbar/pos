<?php

declare(strict_types=1);

namespace App\Http\Requests\SaleReturns;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class CreateSaleReturnRequest extends FormRequest
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
            // Catatan bersifat opsional.
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Merubah nama atribut.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'notes' => 'catatan',
        ];
    }
}
