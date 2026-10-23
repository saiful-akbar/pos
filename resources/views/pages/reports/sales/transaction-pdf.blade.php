<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Detail Penjualan</title>
    <style>
        @page { margin: 28px 30px 42px 30px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9.5px; color: #222; }
        h1 { font-size: 16px; margin: 0 0 4px 0; }
        h2 { font-size: 11px; margin: 14px 0 6px 0; }
        .muted { color: #555; }
        table { width: 100%; border-collapse: collapse; }
        .info td { padding: 2px 0; vertical-align: top; }
        .info .label { width: 100px; color: #555; }
        .data { margin-top: 4px; }
        .data th, .data td { border: 1px solid #bbb; padding: 4px 5px; }
        .data th { background: #eee; text-align: left; }
        .data thead { display: table-header-group; }
        .data tr { page-break-inside: avoid; }
        .end { text-align: right; }
        .center { text-align: center; }
        .total td { font-weight: bold; background: #f5f5f5; }
        .summary { width: 46%; margin-left: 54%; margin-top: 10px; }
        .summary td { padding: 3px 6px; border-bottom: 1px solid #ddd; }
        .summary .grand td { font-weight: bold; font-size: 11px; border-top: 1px solid #222; border-bottom: 0; }
    </style>
</head>
<body>
    @php
        $sale = $transaction->sale;
        $methodLabels = ['cash' => 'Tunai', 'qris' => 'QRIS', 'debit' => 'Debit', 'credit' => 'Kredit'];
        $money = fn ($value) => number_format((float) $value, 2, '.', ',');
    @endphp

    <h1>Detail Penjualan</h1>
    <div class="muted">Dibuat pada: {{ $generatedAt->format('d-m-Y H:i') }}</div>

    {{-- Informasi transaksi --}}
    <table class="info" style="margin-top: 8px;">
        <tr>
            <td class="label">Nomor Invoice</td><td>: {{ $sale->invoiceNumber }}</td>
            <td class="label">Metode Pembayaran</td><td>: {{ $methodLabels[$sale->paymentMethod] ?? $sale->paymentMethod }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td><td>: {{ $sale->transactionDate->format('d-m-Y') }}</td>
            <td class="label">Kasir</td><td>: {{ $sale->user->fullName }}</td>
        </tr>
        <tr>
            <td class="label">Pelanggan</td><td>: {{ $sale->customer?->name ?? 'Umum' }}</td>
            <td class="label">Catatan</td><td>: {{ $sale->notes ?? '-' }}</td>
        </tr>
    </table>

    {{-- Daftar produk yang terjual --}}
    <h2>Daftar Produk</h2>
    <table class="data">
        <thead>
            <tr>
                <th class="center">No</th>
                <th>Kode</th>
                <th>Produk</th>
                <th>Unit</th>
                <th class="end">Harga Jual</th>
                <th class="end">Qty</th>
                <th class="end">Kelipatan</th>
                <th class="end">Total Qty (Dasar)</th>
                <th class="end">Subtotal</th>
                <th class="end">Diskon (%)</th>
                <th class="end">Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaction->details as $index => $detail)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $detail->product->code }}</td>
                    <td>{{ $detail->product->name }}</td>
                    <td>{{ $detail->unit->name }} ({{ $detail->unit->code }})</td>
                    <td class="end">{{ $money($detail->sellingPrice) }}</td>
                    <td class="end">{{ $money($detail->quantity) }}</td>
                    <td class="end">{{ $money($detail->multiplesQuantity) }}</td>
                    <td class="end">{{ $money($detail->quantity * $detail->multiplesQuantity) }}</td>
                    <td class="end">{{ $money($detail->subtotal) }}</td>
                    <td class="end">{{ $money($detail->discount) }}</td>
                    <td class="end">{{ $money($detail->grandTotal) }}</td>
                </tr>
            @empty
                <tr><td colspan="11" class="center muted">Tidak ada detail produk.</td></tr>
            @endforelse

            <tr class="total">
                <td colspan="7" class="end">Total ({{ number_format($sale->totalProduct, 0) }} produk)</td>
                <td class="end">{{ $money($transaction->baseQuantity()) }}</td>
                <td class="end">{{ $money($transaction->itemsSubtotal()) }}</td>
                <td></td>
                <td class="end">{{ $money($sale->totalAmount) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Ringkasan penjualan --}}
    <table class="summary">
        <tr><td>Total Harga (sebelum diskon)</td><td class="end">{{ $money($transaction->itemsSubtotal()) }}</td></tr>
        <tr><td>Diskon Item</td><td class="end">{{ $money($transaction->itemDiscount()) }}</td></tr>
        <tr><td>Diskon Pelanggan ({{ rtrim(rtrim(number_format($sale->discount, 2, '.', ''), '0'), '.') }}%)</td><td class="end">{{ $money($transaction->customerDiscount()) }}</td></tr>
        <tr class="grand"><td>Grand Total</td><td class="end">{{ $money($sale->grandTotal) }}</td></tr>
        <tr><td>Dibayar</td><td class="end">{{ $money($sale->paidAmount) }}</td></tr>
        <tr><td>Kembalian</td><td class="end">{{ $money($sale->changeAmount) }}</td></tr>
    </table>

    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont('DejaVu Sans', 'normal');
            $pdf->page_text($pdf->get_width() - 120, $pdf->get_height() - 28, 'Halaman {PAGE_NUM} dari {PAGE_COUNT}', $font, 8, [0.3, 0.3, 0.3]);
        }
    </script>
</body>
</html>
