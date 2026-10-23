<?php

declare(strict_types=1);

namespace App\Http\Requests\Cash;

use App\Application\Cash\DTOs\CashReportFilterDTO;
use App\Domain\Cash\Enums\CashTransactionTypeEnum;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class GetCashTransactionsRequest extends FormRequest
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
        // Periode wajib untuk permintaan data (ajax, total, export); pemuatan halaman biasa boleh kosong.
        $required = $this->routeIs('cash.transactions', 'reports.cash') && ! $this->ajax() ? 'nullable' : 'required';

        return [
            'start_date' => [$required, 'date_format:Y-m-d'],
            'end_date' => [$required, 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'type' => ['nullable', Rule::enum(CashTransactionTypeEnum::class)],
            'user_id' => ['nullable', 'string', 'exists:users,id'],
        ];
    }

    /**
     * Merubah nama atribut.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'start_date' => 'tanggal mulai',
            'end_date' => 'tanggal akhir',
            'type' => 'jenis transaksi',
            'user_id' => 'pencatat',
        ];
    }

    /**
     * Membuat DTO filter dari data yang sudah tervalidasi.
     */
    public function toFilter(): CashReportFilterDTO
    {
        $data = $this->validated();

        return new CashReportFilterDTO(
            startDate: Carbon::createFromFormat('Y-m-d', $data['start_date'])->startOfDay(),
            endDate: Carbon::createFromFormat('Y-m-d', $data['end_date'])->startOfDay(),
            type: filled($data['type'] ?? null) ? CashTransactionTypeEnum::from($data['type']) : null,
            userId: filled($data['user_id'] ?? null) ? (string) $data['user_id'] : null,
        );
    }
}
