<?php

declare(strict_types=1);

namespace App\Http\Requests\Customers;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class UpdateCustomerRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:20', "unique:customers,phone_number,{$this->id},id"],
            'address' => ['nullable', 'string', 'max:500'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    /**
     * Mengganti nama attribute untuk pesan error.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'name' => 'name',
            'phone_number' => 'nomor telepon',
            'address' => 'alamat',
            'discount' => 'diskon',
            'is_active' => 'status keaktifan',
        ];
    }
}
