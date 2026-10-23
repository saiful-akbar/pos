<?php

declare(strict_types=1);

namespace App\Http\Requests\Reports;

use App\Application\Report\DTOs\SaleReturnReportFilterDTO;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class GetSaleReturnReportRequest extends FormRequest
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
        // Tanggal wajib untuk permintaan data (ajax, summary, analisis, export); pemuatan halaman biasa boleh kosong.
        $required = $this->routeIs('reports.returns') && ! $this->ajax() ? 'nullable' : 'required';

        return [
            'start_date' => [$required, 'date_format:Y-m-d'],
            'end_date' => [$required, 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'return_number' => ['nullable', 'string', 'max:50'],
            'invoice_number' => ['nullable', 'string', 'max:50'],
            'user_id' => ['nullable', 'string', 'exists:users,id'],
            'customer_name' => ['nullable', 'string', 'max:100'],
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
            'return_number' => 'nomor pengembalian',
            'invoice_number' => 'nomor invoice',
            'user_id' => 'petugas',
            'customer_name' => 'pelanggan',
            'category_id' => 'kategori',
            'product_code' => 'kode produk',
        ];
    }

    /**
     * Membuat DTO filter dari data yang sudah tervalidasi.
     */
    public function toFilter(): SaleReturnReportFilterDTO
    {
        $data = $this->validated();
        $pick = fn (string $key): ?string => filled($data[$key] ?? null) ? trim((string) $data[$key]) : null;

        return new SaleReturnReportFilterDTO(
            startDate: Carbon::createFromFormat('Y-m-d', $data['start_date'])->startOfDay(),
            endDate: Carbon::createFromFormat('Y-m-d', $data['end_date'])->startOfDay(),
            returnNumber: $pick('return_number'),
            invoiceNumber: $pick('invoice_number'),
            userId: $pick('user_id'),
            customerName: $pick('customer_name'),
            categoryId: $pick('category_id'),
            productCode: $pick('product_code'),
        );
    }
}
