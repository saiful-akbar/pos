<?php

declare(strict_types=1);

namespace App\Http\Requests\Purchases;

use App\Domain\Purchase\Enums\PaymentMethodEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class SavePurchaseRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'payment_method' => ['required', Rule::enum(PaymentMethodEnum::class)],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Merubah nama atribut
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'invoice_number' => 'nomor invoice',
            'supplier_id' => 'pemasok',
            'payment_method' => 'metode pembayarn',
            'amount_paid' => 'jumlah yang dibayarkan',
            'notes' => 'catatan',
        ];
    }
}
