<?php

declare(strict_types=1);

namespace App\Http\Requests\DailyStocks;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class ImportUpdateDailyStockRequest extends FormRequest
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
            // File maksimal 5MB (5120 KB) bertipe xlsx, xls, atau csv.
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
            // Catatan bersifat opsional.
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Merubah nama atribut.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'file' => 'file',
            'notes' => 'catatan',
        ];
    }
}
