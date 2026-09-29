<?php

declare(strict_types=1);

namespace App\Http\Requests\Reports;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class GetMonitoringMonthlyStockReportRequest extends FormRequest
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
        $required = $this->ajax() ? 'required' : 'nullable';

        return [
            'year' => [$required, 'numeric'],
            'month' => [$required, 'numeric'],
        ];
    }

    /**
     * Merubah nama attribute untuk pesan error.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'year' => 'tahun',
            'month' => 'bulan',
        ];
    }
}
