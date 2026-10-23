<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Kas</title>
    <style>
        @page { margin: 28px 28px 40px 28px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #222; }
        h1 { font-size: 15px; margin: 0 0 4px 0; }
        .muted { color: #555; }
        .meta { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        .kpi td { border: 1px solid #bbb; padding: 6px 8px; width: 33%; }
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
        $money = fn ($value) => $value === null ? '-' : number_format((float) $value, 2, '.', ',');
        $signed = fn ($value) => $value === null ? '-' : (bccomp((string) $value, '0', 2) > 0 ? '+' : '').number_format((float) $value, 2, '.', ',');
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $titles = ['transactions' => 'Transaksi Kas', 'monthly' => 'Rekap Mutasi Bulanan', 'reconciliation' => 'Rekonsiliasi Bulanan'];
        $net = bcsub((string) $totals['incoming'], (string) $totals['outgoing'], 2);
    @endphp

    <h1>Laporan Kas - {{ $titles[$type] }}</h1>
    <div class="meta">
        <div>Periode: <strong>{{ $filter->startDate->format('d-m-Y') }} s/d {{ $filter->endDate->format('d-m-Y') }}</strong>
            @if ($type === 'transactions') | Jenis: {{ $filter->type?->label() ?? 'Semua' }} @endif</div>
        <div class="muted">Dibuat pada: {{ $generatedAt->format('d-m-Y H:i') }}</div>
    </div>

    {{-- Total kas pada periode filter --}}
    <table class="kpi">
        <tr>
            <td><div class="label">Total Kas Masuk</div><div class="value">{{ $money($totals['incoming']) }}</div></td>
            <td><div class="label">Total Kas Keluar</div><div class="value">{{ $money($totals['outgoing']) }}</div></td>
            <td><div class="label">Mutasi Bersih</div><div class="value">{{ $signed($net) }}</div></td>
        </tr>
    </table>

    @if ($type === 'transactions')
        <table class="data">
            <thead>
                <tr>
                    <th class="center">No</th>
                    <th>Tanggal</th>
                    <th>No. Transaksi</th>
                    <th>Jenis</th>
                    <th class="end">Nominal</th>
                    <th>Catatan</th>
                    <th>Referensi</th>
                    <th>Pencatat</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $index => $row)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->transaction_date)->format('d-m-Y') }}</td>
                        <td>{{ $row->transaction_number }}</td>
                        <td>{{ $row->type === 'INCOMING' ? 'Kas Masuk' : 'Kas Keluar' }}</td>
                        <td class="end">{{ $money($row->amount) }}</td>
                        <td>{{ $row->notes ?? '-' }}</td>
                        <td>{{ $row->reference_number ?? '-' }}</td>
                        <td>{{ $row->user_name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="center muted">Tidak ada transaksi kas pada filter yang dipilih.</td></tr>
                @endforelse
            </tbody>
        </table>
    @else
        <table class="data">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th class="end">Saldo Awal</th>
                    <th class="end">Kas Masuk</th>
                    <th class="end">Kas Keluar</th>
                    <th class="end">Saldo Teoritis</th>
                    @if ($type === 'reconciliation')
                        <th class="end">Saldo Aktual Berikutnya</th>
                        <th class="end">Selisih</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($summaries as $summary)
                    <tr>
                        <td>{{ $months[$summary->month - 1] }} {{ $summary->year }}</td>
                        <td class="end">{{ $money($summary->openingBalance) }}</td>
                        <td class="end">{{ $money($summary->incoming) }}</td>
                        <td class="end">{{ $money($summary->outgoing) }}</td>
                        <td class="end bold">{{ $money($summary->theoreticalClosing) }}</td>
                        @if ($type === 'reconciliation')
                            <td class="end">{{ $money($summary->nextOpeningBalance) }}</td>
                            <td class="end bold">{{ $signed($summary->difference) }}</td>
                            <td>{{ $summary->status->label() }}</td>
                            <td>{{ $summary->notes ?? '-' }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="note">
            Saldo teoritis = saldo awal + kas masuk - kas keluar. Selisih = saldo aktual awal bulan berikutnya - saldo teoritis.
            Tanda "-" berarti data belum dicatat (bukan nol). Rekap menampilkan bulan penuh yang tercakup periode filter.
        </div>
    @endif

    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont('DejaVu Sans', 'normal');
            $pdf->page_text($pdf->get_width() - 110, $pdf->get_height() - 26, 'Halaman {PAGE_NUM} dari {PAGE_COUNT}', $font, 8, [0.3, 0.3, 0.3]);
        }
    </script>
</body>
</html>
