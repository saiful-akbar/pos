<x-layouts.auth title="Laporan Penjualan" :access="$access">
    {{-- Header halaman: judul dan ikon --}}
    <x-page-header title="Laporan Penjualan" icon="point_of_sale" />

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
                        <p class="text-body-secondary mt-1 mb-0">Pilih periode lalu saring data penjualan yang ingin ditampilkan.</p>
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
                            <div class="col-12 col-md-6 col-xl-4 mb-5">
                                <x-form-input type="text" name="start_date" id="startDate" label="Tanggal Mulai"
                                    placeholder="Pilih tanggal" value="{{ request('start_date', date('Y-m-01')) }}"
                                    data-flatpickr="" required />
                            </div>

                            <div class="col-12 col-md-6 col-xl-4 mb-5">
                                <x-form-input type="text" name="end_date" id="endDate" label="Tanggal Akhir"
                                    placeholder="Pilih tanggal" value="{{ request('end_date', date('Y-m-t')) }}"
                                    data-flatpickr="" required />
                            </div>
                        </div>

                        <hr class="mt-1 mb-4">

                        {{-- Transaksi (opsional): kasir, metode pembayaran, dan nomor invoice --}}
                        <div class="d-flex align-items-center gap-2 text-body-secondary small fw-semibold text-uppercase mb-4">
                            <x-icon class="fs-6">receipt_long</x-icon>
                            <span>Transaksi</span>
                        </div>

                        <div class="row gx-6">
                            <div class="col-12 col-md-6 col-xl-4 mb-5">
                                <x-form-select name="user_id" id="userId" label="Kasir">
                                    <option value="">Semua</option>
                                    @foreach ($cashiers as $cashier)
                                        <option value="{{ $cashier['id'] }}" @selected(request('user_id') === $cashier['id'])>
                                            {{ $cashier['name'] }}
                                        </option>
                                    @endforeach
                                </x-form-select>
                            </div>

                            <div class="col-12 col-md-6 col-xl-4 mb-5">
                                <x-form-select name="payment_method" id="paymentMethod" label="Metode Pembayaran">
                                    <option value="">Semua</option>
                                    <option value="cash" @selected(request('payment_method') === 'cash')>Tunai</option>
                                    <option value="qris" @selected(request('payment_method') === 'qris')>QRIS</option>
                                    <option value="debit" @selected(request('payment_method') === 'debit')>Debit</option>
                                    <option value="credit" @selected(request('payment_method') === 'credit')>Kredit</option>
                                </x-form-select>
                            </div>

                            <div class="col-12 col-md-6 col-xl-4 mb-5">
                                <x-form-input type="text" name="invoice_number" id="invoiceNumber" label="Nomor Invoice"
                                    placeholder="Cari nomor invoice" value="{{ request('invoice_number') }}" maxlength="50" />
                            </div>
                        </div>

                        <hr class="mt-1 mb-4">

                        {{-- Produk (opsional): kategori dan kode produk --}}
                        <div class="d-flex align-items-center gap-2 text-body-secondary small fw-semibold text-uppercase mb-4">
                            <x-icon class="fs-6">inventory_2</x-icon>
                            <span>Produk</span>
                        </div>

                        <div class="row gx-6">
                            <div class="col-12 col-md-6 col-xl-4 mb-5">
                                <x-form-select name="category_id" id="categoryId" label="Kategori Produk">
                                    <option value="">Semua</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(request('category_id') === $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </x-form-select>
                            </div>

                            <div class="col-12 col-md-6 col-xl-4 mb-5">
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
                ['id' => 'kpiTransactions', 'label' => 'Total Transaksi', 'icon' => 'receipt_long', 'color' => 'primary', 'value' => '0'],
                ['id' => 'kpiSales', 'label' => 'Total Penjualan', 'icon' => 'point_of_sale', 'color' => 'success', 'value' => '0.00'],
                ['id' => 'kpiProducts', 'label' => 'Produk Terjual', 'icon' => 'inventory_2', 'color' => 'info', 'value' => '0.00', 'hint' => 'Dalam satuan dasar'],
                ['id' => 'kpiDiscount', 'label' => 'Total Diskon', 'icon' => 'sell', 'color' => 'primary', 'value' => '0.00', 'hint' => 'Diskon item + pelanggan'],
            ];
        @endphp

        @foreach ($kpis as $kpi)
            <div class="col-12 col-sm-6 col-xl-3 mt-7">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div style="min-width: 0;">
                                <div class="text-body-secondary small fw-semibold text-uppercase">{{ $kpi['label'] }}</div>
                                <div class="fs-3 fw-bold text-break mt-2" id="{{ $kpi['id'] }}">{{ $kpi['value'] }}</div>
                            </div>

                            <div class="rounded-3 bg-{{ $kpi['color'] }} bg-opacity-10 text-body d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 3rem; height: 3rem;">
                                <x-icon class="fs-4">{{ $kpi['icon'] }}</x-icon>
                            </div>
                        </div>

                        <div class="small text-body-secondary mt-2">{{ $kpi['hint'] ?? 'Sesuai filter aktif' }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Card tabel laporan beserta tombol export --}}
    <div class="row gx-7">
        <div class="col-12 mt-7 mb-7">
            <div class="card shadow-sm">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 py-4">
                    <div style="min-width: 0;">
                        <h2 class="fs-5 mb-0">Daftar Penjualan</h2>
                        <p class="text-body-secondary mt-1 mb-0" id="periodLabel">&nbsp;</p>
                    </div>

                    {{-- Tombol export (hanya ikon) dengan tooltip --}}
                    <div class="d-flex gap-2">
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

                <div class="card-body">
                    <table id="saleReportTable" class="table table-sm table-hover align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal detail produk transaksi penjualan --}}
    <x-modal id="modalDetail" title="Detail Penjualan" size="xl" fullScreen="md">
        <x-slot:headerAction>
            <div class="d-flex align-items-center gap-3 ms-auto ps-3">
                <span id="detailSummary" class="badge border fw-normal text-wrap d-none d-md-inline"></span>

                <x-button type="link" id="buttonDetailPdf" href="#" target="_blank" color="light" size="sm"
                    class="border rounded" title="Cetak PDF" aria-label="Cetak PDF">
                    <x-icon class="fs-6">picture_as_pdf</x-icon>
                </x-button>
            </div>
        </x-slot:headerAction>

        <table id="detailTable" class="table table-sm table-hover align-middle w-100"></table>
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
                    this.methodLabels = { cash: 'Tunai', qris: 'QRIS', debit: 'Debit', credit: 'Kredit' };
                    this.table = this.#reportDataTableInit();
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
                                        user_id: $('#userId').val(),
                        payment_method: $('#paymentMethod').val(),
                        category_id: $('#categoryId').val(),
                        product_code: $('#productCode').val().trim(),
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
                 * Inisialisasi DataTables laporan penjualan (server side).
                 */
                #reportDataTableInit() {
                    const money = (data) => app.numberFormat(Number(data ?? 0));

                    const columns = [
                        {
                            title: 'Tanggal',
                            name: 'sales.transaction_date',
                            data: 'transaction_date',
                            className: 'text-nowrap',
                        },
                        {
                            title: 'Invoice',
                            name: 'sales.invoice_number',
                            data: 'invoice_number',
                            className: 'text-nowrap',
                            render: (data) => this.escapeHtml(data ?? '-'),
                        },
                        {
                            // Transaksi tanpa pelanggan ditampilkan sebagai "Umum".
                            title: 'Pelanggan',
                            name: 'customers.name',
                            data: 'customer_name',
                            className: 'text-nowrap',
                            render: (data) => this.escapeHtml(data ?? 'Umum'),
                        },
                        {
                            title: 'Jenis Produk',
                            name: 'sales.total_product',
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
                            // Total harga sebelum diskon (jumlah subtotal detail).
                            title: 'Total Harga',
                            name: 'items_subtotal',
                            data: 'items_subtotal',
                            className: 'text-nowrap text-end',
                            searchable: false,
                            render: money,
                        },
                        {
                            // Nominal diskon: diskon item + diskon pelanggan.
                            title: 'Diskon',
                            name: 'discount_amount',
                            data: 'discount_amount',
                            className: 'text-nowrap text-end',
                            searchable: false,
                            render: money,
                        },
                        {
                            title: 'Grand Total',
                            name: 'sales.grand_total',
                            data: 'grand_total',
                            className: 'text-nowrap text-end fw-semibold',
                            render: money,
                        },
                        {
                            title: 'Dibayar',
                            name: 'sales.paid_amount',
                            data: 'paid_amount',
                            className: 'text-nowrap text-end',
                            render: money,
                        },
                        {
                            title: 'Kembalian',
                            name: 'sales.change_amount',
                            data: 'change_amount',
                            className: 'text-nowrap text-end',
                            render: money,
                        },
                        {
                            title: 'Metode',
                            name: 'sales.payment_method',
                            data: 'payment_method',
                            className: 'text-nowrap',
                            render: (data) => this.methodLabels[data] ?? this.escapeHtml(data ?? '-'),
                        },
                        {
                            title: 'Kasir',
                            name: 'users.full_name',
                            data: 'cashier_name',
                            className: 'text-nowrap',
                            render: (data) => this.escapeHtml(data ?? '-'),
                        },
                        {
                            title: app.icon('settings', 'fs-6'),
                            name: 'sales.id',
                            data: 'id',
                            className: 'text-nowrap text-center',
                            searchable: false,
                            orderable: false,
                            render: (data, type, row) => {
                                // Ringkasan transaksi disimpan pada atribut tombol (kolom tetap dikloning DataTables).
                                const summary = this.attr(`${row.invoice_number ?? '-'} • ${row.customer_name ?? 'Umum'}`);

                                return `
                                    <button type="button" class="btn btn-sm btn-light" data-action="detail" data-id="${this.escapeHtml(data)}" data-summary="${summary}" title="Lihat detail" aria-label="Lihat detail">
                                        ${app.icon('visibility', 'fs-6')}
                                    </button>
                                `;
                            },
                        },
                    ];

                    return app.dataTable('#saleReportTable', {
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
                            url: @json(route('reports.sales')),
                            // Kirim filter terbaru pada setiap request tabel.
                            data: (params) => Object.assign(params, this.filters()),
                        },
                        order: [[0, 'desc']],
                    });
                }

                /**
                 * Mengambil ringkasan (KPI) sesuai filter (ajax).
                 */
                getSummary() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'get',
                            url: @json(route('reports.sales.summary')),
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
                    $('#kpiSales').text(app.numberFormat(Number(summary?.total_sales ?? 0)));
                    $('#kpiProducts').text(app.numberFormat(Number(summary?.total_products_sold ?? 0)));
                    $('#kpiDiscount').text(app.numberFormat(Number(summary?.total_discount ?? 0)));
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

                    // Keterangan periode pada header tabel.
                    $('#periodLabel').text(`Periode ${this.startDate.val()} s/d ${this.endDate.val()}`);

                    // Arahkan tombol export ke filter yang sama dengan tabel.
                    $('#buttonExportExcel').attr('href', @json(route('reports.sales.export.excel')) + '?' + queryString);
                    $('#buttonExportPdf').attr('href', @json(route('reports.sales.export.pdf')) + '?' + queryString);

                    this.buttonFilter.prop('disabled', true);
                    app.showPreloader();

                    try {
                        // KPI dan tabel memakai filter yang sama.
                        const [response] = await Promise.all([
                            this.getSummary(),
                            new Promise((resolve) => {
                                // Selesai saat tabel tergambar atau saat terjadi error ajax (agar preloader tidak menggantung).
                                this.table.one('draw.dt error.dt', resolve);
                                this.table.ajax.reload();
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
                    $('#userId, #paymentMethod, #categoryId').val('');
                    $('#productCode, #invoiceNumber').val('');

                    this.apply();
                }
            }

            window.service = new Service();

            // Error ajax tabel: tampilkan pesan umum (tanpa detail internal).
            service.table.on('error.dt', function () {
                app.alert('danger', 'Terjadi kesalahan saat mengambil data laporan.');
            });

            // Terapkan filter.
            service.form.submit(function (e) {
                e.preventDefault();
                service.apply();
            });

            // Reset filter.
            $('#buttonReset').click(function (e) {
                e.preventDefault();
                service.reset();
            });

            // Delegasi pada wrapper tabel karena kolom aksi tetap (fixed columns) berupa klon di luar elemen tabel asli.
            $(document).on('click', '#saleReportTable_wrapper button[data-action="detail"]', function () {
                const id = $(this).data('id');

                service.detailUrl = app.url(`/reports/sales/${id}/details`);
                $('#detailSummary').text($(this).attr('data-summary') ?? '');
                $('#buttonDetailPdf').attr('href', app.url(`/reports/sales/${id}/export/pdf`));

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
                            className: 'text-nowrap',
                            render: (data) => service.escapeHtml(data),
                        },
                        {
                            title: 'Unit (Satuan)',
                            name: 'units.name',
                            data: 'unit_name',
                            className: 'text-nowrap',
                            render: (data, type, row) => `${service.escapeHtml(row.unit_name)} (${service.escapeHtml(row.unit_code)})`,
                        },
                        {
                            title: 'Harga Jual',
                            name: 'sale_details.selling_price',
                            data: 'selling_price',
                            className: 'text-nowrap text-end',
                            render: money,
                        },
                        {
                            title: 'Qty',
                            name: 'sale_details.quantity',
                            data: 'quantity',
                            className: 'text-nowrap text-end',
                            render: money,
                        },
                        {
                            title: 'Kelipatan Qty',
                            name: 'sale_details.multiples_quantity',
                            data: 'multiples_quantity',
                            className: 'text-nowrap text-end',
                            render: money,
                        },
                        {
                            // Total quantity dalam satuan dasar = qty x kelipatan.
                            title: 'Total Qty (Dasar)',
                            data: null,
                            className: 'text-nowrap text-end',
                            orderable: false,
                            searchable: false,
                            render: (data, type, row) => money(Number(row.quantity ?? 0) * Number(row.multiples_quantity ?? 0)),
                        },
                        {
                            title: 'Subtotal',
                            name: 'sale_details.subtotal',
                            data: 'subtotal',
                            className: 'text-nowrap text-end',
                            render: money,
                        },
                        {
                            title: 'Diskon (%)',
                            name: 'sale_details.discount',
                            data: 'discount',
                            className: 'text-nowrap text-end',
                            render: money,
                        },
                        {
                            title: 'Grand Total',
                            name: 'sale_details.grand_total',
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
