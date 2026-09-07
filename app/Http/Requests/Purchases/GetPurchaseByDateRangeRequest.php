<?php

declare(strict_types=1);

namespace App\Http\Requests\Purchases;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class GetPurchaseByDateRangeRequest extends FormRequest
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
        $required = $this->ajax() ? 'required' : 'nullable';

        return [
            'from_date' => [$required, 'date'],
            'to_date' => [$required, 'date'],
            'payment_status' => [$required, 'in:all,unpaid,patrial,paid'],
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'from_date' => 'tanggal awal',
            'to_date' => 'tanggal akhir',
            'payment_status' => 'status pembayaran',
        ];
    }
}
