<?php

declare(strict_types=1);

namespace App\Http\Requests\Reports;

use App\Application\Report\DTOs\SaleReportFilterDTO;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class GetSaleReportRequest extends FormRequest
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
        // Tanggal wajib untuk permintaan data (ajax, summary, export, pdf); pemuatan halaman biasa boleh kosong.
        $required = $this->routeIs('reports.sales') && ! $this->ajax() ? 'nullable' : 'required';

        return [
            'start_date' => [$required, 'date_format:Y-m-d'],
            'end_date' => [$required, 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'user_id' => ['nullable', 'string', 'exists:users,id'],
            'payment_method' => ['nullable', Rule::in(['cash', 'qris', 'debit', 'credit'])],
            'invoice_number' => ['nullable', 'string', 'max:50'],
            'category_id' => ['nullable', 'string', 'exists:categories,id'],
            'product_code' => ['nullable', 'string', 'max:100', 'exists:products,code'],
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
            'user_id' => 'kasir',
            'payment_method' => 'metode pembayaran',
            'invoice_number' => 'nomor invoice',
            'category_id' => 'kategori',
            'product_code' => 'kode produk',
        ];
    }

    /**
     * Membuat DTO filter dari data yang sudah tervalidasi.
     */
    public function toFilter(): SaleReportFilterDTO
    {
        $data = $this->validated();
        $pick = fn (string $key): ?string => filled($data[$key] ?? null) ? trim((string) $data[$key]) : null;

        return new SaleReportFilterDTO(
            startDate: Carbon::createFromFormat('Y-m-d', $data['start_date'])->startOfDay(),
            endDate: Carbon::createFromFormat('Y-m-d', $data['end_date'])->startOfDay(),
            userId: $pick('user_id'),
            paymentMethod: $pick('payment_method'),
            invoiceNumber: $pick('invoice_number'),
            categoryId: $pick('category_id'),
            productCode: $pick('product_code'),
        );
    }
}
