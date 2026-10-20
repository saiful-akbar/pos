<?php

declare(strict_types=1);

namespace App\Http\Requests\Purchases;

use App\Domain\Purchase\Enums\PaymentMethodEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class CreatePurchaseRequest extends FormRequest
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
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'transaction_date' => ['required', 'date'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['nullable', Rule::enum(PaymentMethodEnum::class)],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:200'],
        ];
    }

    /**
     * Merubah nama atribut.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'supplier_id' => 'pemasok',
            'transaction_date' => 'tanggal',
            'invoice_number' => 'nomor faktur',
            'payment_method' => 'metode pembayaran',
            'amount_paid' => 'jumlah yang dibayarkan',
            'notes' => 'catatan',
        ];
    }
}
