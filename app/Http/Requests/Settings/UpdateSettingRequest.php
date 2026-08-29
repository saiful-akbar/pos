<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class UpdateSettingRequest extends FormRequest
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
            'app_name' => ['required', 'string', 'max:100'],
            'store_name' => ['required', 'string', 'max:100'],
            'store_address' => ['nullable', 'string', 'max:1000'],
            'store_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:1024'],
            'background_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:1024'],
        ];
    }

    /**
     * Merubah nama attribute untuk pesan error.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'app_name' => 'nama aplikasi',
            'store_name' => 'nama toko',
            'store_address' => 'alamat toko',
            'store_logo' => 'logo',
            'background_image' => 'gambar latar belakang',
        ];
    }
}
