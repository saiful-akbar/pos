<?php

declare(strict_types=1);

namespace App\Http\Requests\Purchases;

use App\Domain\Purchase\Enums\PaymentMethodEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class StorePurchasePaymentRequest extends FormRequest
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
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', Rule::enum(PaymentMethodEnum::class)],
            'amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:200'],
        ];
    }

    /**
     * Merubah nama atribut untuk pesan error.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'payment_date' => 'tanggal pembayaran',
            'payment_method' => 'metode pembayaran',
            'amount' => 'jumlah',
            'notes' => 'catatan',
        ];
    }
}
