<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Stok</title>
    <style>
        @page { margin: 28px 28px 40px 28px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #222; }
        h1 { font-size: 15px; margin: 0 0 4px 0; }
        .muted { color: #555; }
        .meta { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        .kpi td { border: 1px solid #bbb; padding: 6px 8px; width: 20%; }
        .kpi .label { font-size: 8px; color: #555; }
        .kpi .value { font-size: 12px; font-weight: bold; }
        .data { margin-top: 12px; }
        .data th, .data td { border: 1px solid #bbb; padding: 4px 5px; }
        .data th { background: #eee; text-align: left; }
        .data thead { display: table-header-group; }
        .data tr { page-break-inside: avoid; }
        .end { text-align: right; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .note { margin-top: 8px; font-size: 8px; color: #555; }
    </style>
</head>
<body>
    @php
        $statusLabels = ['match' => 'Sesuai', 'negative' => 'Selisih Kurang', 'positive' => 'Selisih Lebih', 'unavailable' => 'Belum Direkonsiliasi'];
        $money = fn ($value) => number_format((float) $value, 2, '.', ',');
        $signed = fn ($value) => ((float) $value > 0 ? '+' : '').number_format((float) $value, 2, '.', ',');
    @endphp

    <h1>Laporan Stok / Inventory</h1>
    <div class="meta">
        <div>Periode: <strong>{{ sprintf('%02d-%04d', $filter->month, $filter->year) }}</strong>
            | Stok akhir aktual: snapshot stok awal {{ sprintf('%02d-%04d', $filter->nextMonth(), $filter->nextYear()) }}</div>
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

    @unless ($summary->openingAvailable && $summary->closingAvailable)
        <div class="note" style="color:#b00020; font-size:9px;">
            Snapshot {{ ! $summary->openingAvailable ? 'stok awal periode' : '' }}{{ ! $summary->openingAvailable && ! $summary->closingAvailable ? ' dan ' : '' }}{{ ! $summary->closingAvailable ? 'stok akhir (stok awal bulan berikutnya)' : '' }} belum tersedia, sehingga produk belum dapat direkonsiliasi.
        </div>
    @endunless

    {{-- Sebaran status rekonsiliasi (jumlah produk) --}}
    <table class="kpi" style="margin-top: 6px;">
        <tr>
            <td><div class="label">Produk Dilaporkan</div><div class="value">{{ number_format($summary->totalProducts) }}</div></td>
            <td><div class="label">Sesuai</div><div class="value">{{ number_format($summary->matched) }}</div></td>
            <td><div class="label">Selisih Kurang</div><div class="value">{{ number_format($summary->negative) }}</div></td>
            <td><div class="label">Selisih Lebih</div><div class="value">{{ number_format($summary->positive) }}</div></td>
            <td><div class="label">Belum Direkonsiliasi</div><div class="value">{{ number_format($summary->unavailable) }}</div></td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th class="center">No</th>
                <th>Kode</th>
                <th>Produk</th>
                <th>Satuan</th>
                <th class="end">Stok Awal</th>
                <th class="end">Pembelian</th>
                <th class="end">Pengembalian</th>
                <th class="end">Penjualan</th>
                <th class="end">Book Balance</th>
                <th class="end">Stok Akhir</th>
                <th class="end">Selisih</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $index => $row)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $row->code }}</td>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->base_unit ?? '-' }}</td>
                    <td class="end">{{ $money($row->opening) }}</td>
                    <td class="end">{{ $money($row->purchase_qty) }}</td>
                    <td class="end">{{ $money($row->return_qty) }}</td>
                    <td class="end">{{ $money($row->sale_qty) }}</td>
                    <td class="end">{{ $money($row->book_balance) }}</td>
                    <td class="end">{{ $row->closing === null ? '-' : $money($row->closing) }}</td>
                    <td class="end bold">{{ $row->difference === null ? '-' : $signed($row->difference) }}</td>
                    <td>{{ $statusLabels[$row->status] ?? $row->status }}</td>
                </tr>
            @empty
                <tr><td colspan="12" class="center muted">Tidak ada produk pada periode atau filter yang dipilih.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="note">
        Semua kuantitas dalam satuan dasar (quantity x kelipatan). Book Balance = Stok Awal + Pembelian + Pengembalian Penjualan - Penjualan.
        Selisih = Stok Akhir Aktual - Book Balance.
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont('DejaVu Sans', 'normal');
            $pdf->page_text($pdf->get_width() - 110, $pdf->get_height() - 26, 'Halaman {PAGE_NUM} dari {PAGE_COUNT}', $font, 8, [0.3, 0.3, 0.3]);
        }
    </script>
</body>
</html>
