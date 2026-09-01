<?php

declare(strict_types=1);

namespace App\Http\Requests\Purchases;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class StorePuchaseCartDetailRequest extends FormRequest
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
            'purchase_price' => ['numeric', 'min:0', 'nullable'],
            'quantity' => ['numeric', 'min:1', 'nullable'],
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'product_code' => 'kode atau barcode produk',
            'purchase_price' => 'harga beli',
            'quantity' => 'quantity',
        ];
    }
}
