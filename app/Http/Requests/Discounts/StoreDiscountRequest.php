<?php

declare(strict_types=1);

namespace App\Http\Requests\Discounts;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreDiscountRequest extends FormRequest
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
            'discount' => ['required', 'numeric', 'min:0'],
            'name' => ['required', 'string', 'max:200'],
            'effective_date' => ['required', 'date'],
            'expiration_date' => ['nullable', 'date'],
        ];
    }

    /**
     * Merubah nama attribute untuk pesan error.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'discount' => 'diskon',
            'name' => 'nama atau deskripsi',
            'effective_date' => 'tanggal efektif',
            'expiration_date' => 'tanggal kadaluarsa',
        ];
    }
}
