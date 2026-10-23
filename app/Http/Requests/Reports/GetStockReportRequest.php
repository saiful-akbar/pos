<?php

declare(strict_types=1);

namespace App\Http\Requests\Reports;

use App\Application\Report\DTOs\StockReportFilterDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class GetStockReportRequest extends FormRequest
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
        // Periode wajib untuk permintaan data (ajax, summary, mutasi, export); pemuatan halaman biasa boleh kosong.
        $required = $this->routeIs('reports.stocks') && ! $this->ajax() ? 'nullable' : 'required';

        return [
            'year' => [$required, 'integer', 'min:2000', 'max:'.(now()->year + 1)],
            'month' => [$required, 'integer', 'between:1,12'],
            'category_id' => ['nullable', 'string', 'exists:categories,id'],
            'keyword' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(StockReportFilterDTO::STATUSES)],
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
            'category_id' => 'kategori',
            'keyword' => 'kata kunci',
            'status' => 'status selisih',
        ];
    }

    /**
     * Membuat DTO filter dari data yang sudah tervalidasi.
     */
    public function toFilter(): StockReportFilterDTO
    {
        $data = $this->validated();
        $pick = fn (string $key): ?string => filled($data[$key] ?? null) ? trim((string) $data[$key]) : null;

        return new StockReportFilterDTO(
            year: (int) $data['year'],
            month: (int) $data['month'],
            categoryId: $pick('category_id'),
            keyword: $pick('keyword'),
            status: $pick('status'),
        );
    }
}
