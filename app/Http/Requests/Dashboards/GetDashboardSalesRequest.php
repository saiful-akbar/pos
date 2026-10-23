<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboards;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class GetDashboardSalesRequest extends FormRequest
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
            // Tahun kalender; default tahun berjalan jika tidak dikirim.
            'year' => ['nullable', 'integer', 'min:2000', 'max:'.now()->year],
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
        ];
    }

    /**
     * Tahun terpilih (default tahun berjalan menurut zona waktu aplikasi).
     */
    public function year(): int
    {
        return (int) ($this->validated('year') ?? now()->year);
    }
}
