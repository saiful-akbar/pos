<?php

declare(strict_types=1);

namespace App\Http\Requests\Reports;

use App\Application\Report\DTOs\PurchaseReportFilterDTO;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class GetPurchaseReportRequest extends FormRequest
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
        $required = $this->routeIs('reports.purchases') && ! $this->ajax() ? 'nullable' : 'required';

        return [
            'start_date' => [$required, 'date_format:Y-m-d'],
            'end_date' => [$required, 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'supplier_id' => ['nullable', 'string', 'exists:suppliers,id'],
            'payment_status' => ['nullable', Rule::enum(PaymentStatusEnum::class)],
            'invoice_number' => ['nullable', 'string', 'max:100'],
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
            'supplier_id' => 'pemasok',
            'payment_status' => 'status pembayaran',
            'invoice_number' => 'nomor faktur',
        ];
    }

    /**
     * Membuat DTO filter dari data yang sudah tervalidasi.
     */
    public function toFilter(): PurchaseReportFilterDTO
    {
        $data = $this->validated();

        return new PurchaseReportFilterDTO(
            startDate: Carbon::createFromFormat('Y-m-d', $data['start_date'])->startOfDay(),
            endDate: Carbon::createFromFormat('Y-m-d', $data['end_date'])->startOfDay(),
            supplierId: filled($data['supplier_id'] ?? null) ? $data['supplier_id'] : null,
            paymentStatus: filled($data['payment_status'] ?? null) ? PaymentStatusEnum::from($data['payment_status']) : null,
            invoiceNumber: filled($data['invoice_number'] ?? null) ? trim($data['invoice_number']) : null,
        );
    }
}
