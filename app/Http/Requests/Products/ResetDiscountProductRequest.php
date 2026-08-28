<?php

declare(strict_types=1);

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class ResetDiscountProductRequest extends FormRequest
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
            'discount' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    /**
     * Merubah nama attribute.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'discount' => 'diskon',
        ];
    }
}
