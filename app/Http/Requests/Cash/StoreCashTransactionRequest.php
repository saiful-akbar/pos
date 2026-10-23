<?php

declare(strict_types=1);

namespace App\Http\Requests\Cash;

use App\Application\Cash\DTOs\CashTransactionDTO;
use App\Domain\Cash\Enums\CashTransactionTypeEnum;
use Carbon\Carbon;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class StoreCashTransactionRequest extends FormRequest
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
            'transaction_date' => ['required', 'date_format:Y-m-d'],
            'type' => ['required', Rule::enum(CashTransactionTypeEnum::class)],
            // Nominal berupa angka desimal (maksimal 2 digit di belakang koma) dan lebih besar dari nol.
            'amount' => [
                'required',
                'regex:/^\d{1,13}(\.\d{1,2})?$/',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (is_string($value) && preg_match('/^\d{1,13}(\.\d{1,2})?$/', $value) === 1 && bccomp($value, '0', 2) <= 0) {
                        $fail('Nominal harus lebih besar dari 0.');
                    }
                },
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
            'reference_number' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Merubah nama atribut.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'transaction_date' => 'tanggal transaksi',
            'type' => 'jenis transaksi',
            'amount' => 'nominal',
            'notes' => 'catatan',
            'reference_number' => 'nomor referensi',
        ];
    }

    /**
     * Pesan validasi khusus.
     */
    #[Override]
    public function messages(): array
    {
        return [
            'amount.regex' => 'Nominal harus berupa angka positif dengan maksimal 2 digit desimal.',
        ];
    }

    /**
     * Membuat DTO dari data tervalidasi; pencatat selalu pengguna yang sedang login (bukan dari input client).
     */
    public function toDto(): CashTransactionDTO
    {
        $data = $this->validated();

        return new CashTransactionDTO(
            userId: user()->id,
            transactionDate: Carbon::createFromFormat('Y-m-d', $data['transaction_date'])->startOfDay(),
            type: CashTransactionTypeEnum::from($data['type']),
            amount: bcadd((string) $data['amount'], '0', 2),
            notes: filled($data['notes'] ?? null) ? trim((string) $data['notes']) : null,
            referenceNumber: filled($data['reference_number'] ?? null) ? trim((string) $data['reference_number']) : null,
        );
    }
}
