<?php

declare(strict_types=1);

namespace App\Http\Requests\Roles;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class CreateRoleRequest extends FormRequest
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
            'role_name' => ['required', 'string', 'unique:roles,name'],
            'role_description' => ['string', 'nullable'],
            'modules' => ['array'],
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'role_name' => 'nama role',
            'role_description' => 'deskripsi role',
        ];
    }
}
