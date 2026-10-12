<?php

declare(strict_types=1);

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class StorePriceProductRequest extends FormRequest
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
            'unit_id' => ['required', 'exists:units,id'],
            'multiples_quantity' => ['required', 'numeric', 'min:1'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Merubah nama attribute untuk pesan error.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'unit_id' => 'unit (satuan)',
            'multiples_quantity' => 'kelipatan stok',
            'purchase_price' => 'harga beli',
            'selling_price' => 'harga jual',
            'is_default' => 'nilai default',
        ];
    }
}
