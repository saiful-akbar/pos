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
            'name' => ['required', 'string', 'max:100'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'boolean'],
        ];
    }

    /**
     * Mengganti nama atribut
     *
     * @return void
     */
    #[Override]
    public function attributes()
    {
        return [
            'name' => 'nama',
            'phone_number' => 'nomor telepon',
            'address' => 'alamat',
            'notes' => 'catatan',
            'status' => 'status',
        ];
    }
}
