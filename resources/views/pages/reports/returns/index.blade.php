<x-layouts.auth title="Laporan Pengembalian" :access="$access">
    {{-- Header halaman: judul, deskripsi singkat --}}
    <x-page-header title="Laporan Pengembalian Penjualan" icon="assignment_return">
        <p class="text-body-secondary small mb-0 mt-4">
            Pantau dokumen pengembalian, nilai dan kuantitas barang yang dikembalikan, serta transaksi penjualan asalnya pada periode tertentu.
        </p>
    </x-page-header>

    {{-- Card form filter laporan --}}
    <div class="row gx-7">
        <div class="col-12 mt-7">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center gap-3 py-4">
                    <div class="rounded-3 bg-primary bg-opacity-10 text-body d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width: 2.5rem; height: 2.5rem;">
                        <x-icon class="fs-5">tune</x-icon>
                    </div>

                    <div style="min-width: 0;">
                        <h2 class="fs-5 mb-0">Filter Laporan</h2>
                        <p class="text-body-secondary mt-1 mb-0">Pilih periode lalu saring dokumen pengembalian yang ingin ditampilkan.</p>
                    </div>
                </div>

                <form id="formReport" autocomplete="off">
                    <div class="card-body pb-0">
                        {{-- Periode (wajib) --}}
                        <div class="d-flex align-items-center gap-2 text-body-secondary small fw-semibold text-uppercase mb-4">
                            <x-icon class="fs-6">date_range</x-icon>
                            <span>Periode</span>
                        </div>

                        <div class="row gx-6">
                            <div class="col-12 col-md-6 col-xl-3 mb-5">
                                <x-form-input type="text" name="start_date" id="startDate" label="Tanggal Mulai"
                                    placeholder="Pilih tanggal" value="{{ request('start_date', date('Y-m-01')) }}"
                                    data-flatpickr="" required />
                            </div>

                            <div class="col-12 col-md-6 col-xl-3 mb-5">
                                <x-form-input type="text" name="end_date" id="endDate" label="Tanggal Akhir"
                                    placeholder="Pilih tanggal" value="{{ request('end_date', date('Y-m-t')) }}"
                                    data-flatpickr="" required />
                            </div>
                        </div>

                        <hr class="mt-1 mb-4">

                        {{-- Dokumen (opsional) --}}
                        <div class="d-flex align-items-center gap-2 text-body-secondary small fw-semibold text-uppercase mb-4">
                            <x-icon class="fs-6">receipt_long</x-icon>
                            <span>Dokumen</span>
                        </div>

                        <div class="row gx-6">
                            <div class="col-12 col-md-6 col-xl-3 mb-5">
                                <x-form-input type="text" name="return_number" id="returnNumber" label="Nomor Pengembalian"
                                    placeholder="Cari nomor pengembalian" value="{{ request('return_number') }}" maxlength="50" />
                            </div>

                            <div class="col-12 col-md-6 col-xl-3 mb-5">
                                <x-form-input type="text" name="invoice_number" id="invoiceNumber" label="Invoice Penjualan"
                                    placeholder="Cari nomor invoice" value="{{ request('invoice_number') }}" maxlength="50" />
                            </div>

                            <div class="col-12 col-md-6 col-xl-3 mb-5">
                                <x-form-input type="text" name="customer_name" id="customerName" label="Pelanggan"
                                    placeholder="Cari nama pelanggan" value="{{ request('customer_name') }}" maxlength="100" />
                            </div>

                            <div class="col-12 col-md-6 col-xl-3 mb-5">
                                <x-form-select name="user_id" id="userId" label="Petugas">
                                    <option value="">Semua</option>
                                    @foreach ($cashiers as $cashier)
                                        <option value="{{ $cashier['id'] }}" @selected(request('user_id') === $cashier['id'])>
                                            {{ $cashier['name'] }}
                                        </option>
                                    @endforeach
                                </x-form-select>
                            </div>
                        </div>

                        <hr class="mt-1 mb-4">

                        {{-- Produk (opsional) --}}
                        <div class="d-flex align-items-center gap-2 text-body-secondary small fw-semibold text-uppercase mb-4">
                            <x-icon class="fs-6">inventory_2</x-icon>
                            <span>Produk</span>
                        </div>

                        <div class="row gx-6">
                            <div class="col-12 col-md-6 col-xl-3 mb-5">
                                <x-form-select name="category_id" id="categoryId" label="Kategori Produk">
                                    <option value="">Semua</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(request('category_id') === $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </x-form-select>
                            </div>

                            <div class="col-12 col-md-6 col-xl-3 mb-5">
                                <x-form-input type="text" name="product_code" id="productCode" label="Kode Produk"
                                    placeholder="Masukan kode produk" value="{{ request('product_code') }}" maxlength="100" />
                            </div>
                        </div>
                    </div>

                    {{-- Tombol reset dan terapkan filter berukuran sama --}}
                    <div class="card-footer">
                        <div class="row justify-content-end gx-3 gy-3">
                            <div class="col-12 col-sm-6 col-md-4 col-xl-2 order-2 order-sm-1">
                                <x-button type="button" id="buttonReset" color="light" startIcon="restart_alt" class="border" fullWidth>
                                    Reset
                                </x-button>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4 col-xl-2 order-1 order-sm-2">
                                <x-button type="submit" id="buttonFilter" startIcon="filter_alt" fullWidth>
                                    Terapkan Filter
                                </x-button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Empat kartu ringkasan (KPI) sesuai filter --}}
    <div class="row gx-7">
        @php
            $kpis = [
                ['id' => 'kpiReturns', 'label' => 'Total Pengembalian', 'icon' => 'assignment_return', 'color' => 'primary', 'value' => '0', 'hint' => 'Dokumen pengembalian'],
                ['id' => 'kpiValue', 'label' => 'Nilai Pengembalian', 'icon' => 'payments', 'color' => 'success', 'value' => '0.00', 'hint' => 'Sesuai filter aktif', 'hintId' => 'kpiValueHint'],
                ['id' => 'kpiQuantity', 'label' => 'Qty Dikembalikan', 'icon' => 'inventory_2', 'color' => 'info', 'value' => '0.00', 'hint' => 'Dalam satuan dasar'],
                ['id' => 'kpiProducts', 'label' => 'Jenis Produk', 'icon' => 'category', 'color' => 'primary', 'value' => '0', 'hint' => 'Produk unik dikembalikan'],
            ];
        @endphp

        @foreach ($kpis as $kpi)
            <div class="col-12 col-sm-6 col-xl-3 mt-7">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div style="min-width: 0;">
                                <div class="text-body-secondary small fw-semibold text-uppercase">{{ $kpi['label'] }}</div>
                                <div class="fs-5 fw-bold text-break mt-2" id="{{ $kpi['id'] }}">{{ $kpi['value'] }}</div>
                            </div>

                            <div class="rounded-3 bg-{{ $kpi['color'] }} bg-opacity-10 text-body d-flex align-items-center justify-content-center flex-shrink-0" style="width: 3rem; height: 3rem;">
                                <x-icon class="fs-4">{{ $kpi['icon'] }}</x-icon>
                            </div>
                        </div>

                        <div class="small text-body-secondary mt-2" @isset($kpi['hintId']) id="{{ $kpi['hintId'] }}" @endisset>{{ $kpi['hint'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Analisis: produk dan kategori yang paling banyak dikembalikan --}}
    <div class="row gx-7">
        @foreach ([['id' => 'analysisProducts', 'title' => 'Produk Paling Sering Dikembalikan', 'icon' => 'inventory_2'], ['id' => 'analysisCategories', 'title' => 'Kategori Paling Sering Dikembalikan', 'icon' => 'category']] as $analysis)
            <div class="col-12 col-xl-6 mt-7">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex align-items-center gap-2 py-4">
                        <x-icon class="fs-5">{{ $analysis['icon'] }}</x-icon>
                        <h2 class="fs-5 mb-0">{{ $analysis['title'] }}</h2>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-5">Nama</th>
                                        <th class="text-end">Qty (Dasar)</th>
                                        <th class="text-end pe-5">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody id="{{ $analysis['id'] }}">
                                    <tr><td colspan="3" class="text-center text-body-secondary py-4">Memuat data...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer small text-body-secondary">Peringkat 5 teratas berdasarkan qty satuan dasar.</div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Card tabel laporan --}}
    <div class="row gx-7">
        <div class="col-12 mt-7 mb-7">
            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-12 col-lg-8 mb-5 mb-lg-0">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-8">
                                    <h2 class="fs-5 mb-0" id="tableTitle">Daftar Pengembalian</h2>
                                    <p class="text-body-secondary mt-1 mb-0" id="periodLabel">&nbsp;</p>
                                </div>

                                <div class="col-auto d-flex gap-2">
                                    <x-button
                                        type="link"
                                        id="buttonExportExcel"
                                        href="#"
                                        color="light"
                                        size="sm"
                                        class="border rounded"
                                        title="Export Excel"
                                        aria-label="Export Excel"
                                    >
                                        <x-icon class="fs-6">download</x-icon>
                                    </x-button>

                                    <x-button
                                        type="link"
                                        id="buttonExportPdf"
                                        href="#"
                                        target="_blank"
                                        color="light"
                                        size="sm"
                                        class="border rounded"
                                        title="Cetak PDF"
                                        aria-label="Cetak PDF"
                                    >
                                        <x-icon class="fs-6">picture_as_pdf</x-icon>
                                    </x-button>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <x-view-mode-toggle />
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div id="panelTransactions">
                        <table id="returnReportTable" class="table table-sm table-hover align-middle w-100"></table>
                    </div>

                    {{-- Tabel per produk (dimuat saat mode "Per Produk" dipilih) --}}
                    <div id="panelProducts" class="d-none">
                        <table id="returnProductTable" class="table table-sm table-hover align-middle w-100"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal detail dokumen pengembalian --}}
    <x-modal id="modalDetail" title="Detail Pengembalian" size="xl" fullScreen="md">
        {{-- Informasi header dokumen --}}
        <div class="row gx-6 gy-3 mb-5 small" id="detailInfo"></div>

        <table id="detailTable" class="table table-sm table-hover align-middle w-100"></table>

        {{-- Ringkasan nominal (nilai header sebagai ringkasan resmi) --}}
        <div class="row justify-content-end mt-5">
            <div class="col-12 col-md-6 col-xl-4">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr><td class="text-body-secondary">Total Sebelum Diskon</td><td class="text-end" id="detailSubtotal">0.00</td></tr>
                        <tr><td class="text-body-secondary">Diskon</td><td class="text-end" id="detailDiscount">0.00</td></tr>
                        <tr class="fw-semibold"><td>Nilai Pengembalian</td><td class="text-end" id="detailGrandTotal">0.00</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </x-modal>

    {{-- JavaScript --}}
    <x-slot:script>
        <script>
            "use strict"

            // Error ajax DataTables ditangani sendiri (tidak memakai alert bawaan).
            DataTable.ext.errMode = 'none';

            class Service {
                constructor() {
                    this.modalDetail = new bootstrap.Modal('#modalDetail');
                    this.detailTable = null;
                    this.detailUrl = null;
                    this.form = $('#formReport');
                    this.startDate = $('#startDate');
                    this.endDate = $('#endDate');
                    this.buttonFilter = $('#buttonFilter');
                    this.table = this.#reportDataTableInit();
                    this.mode = 'transactions';
                    this.productTable = null;
                }

                /**
                 * Mengubah karakter HTML dari data server agar tidak dirender sebagai markup.
                 */
                escapeHtml(value) {
                    return $('<div>').text(value ?? '').html();
                }

                /**
                 * Mengubah teks agar aman dipakai pada nilai atribut HTML.
                 */
                attr(value) {
                    return this.escapeHtml(value).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                }

                /**
                 * Nilai filter saat ini dari form (nilai kosong tidak dikirim).
                 */
                filters() {
                    const filters = {
                        start_date: this.startDate.val(),
                        end_date: this.endDate.val(),
                        return_number: $('#returnNumber').val().trim(),
                        invoice_number: $('#invoiceNumber').val().trim(),
                        customer_name: $('#customerName').val().trim(),
                        user_id: $('#userId').val(),
                        category_id: $('#categoryId').val(),
                        product_code: $('#productCode').val().trim(),
                    };

                    Object.keys(filters).forEach((key) => {
                        if (!filters[key]) delete filters[key];
                    });

                    return filters;
                }

                /**
                 * Validasi tanggal di sisi klien (validasi sebenarnya tetap di server).
                 */
                validateDates() {
                    app.clearFormErrors(this.form);

                    const startDate = this.startDate.val();
                    const endDate = this.endDate.val();

                    if (!startDate || !endDate) {
                        app.alert('danger', 'Tanggal mulai dan tanggal akhir wajib diisi.');
                        return false;
                    }

                    if (endDate < startDate) {
                        app.alert('danger', 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.');
                        return false;
                    }

                    return true;
                }

                /**
                 * Inisialisasi DataTables laporan pengembalian (server side).
                 */
                #reportDataTableInit() {
                    const money = (data) => app.numberFormat(Number(data ?? 0));

                    const columns = [
                        {
                            title: 'Tanggal',
                            name: 'sale_returns.transaction_date',
                            data: 'transaction_date',
                            className: 'text-nowrap',
                        },
                        {
                            title: 'No. Pengembalian',
                            name: 'sale_returns.return_number',
                            data: 'return_number',
                            className: 'text-nowrap fw-semibold',
                            render: (data) => this.escapeHtml(data),
                        },
                        {
                            title: 'Invoice Asal',
                            name: 'sales.invoice_number',
                            data: 'invoice_number',
                            className: 'text-nowrap',
                            render: (data) => this.escapeHtml(data ?? '-'),
                        },
                        {
                            // Penjualan tanpa pelanggan ditampilkan sebagai "Umum".
                            title: 'Pelanggan',
                            name: 'customers.name',
                            data: 'customer_name',
                            className: 'text-nowrap',
                            render: (data) => this.escapeHtml(data ?? 'Umum'),
                        },
                        {
                            title: 'Jenis Produk',
                            name: 'sale_returns.total_product',
                            data: 'total_product',
                            className: 'text-nowrap text-end',
                            render: (data) => app.numberFormat(Number(data ?? 0), 0),
                        },
                        {
                            title: 'Qty (Satuan Dasar)',
                            name: 'base_quantity',
                            data: 'base_quantity',
                            className: 'text-nowrap text-end',
                            searchable: false,
                            render: money,
                        },
                        {
                            title: 'Total Harga',
                            name: 'items_subtotal',
                            data: 'items_subtotal',
                            className: 'text-nowrap text-end',
                            searchable: false,
                            render: money,
                        },
                        {
                            title: 'Diskon',
                            name: 'discount_amount',
                            data: 'discount_amount',
                            className: 'text-nowrap text-end',
                            searchable: false,
                            render: money,
                        },
                        {
                            title: 'Nilai Pengembalian',
                            name: 'sale_returns.grand_total',
                            data: 'grand_total',
                            className: 'text-nowrap text-end fw-semibold',
                            render: money,
                        },
                        {
                            title: 'Petugas',
                            name: 'users.full_name',
                            data: 'user_name',
                            className: 'text-nowrap',
                            render: (data) => this.escapeHtml(data ?? '-'),
                        },
                        {
                            title: 'Catatan',
                            name: 'sale_returns.notes',
                            data: 'notes',
                            render: (data) => this.escapeHtml(data ?? '-'),
                        },
                        {
                            title: app.icon('settings', 'fs-6'),
                            name: 'sale_returns.id',
                            data: 'id',
                            className: 'text-nowrap text-center',
                            searchable: false,
                            orderable: false,
                            render: (data, type, row) => {
                                // Data dokumen disimpan pada atribut tombol (kolom tetap dikloning DataTables).
                                const payload = this.attr(JSON.stringify({
                                    id: row.id,
                                    return_number: row.return_number,
                                    transaction_date: row.transaction_date,
                                    invoice_number: row.invoice_number,
                                    sale_date: row.sale_date,
                                    customer_name: row.customer_name,
                                    user_name: row.user_name,
                                    notes: row.notes,
                                    items_subtotal: row.items_subtotal,
                                    discount_amount: row.discount_amount,
                                    grand_total: row.grand_total,
                                }));

                                return `
                                    <button type="button" class="btn btn-light" data-action="detail" data-row="${payload}" title="Lihat detail" aria-label="Lihat detail">
                                        ${app.icon('visibility', 'fs-6')}
                                    </button>
                                `;
                            },
                        },
                    ];

                    return app.dataTable('#returnReportTable', {
                        columns,
                        serverSide: true,
                        // Data awal dimuat oleh apply() (bukan oleh inisialisasi tabel) agar tidak ada request ganda.
                        deferLoading: 0,
                        // Kolom aksi (kolom terakhir) tetap terlihat saat tabel digeser horizontal.
                        fixedColumns: {
                            start: 0,
                            end: 1,
                        },
                        // Empty state yang menjelaskan penyebab dan langkah selanjutnya.
                        language: {
                            search: '',
                            searchPlaceholder: 'Cari...',
                            lengthMenu: '_MENU_',
                            info: '_START_ - _END_ dari _TOTAL_',
                            infoEmpty: '0 - 0 dari 0',
                            infoFiltered: '',
                            zeroRecords: 'Tidak ditemukan pengembalian yang sesuai dengan pencarian.',
                            emptyTable: 'Tidak ada pengembalian pada periode atau filter yang dipilih. Ubah periode atau klik Reset untuk menampilkan semua pengembalian bulan ini.',
                        },
                        ajax: {
                            url: @json(route('reports.returns')),
                            // Kirim filter terbaru pada setiap request tabel.
                            data: (params) => Object.assign(params, this.filters()),
                        },
                        order: [[0, 'desc']],
                    });
                }

                /**
                 * Permintaan ajax GET sederhana (ringkasan dan analisis) dengan filter aktif.
                 */
                #get(url) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'get',
                            url,
                            dataType: 'json',
                            data: this.filters(),
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            success: (response) => resolve(response),
                            error: (xhr) => reject({ ...xhr.responseJSON, status: xhr.status }),
                        });
                    });
                }

                /**
                 * Inisialisasi dataTable laporan per produk (satu baris per detail produk).
                 */
                #productDataTableInit() {
                    const columns = [
                        {
                            title: 'Tanggal',
                            name: 'sale_returns.transaction_date',
                            data: 'transaction_date',
                            className: 'text-nowrap',
                        },
                        {
                            title: 'No. Pengembalian',
                            name: 'sale_returns.return_number',
                            data: 'return_number',
                            className: 'text-nowrap',
                            render: (value) => this.escapeHtml(value ?? '-'),
                        },
                        {
                            title: 'Invoice Asal',
                            name: 'sales.invoice_number',
                            data: 'invoice_number',
                            className: 'text-nowrap',
                            render: (value) => this.escapeHtml(value ?? '-'),
                        },
                        {
                            title: 'Pelanggan',
                            name: 'customers.name',
                            data: 'customer_name',
                            className: 'text-nowrap',
                            render: (value) => this.escapeHtml(value ?? 'Umum'),
                        },
                        {
                            title: 'Kode Produk',
                            name: 'products.code',
                            data: 'product_code',
                            className: 'text-nowrap',
                            render: (value) => this.escapeHtml(value ?? '-'),
                        },
                        {
                            title: 'Nama Produk',
                            name: 'products.name',
                            data: 'product_name',
                            className: '',
                            width: '200px',
                            render: (value) => this.escapeHtml(value ?? '-'),
                        },
                        {
                            title: 'Unit (Satuan)',
                            name: 'units.name',
                            data: 'unit_name',
                            className: 'text-nowrap',
                            render: (value, type, row) => this.escapeHtml(`${row.unit_name ?? '-'} (${row.unit_code ?? '-'})`),
                        },
                        {
                            title: 'Qty',
                            name: 'sale_return_details.quantity',
                            data: 'quantity',
                            className: 'text-nowrap text-end',
                            render: (value) => app.numberFormat(Number(value ?? 0)),
                        },
                        {
                            title: 'Kelipatan Qty',
                            name: 'sale_return_details.multiples_quantity',
                            data: 'multiples_quantity',
                            className: 'text-nowrap text-end',
                            render: (value) => app.numberFormat(Number(value ?? 0)),
                        },
                        {
                            // Total quantity dalam satuan dasar = qty x kelipatan.
                            title: 'Total Qty (Dasar)',
                            name: 'base_quantity',
                            data: 'base_quantity',
                            className: 'text-nowrap text-end',
                            searchable: false,
                            render: (value) => app.numberFormat(Number(value ?? 0)),
                        },
                        {
                            title: 'Subtotal',
                            name: 'sale_return_details.subtotal',
                            data: 'subtotal',
                            className: 'text-nowrap text-end',
                            render: (value) => app.numberFormat(Number(value ?? 0)),
                        },
                        {
                            title: 'Diskon (%)',
                            name: 'sale_return_details.discount',
                            data: 'discount',
                            className: 'text-nowrap text-end',
                            render: (value) => app.numberFormat(Number(value ?? 0)),
                        },
                        {
                            title: 'Grand Total',
                            name: 'sale_return_details.grand_total',
                            data: 'grand_total',
                            className: 'text-nowrap text-end',
                            render: (value) => app.numberFormat(Number(value ?? 0)),
                        },
                    ];

                    return app.dataTable('#returnProductTable', {
                        columns,
                        serverSide: true,
                        ajax: {
                            url: @json(route('reports.returns.products')),
                            // Kirim filter terbaru pada setiap request tabel.
                            data: (params) => Object.assign(params, this.filters()),
                        },
                        order: [[0, 'desc']],
                    });
                }

                /**
                 * Arahkan tombol export ke filter yang sama dengan tabel dan mode tampilan aktif.
                 * Mode per produk hanya tersedia dalam excel (tanpa pdf).
                 */
                updateExportLinks() {
                    const queryString = new URLSearchParams(this.filters()).toString();
                    const excelRoute = this.mode === 'products'
                        ? @json(route('reports.returns.products.export.excel'))
                        : @json(route('reports.returns.export.excel'));

                    $('#buttonExportExcel').attr('href', excelRoute + '?' + queryString);
                    $('#buttonExportPdf').attr('href', @json(route('reports.returns.export.pdf')) + '?' + queryString);
                    $('#buttonExportPdf').toggleClass('d-none', this.mode === 'products');
                }

                /**
                 * Ganti mode tampilan tabel (per transaksi atau per produk).
                 */
                setMode(mode) {
                    if (!this.validateDates()) {
                        // Kembalikan pilihan mode jika tanggal belum valid.
                        $(`input[name="viewMode"][value="${this.mode}"]`).prop('checked', true);
                        return;
                    }

                    this.mode = mode;
                    $('#tableTitle').text(mode === 'products' ? 'Daftar Produk Pengembalian' : 'Daftar Pengembalian');
                    $('#panelTransactions').toggleClass('d-none', mode !== 'transactions');
                    $('#panelProducts').toggleClass('d-none', mode !== 'products');
                    this.updateExportLinks();

                    if (mode === 'products') {
                        // Tabel per produk dibuat saat pertama kali dibutuhkan; selanjutnya cukup dimuat ulang.
                        if (this.productTable === null) {
                            this.productTable = this.#productDataTableInit();
                        } else {
                            this.productTable.ajax.reload();
                        }

                        return;
                    }

                    this.table.ajax.reload();
                }

                /**
                 * Menampilkan nilai KPI pada kartu (nilai kosong menjadi nol).
                 */
                renderSummary(summary) {
                    $('#kpiReturns').text(app.numberFormat(Number(summary?.total_returns ?? 0), 0));
                    $('#kpiValue').text(app.numberFormat(Number(summary?.total_value ?? 0)));
                    $('#kpiQuantity').text(app.numberFormat(Number(summary?.total_quantity ?? 0)));
                    $('#kpiProducts').text(app.numberFormat(Number(summary?.total_products ?? 0), 0));
                    $('#kpiValueHint').text(`Diskon ${app.numberFormat(Number(summary?.total_discount ?? 0))}`);
                }

                /**
                 * Menampilkan peringkat analisis (produk dan kategori).
                 */
                renderAnalysis(analysis) {
                    const render = (selector, rows) => {
                        const html = (rows ?? []).length === 0
                            ? '<tr><td colspan="3" class="text-center text-body-secondary py-4">Tidak ada data.</td></tr>'
                            : rows.map((row) => `
                                <tr>
                                    <td class="ps-5">${this.escapeHtml(row.name)} <span class="text-body-secondary">(${this.escapeHtml(row.code)})</span></td>
                                    <td class="text-end">${app.numberFormat(Number(row.total_quantity ?? 0))}</td>
                                    <td class="text-end pe-5">${app.numberFormat(Number(row.total_value ?? 0))}</td>
                                </tr>
                            `).join('');

                        $(selector).html(html);
                    };

                    render('#analysisProducts', analysis?.products);
                    render('#analysisCategories', analysis?.categories);
                }

                /**
                 * Terapkan filter: perbarui query string, link export, KPI, analisis, dan tabel.
                 */
                async apply() {
                    app.clearAlert();

                    if (!this.validateDates()) return;

                    const filters = this.filters();
                    const queryString = new URLSearchParams(filters).toString();

                    // Perbarui query string URL agar reload halaman memakai filter yang sama.
                    const url = new URL(window.location);
                    url.search = queryString;
                    window.history.pushState({}, '', url);

                    // Keterangan periode pada header tabel.
                    $('#periodLabel').text(`Periode ${this.startDate.val()} s/d ${this.endDate.val()}`);

                    // Arahkan tombol export ke filter yang sama dengan tabel.
                    this.updateExportLinks();

                    this.buttonFilter.prop('disabled', true);
                    app.showPreloader();

                    try {
                        // KPI, analisis, dan tabel memakai filter yang sama.
                        const [summary, analysis] = await Promise.all([
                            this.#get(@json(route('reports.returns.summary'))),
                            this.#get(@json(route('reports.returns.analysis'))),
                            new Promise((resolve) => {
                                // Selesai saat tabel tergambar atau saat terjadi error ajax (agar preloader tidak menggantung).
                                // Muat ulang tabel yang sedang tampil sesuai mode.
                                const activeTable = this.mode === 'products' && this.productTable !== null ? this.productTable : this.table;
                                activeTable.one('draw.dt error.dt', resolve);
                                activeTable.ajax.reload();
                            }),
                        ]);

                        this.renderSummary(summary.data);
                        this.renderAnalysis(analysis.data);
                    } catch (error) {
                        // Error validasi (422): tampilkan pada field terkait.
                        if (error?.status === 422 && error?.errors) {
                            app.setFormErrors(this.form, error);
                            return;
                        }

                        app.alert('danger', 'Terjadi kesalahan saat mengambil data laporan. Silakan coba lagi.');
                    } finally {
                        this.buttonFilter.prop('disabled', false);
                        app.hidePreloader();
                    }
                }

                /**
                 * Kembalikan filter ke nilai awal (bulan berjalan, tanpa filter lain).
                 */
                reset() {
                    const now = new Date();
                    const pad = (value) => String(value).padStart(2, '0');
                    const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
                    const prefix = `${now.getFullYear()}-${pad(now.getMonth() + 1)}`;

                    this.startDate.val(`${prefix}-01`);
                    this.endDate.val(`${prefix}-${pad(lastDay)}`);
                    $('#userId, #categoryId').val('');
                    $('#returnNumber, #invoiceNumber, #customerName, #productCode').val('');

                    this.apply();
                }
            }

            window.service = new Service();

            // Error ajax tabel: tampilkan pesan umum (tanpa detail internal).
            service.table.on('error.dt', function () {
                app.alert('danger', 'Terjadi kesalahan saat mengambil data laporan. Silakan coba lagi.');
            });

            // Terapkan filter.
            service.form.submit(function (e) {
                e.preventDefault();
                service.apply();
            });

            // Reset filter.
            // Pilihan mode tampilan: per transaksi atau per produk.
            $('input[name="viewMode"]').change(function () {
                service.setMode($(this).val());
            });

            $('#buttonReset').click(function (e) {
                e.preventDefault();
                service.reset();
            });

            // Delegasi pada wrapper tabel karena kolom aksi tetap (fixed columns) berupa klon di luar elemen tabel asli.
            $(document).on('click', '#returnReportTable_wrapper button[data-action="detail"]', function () {
                const row = JSON.parse($(this).attr('data-row'));
                const money = (value) => app.numberFormat(Number(value ?? 0));
                const info = [
                    ['No. Pengembalian', row.return_number],
                    ['Tanggal Pengembalian', row.transaction_date],
                    ['Invoice Penjualan Asal', row.invoice_number ?? '-'],
                    ['Tanggal Penjualan Asal', row.sale_date ?? '-'],
                    ['Pelanggan', row.customer_name ?? 'Umum'],
                    ['Petugas', row.user_name ?? '-'],
                    ['Catatan', row.notes ?? '-'],
                ];

                // Informasi header dokumen.
                $('#detailInfo').html(info.map(([label, value]) => `
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="text-body-secondary">${service.escapeHtml(label)}</div>
                        <div class="fw-semibold text-break">${service.escapeHtml(value)}</div>
                    </div>
                `).join(''));

                // Ringkasan nominal dari nilai header dokumen.
                $('#detailSubtotal').text(money(row.items_subtotal));
                $('#detailDiscount').text(money(row.discount_amount));
                $('#detailGrandTotal').text(money(row.grand_total));

                service.detailUrl = app.url(`/reports/returns/${row.id}/details`);
                service.modalDetail.show();
            });

            // Tabel detail dibuat saat modal tampil (agar lebar kolom benar); berikutnya cukup ganti URL sumber data.
            $('#modalDetail').on('shown.bs.modal', function () {
                if (service.detailTable !== null) {
                    service.detailTable.ajax.url(service.detailUrl).load();
                    return;
                }

                const money = (data) => app.numberFormat(Number(data ?? 0));

                service.detailTable = app.modalDataTable('#detailTable', {
                    serverSide: true,
                    scrollX: true,
                    ajax: service.detailUrl,
                    order: [[0, 'asc']],
                    columns: [
                        {
                            title: 'Kode',
                            name: 'products.code',
                            data: 'product_code',
                            className: 'text-nowrap',
                            render: (data) => service.escapeHtml(data),
                        },
                        {
                            title: 'Produk',
                            name: 'products.name',
                            data: 'product_name',
                            render: (data) => service.escapeHtml(data),
                        },
                        {
                            title: 'Satuan',
                            name: 'units.name',
                            data: 'unit_name',
                            className: 'text-nowrap',
                            render: (data, type, row) => `${service.escapeHtml(row.unit_name)} (${service.escapeHtml(row.unit_code)})`,
                        },
                        {
                            title: 'Qty',
                            name: 'sale_return_details.quantity',
                            data: 'quantity',
                            className: 'text-nowrap text-end',
                            render: money,
                        },
                        {
                            title: 'Harga Jual',
                            name: 'sale_return_details.selling_price',
                            data: 'selling_price',
                            className: 'text-nowrap text-end',
                            render: money,
                        },
                        {
                            title: 'Subtotal',
                            name: 'sale_return_details.subtotal',
                            data: 'subtotal',
                            className: 'text-nowrap text-end',
                            render: money,
                        },
                        {
                            title: 'Diskon (%)',
                            name: 'sale_return_details.discount',
                            data: 'discount',
                            className: 'text-nowrap text-end',
                            render: money,
                        },
                        {
                            title: 'Grand Total',
                            name: 'sale_return_details.grand_total',
                            data: 'grand_total',
                            className: 'text-nowrap text-end',
                            render: money,
                        },
                    ],
                });
            });

            // Halaman siap: tampilkan data awal sesuai filter default atau query string.
            $(document).ready(function () {
                service.apply();
            });
        </script>
    </x-slot:script>
</x-layouts.auth>
