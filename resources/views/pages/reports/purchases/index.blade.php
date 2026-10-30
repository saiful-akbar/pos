<x-layouts.auth title="Laporan Pembelian" :access="$access">
    {{-- Header halaman: judul dan ikon --}}
    <x-page-header title="Laporan Pembelian" icon="receipt_long" />

    {{-- Card form filter laporan --}}
    <div class="row gx-7">
        <div class="col-12 mt-7">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center gap-3 py-4">
                    <div class="rounded-3 bg-primary bg-opacity-10 text-body d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.5rem; height: 2.5rem;">
                        <x-icon class="fs-5">tune</x-icon>
                    </div>

                    <div>
                        <h2 class="fs-5 mb-0">Filter Laporan</h2>
                        <p class="text-body-secondary mt-1 mb-0">Pilih periode dan saring data pembelian yang ditampilkan.</p>
                    </div>
                </div>

                <form id="formReport" autocomplete="off">
                    <div class="card-body">
                        {{-- Periode (wajib) --}}
                        <div class="d-flex align-items-center gap-2 text-body-secondary small fw-semibold text-uppercase mb-4">
                            <x-icon class="fs-6">date_range</x-icon>
                            <span>Periode</span>
                        </div>

                        <div class="row gx-7">
                            {{-- Tanggal mulai (default awal bulan berjalan) --}}
                            <div class="col-12 col-md-6 mb-5">
                                <x-form-input type="text" name="start_date" id="startDate" label="Tanggal Mulai"
                                    placeholder="Pilih tanggal" value="{{ request('start_date', date('Y-m-01')) }}"
                                    data-flatpickr="" required />
                            </div>

                            {{-- Tanggal akhir (default akhir bulan berjalan) --}}
                            <div class="col-12 col-md-6 mb-5">
                                <x-form-input type="text" name="end_date" id="endDate" label="Tanggal Akhir"
                                    placeholder="Pilih tanggal" value="{{ request('end_date', date('Y-m-t')) }}"
                                    data-flatpickr="" required />
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Penyaring tambahan (opsional) --}}
                        <div class="d-flex align-items-center gap-2 text-body-secondary small fw-semibold text-uppercase mb-4">
                            <x-icon class="fs-6">filter_list</x-icon>
                            <span>Penyaring (opsional)</span>
                        </div>

                        <div class="row gx-7">
                            {{-- Pemasok --}}
                            <div class="col-12 col-md-4 mb-5 mb-md-0">
                                <x-form-select name="supplier_id" id="supplierId" label="Pemasok">
                                    <option value="">Semua</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" @selected(request('supplier_id') === $supplier->id)>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </x-form-select>
                            </div>

                            {{-- Status pembayaran --}}
                            <div class="col-12 col-md-4 mb-5 mb-md-0">
                                <x-form-select name="payment_status" id="paymentStatus" label="Status Pembayaran">
                                    <option value="">Semua</option>
                                    <option value="unpaid" @selected(request('payment_status') === 'unpaid')>Belum Dibayar</option>
                                    <option value="partial" @selected(request('payment_status') === 'partial')>Dibayar Sebagian</option>
                                    <option value="paid" @selected(request('payment_status') === 'paid')>Lunas</option>
                                </x-form-select>
                            </div>

                            {{-- Nomor faktur --}}
                            <div class="col-12 col-md-4">
                                <x-form-input type="text" name="invoice_number" id="invoiceNumber" label="Nomor Faktur"
                                    placeholder="Cari nomor faktur" value="{{ request('invoice_number') }}" maxlength="100" />
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="w-100">
                            <div class="row align-items-center justify-content-end">
                                <div class="col-6 col-lg-4 col-xl-2">
                                    <x-button type="button" id="buttonReset" color="light" startIcon="restart_alt" class="border" fullWidth>
                                        Reset
                                    </x-button>
                                </div>
    
                                <div class="col-6 col-lg-4 col-xl-2">
                                    <x-button type="submit" id="buttonFilter" startIcon="filter_alt" fullWidth>
                                        Terapkan Filter
                                    </x-button>
                                </div>
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
                ['id' => 'kpiTransactions', 'label' => 'Total Transaksi', 'icon' => 'receipt_long', 'color' => 'primary', 'value' => '0'],
                ['id' => 'kpiPurchase', 'label' => 'Total Pembelian', 'icon' => 'shopping_cart', 'color' => 'info', 'value' => '0.00'],
                ['id' => 'kpiPaid', 'label' => 'Total Sudah Dibayar', 'icon' => 'payments', 'color' => 'success', 'value' => '0.00'],
                ['id' => 'kpiOutstanding', 'label' => 'Total Sisa Utang', 'icon' => 'account_balance_wallet', 'color' => 'danger', 'value' => '0.00'],
            ];
        @endphp

        @foreach ($kpis as $kpi)
            <div class="col-12 col-sm-6 col-xl-3 mt-7">
                <div class="card shadow-sm h-100 border-start border-3 border-{{ $kpi['color'] }} border-opacity-25">
                    <div class="card-body d-flex align-items-center gap-4">
                        <div class="rounded-3 bg-{{ $kpi['color'] }} bg-opacity-10 text-body d-flex align-items-center justify-content-center flex-shrink-0" style="width: 3.25rem; height: 3.25rem;">
                            <x-icon class="fs-3">{{ $kpi['icon'] }}</x-icon>
                        </div>

                        <div style="min-width: 0;">
                            <div class="text-body-secondary small">{{ $kpi['label'] }}</div>
                            <div class="fs-5 fw-semibold text-break" id="{{ $kpi['id'] }}">{{ $kpi['value'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Card tabel laporan beserta tombol export --}}
    <div class="row gx-7">
        <div class="col-12 mt-7">
            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-12 col-lg-8 mb-5 mb-lg-0">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-8">
                                    <h2 class="fs-5 mb-0" id="tableTitle">Daftar Pembelian</h2>
                                </div>

                                <div class="col-auto d-flex gap-2">
                                    <x-button type="link" id="buttonExportExcel" href="#" color="light" size="sm"
                                        class="border rounded" title="Export Excel" aria-label="Export Excel">
                                        <x-icon class="fs-6">download</x-icon>
                                    </x-button>
            
                                    <x-button type="link" id="buttonExportPdf" href="#" target="_blank" color="light" size="sm"
                                        class="border rounded" title="Cetak PDF" aria-label="Cetak PDF">
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
                        <table id="purchaseReportTable" class="table table-sm table-hover align-middle w-100"></table>
                    </div>

                    {{-- Tabel per produk (dimuat saat mode "Per Produk" dipilih) --}}
                    <div id="panelProducts" class="d-none">
                        <table id="purchaseProductTable" class="table table-sm table-hover align-middle w-100"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal detail produk transaksi (export excel dan cetak pdf pada header) --}}
    <x-modal id="modalDetail" title="Detail Pembelian" size="xl" fullScreen="md">
        <x-slot:headerAction>
            <div class="d-flex align-items-center gap-3 ms-auto ps-3">
                <span id="detailSummary" class="badge border fw-normal text-wrap d-none d-md-inline"></span>

                <div class="d-flex gap-2">
                    <x-button type="link" id="buttonDetailExcel" href="#" color="light" size="sm"
                        class="border rounded" title="Export Excel" aria-label="Export Excel">
                        <x-icon class="fs-6">download</x-icon>
                    </x-button>

                    <x-button type="link" id="buttonDetailPdf" href="#" target="_blank" color="light" size="sm"
                        class="border rounded" title="Cetak PDF" aria-label="Cetak PDF">
                        <x-icon class="fs-6">picture_as_pdf</x-icon>
                    </x-button>
                </div>
            </div>
        </x-slot:headerAction>

        <table id="detailTable" class="table table-sm table-hover align-middle w-100"></table>
    </x-modal>

    {{-- Modal daftar pembayaran yang sudah dilakukan --}}
    <x-modal id="modalPayments" title="Daftar Pembayaran" size="lg" fullScreen="md">
        <x-slot:headerAction>
            <div class="d-flex align-items-center gap-3 ms-auto ps-3">
                <span id="paymentsSummary" class="badge border fw-normal text-wrap d-none d-md-inline"></span>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        </x-slot:headerAction>

        <table id="paymentsTable" class="table table-sm table-hover align-middle w-100"></table>
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
                    this.modalPayments = new bootstrap.Modal('#modalPayments');
                    this.detailTable = null;
                    this.paymentsTable = null;
                    this.purchaseId = null;
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
                        supplier_id: $('#supplierId').val(),
                        payment_status: $('#paymentStatus').val(),
                        invoice_number: $('#invoiceNumber').val().trim(),
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
                 * Label dan warna badge status pembayaran.
                 */
                #statusBadge(status) {
                    const map = {
                        unpaid: ['Belum Dibayar', 'text-bg-danger'],
                        partial: ['Dibayar Sebagian', 'border text-body-secondary'],
                        paid: ['Lunas', 'text-bg-success'],
                    };
                    const [label, color] = map[status] ?? [this.escapeHtml(status), 'text-bg-secondary'];

                    return `<span class="badge ${color}">${label}</span>`;
                }

                /**
                 * Inisialisasi DataTables laporan pembelian (server side).
                 */
                #reportDataTableInit() {
                    const columns = [
                        {
                            title: 'Tanggal',
                            name: 'purchases.transaction_date',
                            data: 'transaction_date',
                            className: 'text-nowrap',
                        },
                        {
                            title: 'Nomor Faktur',
                            name: 'purchases.invoice_number',
                            data: 'invoice_number',
                            className: 'text-nowrap',
                            render: (data) => this.escapeHtml(data ?? '-'),
                        },
                        {
                            title: 'Pemasok',
                            name: 'suppliers.name',
                            data: 'supplier_name',
                            className: 'text-nowrap',
                            render: (data) => this.escapeHtml(data ?? '-'),
                        },
                        {
                            title: 'Total Produk',
                            name: 'purchases.total_product',
                            data: 'total_product',
                            className: 'text-nowrap text-end',
                            render: (data) => app.numberFormat(Number(data ?? 0), 0),
                        },
                        {
                            title: 'Total Qty',
                            name: 'purchases.total_quantity',
                            data: 'total_quantity',
                            className: 'text-nowrap text-end',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                        {
                            title: 'Total Pembelian',
                            name: 'purchases.total_price',
                            data: 'total_price',
                            className: 'text-nowrap text-end',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                        {
                            title: 'Sudah Dibayar',
                            name: 'purchases.already_paid',
                            data: 'already_paid',
                            className: 'text-nowrap text-end',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                        {
                            // Pembayaran yang melebihi total pembelian ditampilkan 0 (nilai dari server sudah dibatasi minimal 0).
                            title: 'Sisa Utang',
                            name: 'outstanding',
                            data: 'outstanding',
                            className: 'text-nowrap text-end',
                            searchable: false,
                            render: (data) => app.numberFormat(Math.max(Number(data ?? 0), 0)),
                        },
                        {
                            title: 'Status',
                            name: 'purchases.payment_status',
                            data: 'payment_status',
                            className: 'text-nowrap',
                            render: (data) => this.#statusBadge(data),
                        },
                        {
                            title: 'Petugas',
                            name: 'users.full_name',
                            data: 'user_name',
                            className: 'text-nowrap',
                            render: (data) => this.escapeHtml(data ?? '-'),
                        },
                        {
                            title: app.icon('settings', 'fs-6'),
                            name: 'purchases.id',
                            data: 'id',
                            className: 'text-nowrap text-center',
                            searchable: false,
                            orderable: false,
                            render: (data, type, row) => {
                                const id = this.escapeHtml(data);

                                // Ringkasan transaksi disimpan pada atribut tombol (kolom tetap dikloning DataTables,
                                // sehingga data baris tidak diambil dari elemen tabel asli).
                                const summary = this.attr(`${row.invoice_number ?? '-'} • ${row.supplier_name ?? '-'}`);

                                // Tombol lihat detail produk dan lihat daftar pembayaran (modal).
                                return `
                                    <button type="button" class="btn btn-sm btn-light rounded" data-action="detail" data-id="${id}" data-summary="${summary}" title="Lihat detail" aria-label="Lihat detail">
                                        ${app.icon('visibility', 'fs-6')}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light rounded" data-action="payments" data-id="${id}" data-summary="${summary}" title="Lihat pembayaran" aria-label="Lihat pembayaran">
                                        ${app.icon('payments', 'fs-6')}
                                    </button>
                                `;
                            },
                        },
                    ];

                    return app.dataTable('#purchaseReportTable', {
                        columns,
                        serverSide: true,
                        // Data awal dimuat oleh apply() (bukan oleh inisialisasi tabel) agar tidak ada request ganda.
                        deferLoading: 0,
                        // Kolom aksi (kolom terakhir) tetap terlihat saat tabel digeser horizontal.
                        fixedColumns: {
                            start: 0,
                            end: 1,
                        },
                        ajax: {
                            url: @json(route('reports.purchases')),
                            // Kirim filter terbaru pada setiap request tabel.
                            data: (params) => Object.assign(params, this.filters()),
                        },
                        order: [[0, 'desc']],
                    });
                }

                /**
                 * Inisialisasi dataTable laporan per produk (satu baris per detail produk).
                 */
                #productDataTableInit() {
                    const columns = [
                    {
                        title: 'Tanggal',
                        name: 'purchases.transaction_date',
                        data: 'transaction_date',
                        className: 'text-nowrap',
                    },
                    {
                        title: 'Nomor Faktur',
                        name: 'purchases.invoice_number',
                        data: 'invoice_number',
                        className: 'text-nowrap',
                        render: (value) => this.escapeHtml(value ?? '-'),
                    },
                    {
                        title: 'Pemasok',
                        name: 'suppliers.name',
                        data: 'supplier_name',
                        className: 'text-nowrap',
                        render: (value) => this.escapeHtml(value ?? '-'),
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
                        name: 'purchase_details.quantity',
                        data: 'quantity',
                        className: 'text-nowrap text-end',
                        render: (value) => app.numberFormat(Number(value ?? 0)),
                    },
                    {
                        title: 'Kelipatan Qty',
                        name: 'purchase_details.multiples_quantity',
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
                        title: 'Harga',
                        name: 'purchase_details.price',
                        data: 'price',
                        className: 'text-nowrap text-end',
                        render: (value) => app.numberFormat(Number(value ?? 0)),
                    },
                    {
                        title: 'Subtotal',
                        name: 'purchase_details.subtotal',
                        data: 'subtotal',
                        className: 'text-nowrap text-end',
                        render: (value) => app.numberFormat(Number(value ?? 0)),
                    },
                    ];

                    return app.dataTable('#purchaseProductTable', {
                        columns,
                        serverSide: true,
                        ajax: {
                            url: @json(route('reports.purchases.products')),
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
                        ? @json(route('reports.purchases.products.export.excel'))
                        : @json(route('reports.purchases.export.excel'));

                    $('#buttonExportExcel').attr('href', excelRoute + '?' + queryString);
                    $('#buttonExportPdf').attr('href', @json(route('reports.purchases.export.pdf')) + '?' + queryString);
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
                    $('#tableTitle').text(mode === 'products' ? 'Daftar Produk Pembelian' : 'Daftar Pembelian');
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
                 * Mengambil ringkasan (KPI) sesuai filter (ajax).
                 */
                getSummary() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'get',
                            url: @json(route('reports.purchases.summary')),
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
                 * Menampilkan nilai KPI pada kartu (nilai kosong menjadi nol).
                 */
                renderSummary(summary) {
                    $('#kpiTransactions').text(app.numberFormat(Number(summary?.total_transactions ?? 0), 0));
                    $('#kpiPurchase').text(app.numberFormat(Number(summary?.total_purchase ?? 0)));
                    $('#kpiPaid').text(app.numberFormat(Number(summary?.total_paid ?? 0)));
                    $('#kpiOutstanding').text(app.numberFormat(Number(summary?.total_outstanding ?? 0)));
                }

                /**
                 * Terapkan filter: perbarui query string, link export, KPI, dan tabel.
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

                    // Arahkan tombol export ke filter yang sama dengan tabel.
                    this.updateExportLinks();

                    this.buttonFilter.prop('disabled', true);
                    app.showPreloader();

                    try {
                        // KPI dan tabel memakai filter yang sama.
                        const [response] = await Promise.all([
                            this.getSummary(),
                            new Promise((resolve) => {
                                // Selesai saat tabel tergambar atau saat terjadi error ajax (agar preloader tidak menggantung).
                                // Muat ulang tabel yang sedang tampil sesuai mode.
                                const activeTable = this.mode === 'products' && this.productTable !== null ? this.productTable : this.table;
                                activeTable.one('draw.dt error.dt', resolve);
                                activeTable.ajax.reload();
                            }),
                        ]);

                        this.renderSummary(response.data);
                    } catch (error) {
                        // Error validasi (422): tampilkan pada field terkait.
                        if (error?.status === 422 && error?.errors) {
                            app.setFormErrors(this.form, error);
                            return;
                        }

                        app.alert('danger', error?.message ?? 'Terjadi kesalahan saat mengambil data laporan.');
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
                    $('#supplierId').val('');
                    $('#paymentStatus').val('');
                    $('#invoiceNumber').val('');

                    this.apply();
                }
            }

            window.service = new Service();

            // Error ajax tabel: tampilkan pesan umum (tanpa detail internal).
            service.table.on('error.dt', function (e, settings, techNote, message) {
                app.alert('danger', 'Terjadi kesalahan saat mengambil data laporan.');
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

            const methodLabels = { cash: 'Tunai', transfer: 'Transfer', credit: 'Kredit', debit: 'Debit' };

            // Tombol lihat detail: buka modal daftar produk transaksi.
            // Delegasi pada wrapper tabel karena kolom aksi tetap (fixed columns) berupa klon di luar elemen tabel asli.
            const tableWrapper = '#purchaseReportTable_wrapper';

            $(document).on('click', `${tableWrapper} button[data-action="detail"]`, function () {
                const id = $(this).data('id');

                service.purchaseId = id;
                service.detailUrl = app.url(`/reports/purchases/${id}/details`);

                $('#detailSummary').text($(this).attr('data-summary') ?? '');
                $('#buttonDetailExcel').attr('href', app.url(`/reports/purchases/${id}/export/excel`));
                $('#buttonDetailPdf').attr('href', app.url(`/reports/purchases/${id}/export/pdf`));

                service.modalDetail.show();
            });

            // Tabel detail dibuat saat modal tampil (agar lebar kolom benar); berikutnya cukup ganti URL sumber data.
            $('#modalDetail').on('shown.bs.modal', function () {
                if (service.detailTable !== null) {
                    service.detailTable.ajax.url(service.detailUrl).load();
                    return;
                }

                service.detailTable = app.modalDataTable('#detailTable', {
                    serverSide: true,
                    scrollX: true,
                    ordering: false,
                    order: [[0, 'asc']],
                    ajax: service.detailUrl,
                    columns: [
                        {
                            title: 'Produk',
                            name: 'product.code',
                            data: 'product.code',
                            className: 'text-nowrap',
                        },
                        {
                            title: 'Produk',
                            name: 'product.name',
                            data: 'product.name',
                            className: 'text-nowrap',
                        },
                        {
                            title: 'Unit (Satuan)',
                            name: 'unit.name',
                            data: 'unit.name',
                            className: 'text-nowrap',
                            render: (data, type, row) => `${service.escapeHtml(row.unit.name)} (${service.escapeHtml(row.unit.code)})`,
                        },
                        {
                            title: 'Qty',
                            name: 'quantity',
                            data: 'quantity',
                            className: 'text-nowrap text-end',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                        {
                            title: 'Kelipatan Qty',
                            name: 'multiples_quantity',
                            data: 'multiples_quantity',
                            className: 'text-nowrap text-end',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                        {
                            // Total quantity dalam satuan dasar = qty x kelipatan.
                            title: 'Total Qty (Dasar)',
                            data: null,
                            className: 'text-nowrap text-end',
                            orderable: false,
                            searchable: false,
                            render: (data, type, row) => app.numberFormat(Number(row.quantity ?? 0) * Number(row.multiples_quantity ?? 0)),
                        },
                        {
                            title: 'Harga',
                            name: 'price',
                            data: 'price',
                            className: 'text-nowrap text-end',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                        {
                            title: 'Subtotal',
                            name: 'subtotal',
                            data: 'subtotal',
                            className: 'text-nowrap text-end',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                    ],
                });
            });

            // Tombol lihat pembayaran: buka modal daftar pembayaran transaksi.
            $(document).on('click', `${tableWrapper} button[data-action="payments"]`, function () {
                const id = $(this).data('id');

                service.paymentsUrl = app.url(`/reports/purchases/${id}/payments`);

                $('#paymentsSummary').text($(this).attr('data-summary') ?? '');

                service.modalPayments.show();
            });

            $('#modalPayments').on('shown.bs.modal', function () {
                if (service.paymentsTable !== null) {
                    service.paymentsTable.ajax.url(service.paymentsUrl).load();
                    return;
                }

                service.paymentsTable = app.modalDataTable('#paymentsTable', {
                    serverSide: true,
                    scrollX: true,
                    ajax: service.paymentsUrl,
                    order: [[0, 'asc']],
                    ordering: false,
                    columns: [
                        {
                            title: 'Tanggal',
                            name: 'payment_date',
                            data: 'payment_date',
                            className: 'text-nowrap text-start',
                        },
                        {
                            title: 'Petugas',
                            name: 'user.full_name',
                            data: 'user.full_name',
                            className: 'text-nowrap',
                            render: (data) => service.escapeHtml(data ?? '-'),
                        },
                        {
                            title: 'Metode',
                            name: 'payment_method',
                            data: 'payment_method',
                            className: 'text-nowrap',
                            render: (data) => methodLabels[data] ?? service.escapeHtml(data),
                        },
                        {
                            title: 'Nominal',
                            name: 'amount',
                            data: 'amount',
                            className: 'text-nowrap',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                        {
                            title: 'Catatan',
                            name: 'notes',
                            data: 'notes',
                            render: (data) => service.escapeHtml(data ?? '-'),
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
