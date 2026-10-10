<?php

declare(strict_types=1);

namespace App\Http\Requests\Units;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class UpdateUnitRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:100', "unique:units,code,{$this->id},id"],
            'name' => ['required', 'string', 'max:200'],
            'is_active' => ['required', 'boolean'],
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
            'is_active' => 'status',
        ];
    }
}
