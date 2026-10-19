<?php

declare(strict_types=1);

namespace App\Http\Requests\Purchases;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class StorePurchaseCartDetailRequest extends FormRequest
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
            'product_code' => ['required', 'string', 'exists:products,code'],
        ];
    }

    /**
     * Mengganti nama attribute.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'product_code' => 'kode produk',
        ];
    }
}
