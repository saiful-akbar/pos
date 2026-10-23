<?php

declare(strict_types=1);

namespace App\Http\Requests\DailyStocks;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class ImportDailyStockRequest extends FormRequest
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
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5000'],
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
            'date' => 'tanggal stok opname',
            'file' => 'file',
            'notes' => 'catatan',
        ];
    }
}
