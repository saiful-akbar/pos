@php
    $store = setting();

    $paymentMethods = [
        'cash' => 'Tunai',
        'qris' => 'QRIS',
        'debit' => 'Debit',
        'credit' => 'Kredit',
    ];

    $paymentMethod = $paymentMethods[$sale->paymentMethod]
        ?? ucfirst($sale->paymentMethod);

    $customerName = data_get($sale->customer, 'name')
        ?? data_get($sale->customer, 'fullName')
        ?? '-';

    // Logo toko sebagai data URI agar dapat dirender DomPDF
    $logoSrc = logo_data_uri($store?->storeLogo);

    $formatNumber = static fn ($value): string => number_format((float) ($value ?? 0), 2, ',', '.');
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <title>Invoice {{ $sale->invoiceNumber }}</title>

    <style>
        @page {
            size: A5 portrait;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            color: #222;
            background: #fff;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 8px;
            line-height: 1.4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }

        td,
        th {
            vertical-align: top;
        }

        .header-table {
            margin-bottom: 12px;
        }

        .header-table td {
            padding: 0;
        }

        .store-name {
            margin: 0 0 4px;
            font-size: 13px;
            font-weight: bold;
        }

        .store-address {
            margin: 0;
            font-size: 8px;
            line-height: 1.5;
        }

        .invoice-title {
            margin: 0 0 7px;
            font-size: 16px;
            font-weight: bold;
            text-align: right;
        }

        .invoice-info {
            width: 100%;
            font-size: 8px;
        }

        .invoice-info td {
            padding: 2px 0;
        }

        .invoice-info .info-label {
            width: 45%;
            white-space: nowrap;
        }

        .invoice-info .info-value {
            width: 55%;
            text-align: left;
            overflow-wrap: anywhere;
        }

        .section {
            margin-top: 10px;
        }

        .section-heading {
            margin: 0 0 5px;
            font-size: 9px;
            font-weight: bold;
        }

        .customer-table td {
            padding: 2px 0;
        }

        .muted {
            color: #666;
        }

        .divider {
            height: 1px;
            margin: 9px 0;
            border-top: 1px solid #999;
        }

        .items-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 7px;
        }

        .items-table .product-col {
            width: 22%;
        }

        .items-table .unit-col {
            width: 15%;
        }

        .items-table .price-col {
            width: 14%;
        }

        .items-table .qty-col {
            width: 8%;
        }

        .items-table .subtotal-col {
            width: 14%;
        }

        .items-table .discount-col {
            width: 11%;
        }

        .items-table .grand-total-col {
            width: 21%;
        }

        .items-table th,
        .items-table td {
            padding: 5px 2px;
            overflow-wrap: break-word;
            vertical-align: top;
        }

        .items-table th {
            border-top: 1px solid #555;
            border-bottom: 1px solid #555;
        }

        .items-table td {
            border-bottom: 1px solid #ddd;
        }

        .items-table .text-right {
            text-align: right;
        }

        .items-table .text-left {
            text-align: left;
        }

        .items-table thead {
            display: table-header-group;
        }

        .items-table tr {
            page-break-inside: avoid;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .product-name {
            font-weight: bold;
            overflow-wrap: break-word;
        }

        .product-code {
            display: block;
            margin-top: 2px;
            color: #666;
            font-size: 7px;
        }

        .empty-row {
            padding: 12px 4px !important;
            text-align: center;
        }

        .summary-wrapper {
            margin-top: 10px;
        }

        .summary-table {
            width: 62%;
            margin-left: auto;
            font-size: 8px;
        }

        .summary-table td {
            padding: 3px 0 3px 5px;
        }

        .summary-table .summary-label {
            text-align: left;
        }

        .summary-table .summary-value {
            text-align: right;
            white-space: nowrap;
        }

        .summary-table .grand-total td {
            padding-top: 6px;
            border-top: 1px solid #555;
            font-size: 10px;
            font-weight: bold;
        }

        .payment-table td {
            padding: 3px 0;
        }

        .payment-table td:last-child {
            text-align: right;
        }

        .notes {
            margin-top: 10px;
            padding: 6px;
            border: 1px solid #ddd;
            page-break-inside: avoid;
        }

        .notes p {
            margin: 3px 0 0;
            overflow-wrap: break-word;
        }

        .footer-table {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .footer-table td {
            width: 50%;
            padding: 5px;
            text-align: center;
        }

        .signature-space {
            height: 38px;
        }

        .signature-line {
            border-top: 1px solid #999;
            padding-top: 4px;
        }

        .footer-note {
            margin-top: 12px;
            padding-top: 6px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 7px;
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 52%; padding-right: 8px;">
                @if ($logoSrc)
                    <div style="margin-bottom: 5px;">
                        <img
                            src="{{ $logoSrc }}"
                            alt="Logo"
                            style="max-width: 22mm; max-height: 16mm;"
                        >
                    </div>
                @endif

                <h1 class="store-name">
                    {{ $store?->storeName ?? config('app.name') }}
                </h1>

                @if ($store?->storeAddress)
                    <p class="store-address">
                        {{ $store->storeAddress }}
                    </p>
                @endif
            </td>

            <td style="width: 48%;">
                <h2 class="invoice-title">INVOICE</h2>

                <table class="invoice-info">
                    <tr>
                        <td class="info-label">No. Invoice</td>
                        <td class="info-value">
                            : {{ $sale->invoiceNumber }}
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">Tanggal</td>
                        <td class="info-value">
                            : {{ $sale->transactionDate->format('d/m/Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">Kasir</td>
                        <td class="info-value">
                            : {{ $sale->user->fullName ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">Pembayaran</td>
                        <td class="info-value">
                            : {{ $paymentMethod }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    {{-- Informasi pelanggan --}}
    <table class="customer-table">
        <tr>
            <td style="width: 20%;" class="muted">Kepada</td>
            <td style="width: 80%;" class="bold">
                {{ $customerName }}
            </td>
        </tr>
    </table>

    {{-- Detail produk --}}
    <table class="items-table">
        <thead>
            <tr>
                <th class="product-col text-left">Produk</th>
                <th class="unit-col text-left">Satuan</th>
                <th class="price-col text-right">Harga</th>
                <th class="qty-col text-right">Qty</th>
                <th class="subtotal-col text-right">Subtotal</th>
                <th class="discount-col text-right">Diskon (%)</th>
                <th class="grand-total-col text-right">Total</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($details as $detail)
                <tr>
                    {{-- Produk --}}
                    <td class="product-col text-left">
                        <div class="product-name">
                            {{ $detail->product->name ?? '-' }}
                        </div>

                        @if ($detail->product->code ?? null)
                            <span class="product-code">
                                {{ $detail->product->code }}
                            </span>
                        @endif
                    </td>

                    {{-- Unit --}}
                    <td class="unit-col text-left">
                        {{ $detail->unit->name}} ({{ $detail->unit->code }})
                    </td>

                    {{-- Harga jual per unit --}}
                    <td class="price-col text-right">
                        {{ $formatNumber($detail->sellingPrice) }}
                    </td>

                    {{-- Kuantitas --}}
                    <td class="qty-col text-right">
                        {{ $formatNumber($detail->quantity) }}
                    </td>

                    {{-- Subtotal sebelum diskon --}}
                    <td class="subtotal-col text-right">
                        {{ $formatNumber($detail->subtotal) }}
                    </td>

                    {{-- Persentase diskon --}}
                    <td class="discount-col text-right">
                        {{ $formatNumber($detail->discount) }}%
                    </td>

                    {{-- Total setelah diskon --}}
                    <td class="grand-total-col text-right">
                        {{ $formatNumber($detail->grandTotal) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-row">
                        Tidak ada detail penjualan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Ringkasan transaksi --}}
    <div class="summary-wrapper">
        <table class="summary-table">
            <tr>
                <td class="summary-label">Subtotal</td>
                <td class="summary-value">
                    {{ $formatNumber($sale->totalAmount) }}
                </td>
            </tr>

            <tr>
                <td class="summary-label">Diskon</td>
                <td class="summary-value">
                    {{ $formatNumber($sale->discount) }}
                </td>
            </tr>

            <tr class="grand-total">
                <td class="summary-label">TOTAL</td>
                <td class="summary-value">
                    {{ $formatNumber($sale->grandTotal) }}
                </td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    {{-- Informasi pembayaran --}}
    <table class="payment-table">
        <tr>
            <td>Metode Pembayaran</td>
            <td>{{ $paymentMethod }}</td>
        </tr>
        <tr>
            <td>Jumlah Dibayar</td>
            <td>Rp {{ $formatNumber($sale->paidAmount) }}</td>
        </tr>
        <tr>
            <td>Kembalian</td>
            <td>Rp {{ $formatNumber($sale->changeAmount) }}</td>
        </tr>
    </table>

    @if ($sale->notes)
        <div class="notes">
            <strong>Catatan</strong>
            <p>{{ $sale->notes }}</p>
        </div>
    @endif

    {{-- Area tanda tangan --}}
    <table class="footer-table">
        <tr>
            <td>
                Penerima
                <div class="signature-space"></div>
                <div class="signature-line">(________________)</div>
            </td>
            <td>
                Hormat kami
                <div class="signature-space"></div>
                <div class="signature-line">
                    ({{ $sale->user->fullName ?? 'Kasir' }})
                </div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Dokumen ini merupakan bukti transaksi penjualan.
        <br>
        {{ $store?->storeName ?? config('app.name') }}
    </div>
</body>
</html>
