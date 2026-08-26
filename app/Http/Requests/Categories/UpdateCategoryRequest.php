<?php

declare(strict_types=1);

namespace App\Http\Requests\Categories;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:100', "unique:categories,code,{$this->id},id"],
            'name' => ['required', 'string', 'max:200'],
            'status' => ['required', 'boolean'],
        ];
    }

    /**
     * Merubah nama attribut untuk pesan error.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'code' => 'kode',
            'name' => 'nama',
            'status' => 'status',
        ];
    }
}
