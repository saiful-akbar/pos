<?php

declare(strict_types=1);

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreSaleCartDetailRequest extends FormRequest
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
            'product' => ['required', 'string', 'max:100'],
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'product' => 'produk',
        ];
    }
}
