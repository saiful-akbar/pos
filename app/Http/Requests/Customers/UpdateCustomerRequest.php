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
        $connection = pos_connection();
        $id = $this->id;

        return [
            'name' => ['required', 'string', 'max:100'],
            'phone_number' => ['nullable', 'string', 'max:20', "unique:$connection.customers,phone_number,$id,id"],
            'address' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Merubah nama attribut untuk pesan error.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'name' => 'name',
            'phone_number' => 'nomor telepon',
            'address' => 'alamat',
        ];
    }
}
