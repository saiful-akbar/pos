<?php

declare(strict_types=1);

namespace App\Http\Requests\DailyStockOpnames;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class ImportDailyStockOpnameRequest extends FormRequest
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
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2048'],
            'file' => ['required', 'file', 'max:5120'],
        ];
    }

    /**
     * Merubah nama attribute untuk pesan error.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'date' => 'tanggal',
            'notes' => 'catatan',
            'file' => 'file',
        ];
    }
}
