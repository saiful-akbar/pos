<?php

declare(strict_types=1);

namespace App\Http\Requests\SaleReturns;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreSaleReturnCartRequest extends FormRequest
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
            'invoice_number' => ['required', 'string', 'max:13'],
        ];
    }

    /**
     * Merubah nama atribut.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'invoice_number' => 'nomor faktur penjualan',
        ];
    }
}
