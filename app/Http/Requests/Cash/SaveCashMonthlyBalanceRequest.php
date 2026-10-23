<?php

declare(strict_types=1);

namespace App\Http\Requests\Cash;

use App\Application\Cash\DTOs\CashMonthlyBalanceDTO;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class SaveCashMonthlyBalanceRequest extends FormRequest
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
            'year' => ['required', 'integer', 'min:2000', 'max:'.(now()->year + 1)],
            'month' => ['required', 'integer', 'between:1,12'],
            // Saldo awal berupa angka desimal tidak negatif (maksimal 2 digit di belakang koma).
            'opening_balance' => ['required', 'regex:/^\d{1,13}(\.\d{1,2})?$/'],
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
            'year' => 'tahun',
            'month' => 'bulan',
            'opening_balance' => 'saldo awal',
            'notes' => 'catatan',
        ];
    }

    /**
     * Pesan validasi khusus.
     */
    #[Override]
    public function messages(): array
    {
        return [
            'opening_balance.regex' => 'Saldo awal harus berupa angka tidak negatif dengan maksimal 2 digit desimal.',
        ];
    }

    /**
     * Membuat DTO dari data tervalidasi; pencatat selalu pengguna yang sedang login.
     */
    public function toDto(): CashMonthlyBalanceDTO
    {
        $data = $this->validated();

        return new CashMonthlyBalanceDTO(
            userId: user()->id,
            year: (int) $data['year'],
            month: (int) $data['month'],
            openingBalance: bcadd((string) $data['opening_balance'], '0', 2),
            notes: filled($data['notes'] ?? null) ? trim((string) $data['notes']) : null,
        );
    }
}
