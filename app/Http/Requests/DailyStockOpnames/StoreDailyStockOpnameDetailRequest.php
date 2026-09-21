<?php

declare(strict_types=1);

namespace App\Http\Requests\DailyStockOpnames;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreDailyStockOpnameDetailRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_code' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'numeric', 'min:0'],
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'product_code' => 'produk',
            'quantity' => 'quantity',
        ];
    }
}
