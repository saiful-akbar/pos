<?php

declare(strict_types=1);

namespace App\Http\Requests\Suppliers;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class UpdateSupplierRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100', "unique:suppliers,name,{$this->id},id"],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
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
            'is_active' => 'status keaktifan',
        ];
    }
}
