<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboards;

use App\Domain\Dashboard\Enums\DashboardPeriodEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class GetDashboardProductsRequest extends FormRequest
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
            'period' => ['nullable', Rule::enum(DashboardPeriodEnum::class)],
        ];
    }

    /**
     * Merubah nama atribut.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'period' => 'periode',
        ];
    }

    /**
     * Periode terpilih (default bulan berjalan).
     */
    public function period(): DashboardPeriodEnum
    {
        return DashboardPeriodEnum::tryFrom((string) $this->validated('period')) ?? DashboardPeriodEnum::MONTH;
    }
}
