<?php

declare(strict_types=1);

namespace App\Http\Requests\Purchases;

use App\Domain\Purchase\Enums\PaymentMethodEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class CreatePurchasePaymentRequest extends FormRequest
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
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', Rule::enum(PaymentMethodEnum::class)],
            'amount' => ['required', 'numeric', 'min:0'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    #[Override]
    public function attributes(): array
    {
        return [
            'payment_date' => 'tanggal pembayaran',
            'payment_method' => 'metode pembayaran',
            'amount' => 'jumlah pembayaran',
            'reference_number' => 'nomor referensi',
            'notes' => 'catatan',
        ];
    }
}
