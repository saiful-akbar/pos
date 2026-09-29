<?php

declare(strict_types=1);

namespace App\Http\Requests\Reports;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class GetSaleReturnsReportRequest extends FormRequest
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
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'required_with:from_date', 'date'],
            'return_number' => ['nullable', 'string', 'max:50'],
            'invoice_number' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Merubah nama attribute untuk pesan error.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'from_date' => 'tanggal awal',
            'to_date' => 'tanggal akhir',
            'return_number' => 'nomor pengembalian',
            'invoice_number' => 'faktur pembelian',
        ];
    }
}
