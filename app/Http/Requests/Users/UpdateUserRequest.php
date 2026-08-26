<?php

declare(strict_types=1);

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class UpdateUserRequest extends FormRequest
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
            'full_name' => ['required', 'string', 'max:100'],
            'role' => ['required', 'exists:roles,id'],
            'username' => ['required', 'string', 'max:200', "unique:users,username,{$this->id},id"],
            'password' => ['nullable', 'string', 'max:200'],
            'photo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:1024'],
            'status' => ['required', 'boolean'],
        ];
    }

    /**
     * Merubah nama attribute untuk pesan error.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'full_name' => 'nama lengkap',
            'role' => 'role',
            'username' => 'username',
            'password' => 'kata sandi',
            'photo' => 'foto',
            'status' => 'status',
        ];
    }
}
