<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        @page { margin: 28px 28px 40px 28px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #222; }
        h1 { font-size: 15px; margin: 0 0 4px 0; }
        .muted { color: #555; }
        .meta { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        .kpi td { border: 1px solid #bbb; padding: 6px 8px; width: 25%; }
        .kpi .label { font-size: 8px; color: #555; }
        .kpi .value { font-size: 12px; font-weight: bold; }
        .data { margin-top: 12px; }
        .data th, .data td { border: 1px solid #bbb; padding: 4px 5px; }
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
        $methodLabels = ['cash' => 'Tunai', 'qris' => 'QRIS', 'debit' => 'Debit', 'credit' => 'Kredit'];
        $money = fn ($value) => number_format((float) $value, 2, '.', ',');
    @endphp

    {{-- Judul, periode, filter, dan waktu pembuatan laporan --}}
    <h1>Laporan Penjualan</h1>
    <div class="meta">
        <div>Periode: <strong>{{ $filter->startDate->format('d-m-Y') }} s/d {{ $filter->endDate->format('d-m-Y') }}</strong></div>
        <div class="muted">
            Filter:
            @forelse ($filterLabels as $label => $value)
                {{ $label }}: {{ $value }}@if (! $loop->last) | @endif
            @empty
                Semua
            @endforelse
        </div>
        <div class="muted">Dibuat pada: {{ $generatedAt->format('d-m-Y H:i') }}</div>
    </div>

    {{-- Ringkasan KPI (agregasi dari query yang sama dengan tabel) --}}
    <table class="kpi">
        <tr>
            <td><div class="label">Total Transaksi</div><div class="value">{{ number_format($summary->totalTransactions) }}</div></td>
            <td><div class="label">Total Penjualan</div><div class="value">{{ $money($summary->totalSales) }}</div></td>
            <td><div class="label">Total Produk Terjual (satuan dasar)</div><div class="value">{{ $money($summary->totalProductsSold) }}</div></td>
            <td><div class="label">Total Diskon</div><div class="value">{{ $money($summary->totalDiscount) }}</div></td>
        </tr>
    </table>

    {{-- Tabel transaksi sesuai filter --}}
    <table class="data">
        <thead>
            <tr>
                <th class="center">No</th>
                <th>Tanggal</th>
                <th>Invoice</th>
                <th>Pelanggan</th>
                <th class="end">Produk</th>
                <th class="end">Qty (Dasar)</th>
                <th class="end">Total Harga</th>
                <th class="end">Diskon</th>
                <th class="end">Grand Total</th>
                <th class="end">Dibayar</th>
                <th class="end">Kembalian</th>
                <th>Metode</th>
                <th>Kasir</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $index => $row)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->transaction_date)->format('d-m-Y') }}</td>
                    <td>{{ $row->invoice_number }}</td>
                    <td>{{ $row->customer_name ?? 'Umum' }}</td>
                    <td class="end">{{ number_format((float) $row->total_product, 0) }}</td>
                    <td class="end">{{ $money($row->base_quantity) }}</td>
                    <td class="end">{{ $money($row->items_subtotal) }}</td>
                    <td class="end">{{ $money($row->discount_amount) }}</td>
                    <td class="end">{{ $money($row->grand_total) }}</td>
                    <td class="end">{{ $money($row->paid_amount) }}</td>
                    <td class="end">{{ $money($row->change_amount) }}</td>
                    <td>{{ $methodLabels[$row->payment_method] ?? $row->payment_method }}</td>
                    <td>{{ $row->cashier_name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="center muted">Tidak ada data penjualan pada filter yang dipilih.</td>
                </tr>
            @endforelse

            {{-- Total keseluruhan sesuai filter --}}
            <tr class="total">
                <td colspan="5" class="end">Total</td>
                <td class="end">{{ $money($summary->totalProductsSold) }}</td>
                <td></td>
                <td class="end">{{ $money($summary->totalDiscount) }}</td>
                <td class="end">{{ $money($summary->totalSales) }}</td>
                <td colspan="4"></td>
            </tr>
        </tbody>
    </table>

    {{-- Nomor halaman --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont('DejaVu Sans', 'normal');
            $pdf->page_text($pdf->get_width() - 110, $pdf->get_height() - 26, 'Halaman {PAGE_NUM} dari {PAGE_COUNT}', $font, 8, [0.3, 0.3, 0.3]);
        }
    </script>
</body>
</html>
