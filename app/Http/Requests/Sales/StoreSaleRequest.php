<?php

declare(strict_types=1);

namespace App\Http\Requests\Sales;

use App\Domain\Purchase\Enums\PaymentMethodEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class StoreSaleRequest extends FormRequest
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
            'cart_id' => ['required', 'exists:sale_carts,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'notes' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', Rule::enum(PaymentMethodEnum::class)],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'change_amount' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Merubah nama atribute untuk pesan error.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'customer_id' => 'pelanggan',
            'notes' => 'catatan',
            'payment_method' => 'metode pembayaran',
            'paid_amount' => 'pembayaran',
            'change_amount' => 'kembalian',
        ];
    }
}
