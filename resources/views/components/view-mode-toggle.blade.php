{{-- Pilihan mode tampilan laporan: per transaksi atau per produk (radio ber-id modeTransactions / modeProducts). --}}
<div {{ $attributes->class(['view-mode-toggle', 'd-flex', 'p-1', 'rounded-3', 'border', 'bg-body-tertiary']) }} role="group" aria-label="Mode tampilan">
    <input type="radio" class="btn-check" name="viewMode" id="modeTransactions" value="transactions" autocomplete="off" checked>
    <label class="btn btn-sm border-0 flex-fill d-inline-flex align-items-center justify-content-center gap-2 text-nowrap" for="modeTransactions">
        <x-icon class="fs-6">receipt_long</x-icon>
        <span>Per Transaksi</span>
    </label>

    <input type="radio" class="btn-check" name="viewMode" id="modeProducts" value="products" autocomplete="off">
    <label class="btn btn-sm border-0 flex-fill d-inline-flex align-items-center justify-content-center gap-2 text-nowrap" for="modeProducts">
        <x-icon class="fs-6">inventory_2</x-icon>
        <span>Per Produk</span>
    </label>
</div>

@once
    <style>
        /* Segmented control: pilihan aktif tampil sebagai "kartu" terangkat, pilihan lain redup. */
        .view-mode-toggle .btn {
            color: var(--bs-secondary-color);
            border-radius: var(--bs-border-radius);
            transition: background-color .15s ease, color .15s ease, box-shadow .15s ease;
        }

        .view-mode-toggle .btn:hover {
            color: var(--bs-body-color);
        }

        .view-mode-toggle .btn-check:checked + .btn {
            color: var(--bs-body-color);
            font-weight: 600;
            background-color: var(--bs-body-bg);
            box-shadow: var(--bs-box-shadow-sm);
        }

        .view-mode-toggle .btn-check:focus-visible + .btn {
            outline: 2px solid var(--bs-border-color);
            outline-offset: 1px;
        }
    </style>
@endonce
