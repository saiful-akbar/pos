<?php

declare(strict_types=1);

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreSaleReturnCartDetailRequest extends FormRequest
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
            'product_code' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * Merubah nama atribut untuk pesans error.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'product_code' => 'produk',
        ];
    }
}
