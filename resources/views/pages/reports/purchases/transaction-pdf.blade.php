<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Detail Pembelian</title>
    <style>
        @page { margin: 30px 32px 44px 32px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9.5px; color: #222; }
        h1 { font-size: 16px; margin: 0 0 6px 0; }
        h2 { font-size: 11px; margin: 16px 0 6px 0; }
        .muted { color: #555; }
        table { width: 100%; border-collapse: collapse; }
        .info td { padding: 2px 0; vertical-align: top; }
        .info .label { width: 110px; color: #555; }
        .data { margin-top: 4px; }
        .data th, .data td { border: 1px solid #bbb; padding: 4px 6px; }
        .data th { background: #eee; text-align: left; }
        .data thead { display: table-header-group; }
        .data tr { page-break-inside: avoid; }
        .end { text-align: right; }
        .center { text-align: center; }
        .total td { font-weight: bold; background: #f5f5f5; }
    </style>
</head>
<body>
    @php
        $purchase = $transaction->purchase;
        $statusLabels = ['unpaid' => 'Belum Dibayar', 'partial' => 'Dibayar Sebagian', 'paid' => 'Lunas'];
        $methodLabels = ['cash' => 'Tunai', 'transfer' => 'Transfer', 'credit' => 'Kredit', 'debit' => 'Debit'];
        $money = fn ($value) => number_format((float) $value, 2, '.', ',');
    @endphp

    <h1>Detail Transaksi Pembelian</h1>
    <div class="muted">Dibuat pada: {{ $generatedAt->format('d-m-Y H:i') }}</div>

    {{-- Informasi transaksi --}}
    <table class="info" style="margin-top: 8px;">
        <tr><td class="label">Tanggal</td><td>: {{ $purchase->transactionDate->format('d-m-Y') }}</td><td class="label">Total Pembelian</td><td>: {{ $money($purchase->totalPrice) }}</td></tr>
        <tr><td class="label">Nomor Faktur</td><td>: {{ $purchase->invoiceNumber ?? '-' }}</td><td class="label">Sudah Dibayar</td><td>: {{ $money($purchase->alreadyPaid) }}</td></tr>
        <tr><td class="label">Pemasok</td><td>: {{ $purchase->supplier->name }}</td><td class="label">Sisa Utang</td><td>: {{ $money($transaction->outstanding()) }}</td></tr>
        <tr><td class="label">Petugas</td><td>: {{ $purchase->user->fullName }}</td><td class="label">Status</td><td>: {{ $statusLabels[$purchase->paymentStatus->value] ?? $purchase->paymentStatus->value }}</td></tr>
        <tr><td class="label">Catatan</td><td colspan="3">: {{ $purchase->notes ?? '-' }}</td></tr>
    </table>

    {{-- Daftar produk yang dibeli --}}
    <h2>Daftar Produk</h2>
    <table class="data">
        <thead>
            <tr>
                <th class="center">No</th>
                <th>Produk</th>
                <th>Unit</th>
                <th class="end">Qty</th>
                <th class="end">Harga</th>
                <th class="end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaction->details as $index => $detail)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $detail->product->name }} ({{ $detail->product->code }})</td>
                    <td>{{ $detail->unit->name }} ({{ $detail->unit->code }})</td>
                    <td class="end">{{ $money($detail->quantity) }}</td>
                    <td class="end">{{ $money($detail->price) }}</td>
                    <td class="end">{{ $money($detail->subtotal) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="center muted">Tidak ada detail produk.</td></tr>
            @endforelse

            <tr class="total">
                <td colspan="3" class="end">Total ({{ number_format($purchase->totalProduct, 0) }} produk)</td>
                <td class="end">{{ $money($purchase->totalQuantity) }}</td>
                <td></td>
                <td class="end">{{ $money($purchase->totalPrice) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Daftar pembayaran yang sudah dilakukan --}}
    <h2>Daftar Pembayaran</h2>
    <table class="data">
        <thead>
            <tr>
                <th class="center">No</th>
                <th>Tanggal</th>
                <th>Metode</th>
                <th class="end">Nominal</th>
                <th>Petugas</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaction->payments as $index => $payment)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $payment->paymentDate->format('d-m-Y') }}</td>
                    <td>{{ $methodLabels[$payment->paymentMethod->value] ?? $payment->paymentMethod->value }}</td>
                    <td class="end">{{ $money($payment->amount) }}</td>
                    <td>{{ $payment->user->fullName }}</td>
                    <td>{{ $payment->notes ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="center muted">Belum ada pembayaran.</td></tr>
            @endforelse

            @if (count($transaction->payments) > 0)
                <tr class="total">
                    <td colspan="3" class="end">Total Pembayaran</td>
                    <td class="end">{{ $money($purchase->alreadyPaid) }}</td>
                    <td colspan="2"></td>
                </tr>
            @endif
        </tbody>
    </table>

    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont('DejaVu Sans', 'normal');
            $pdf->page_text($pdf->get_width() - 120, $pdf->get_height() - 28, 'Halaman {PAGE_NUM} dari {PAGE_COUNT}', $font, 8, [0.3, 0.3, 0.3]);
        }
    </script>
</body>
</html>
