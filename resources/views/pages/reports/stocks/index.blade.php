<x-layouts.auth title="Laporan Stok" :access="$access">
    {{-- Header halaman: judul, deskripsi singkat, dan tombol export --}}
    <x-page-header title="Laporan Stok / Inventory" icon="inventory">
        <x-slot:action>
            <div class="d-flex justify-content-lg-end gap-2">
                <x-button type="link" id="buttonExportExcel" href="#" color="light" startIcon="download"
                    class="border">
                    Excel
                </x-button>

                <x-button type="link" id="buttonExportPdf" href="#" target="_blank" color="light"
                    startIcon="picture_as_pdf" class="border">
                    PDF
                </x-button>
            </div>
        </x-slot:action>

        <p class="text-body-secondary small mb-0 mt-4">
            Rekonsiliasi mutasi stok per bulan: stok awal ditambah pembelian dan pengembalian, dikurangi penjualan (Book
            Balance), lalu dibandingkan dengan stok awal bulan berikutnya.
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
                        <p class="text-body-secondary mt-1 mb-0">Pilih bulan dan tahun laporan, lalu saring produk yang
                            ingin ditampilkan.</p>
                    </div>
                </div>

                <form id="formReport" autocomplete="off">
                    @php
                        $monthNames = [
                            'Januari',
                            'Februari',
                            'Maret',
                            'April',
                            'Mei',
                            'Juni',
                            'Juli',
                            'Agustus',
                            'September',
                            'Oktober',
                            'November',
                            'Desember',
                        ];
                        // Default: bulan sebelumnya (stok akhir aktual membutuhkan snapshot bulan berikutnya).
                        $defaultPeriod = now()->subMonthNoOverflow();
                        $selectedMonth = (int) request('month', $defaultPeriod->month);
                        $selectedYear = (int) request('year', $defaultPeriod->year);
                    @endphp

                    <div class="card-body pb-0">
                        {{-- Periode (wajib) --}}
                        <div
                            class="d-flex align-items-center gap-2 text-body-secondary small fw-semibold text-uppercase mb-4">
                            <x-icon class="fs-6">date_range</x-icon>
                            <span>Periode</span>
                        </div>

                        <div class="row gx-6">
                            <div class="col-12 col-md-6 col-xl-3 mb-5">
                                <x-form-select name="month" id="month" label="Bulan" required>
                                    @foreach ($monthNames as $index => $monthName)
                                        <option value="{{ $index + 1 }}" @selected($selectedMonth === $index + 1)>
                                            {{ $monthName }}</option>
                                    @endforeach
                                </x-form-select>
                            </div>

                            <div class="col-12 col-md-6 col-xl-3 mb-5">
                                <x-form-input type="number" name="year" id="year" label="Tahun"
                                    value="{{ $selectedYear }}" min="2000" max="{{ date('Y') + 1 }}" required />
                            </div>
                        </div>

                        <hr class="mt-1 mb-4">

                        {{-- Produk dan status (opsional) --}}
                        <div
                            class="d-flex align-items-center gap-2 text-body-secondary small fw-semibold text-uppercase mb-4">
                            <x-icon class="fs-6">inventory_2</x-icon>
                            <span>Produk dan Status</span>
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
                                <x-form-input type="text" name="keyword" id="keyword" label="Kode / Nama Produk"
                                    placeholder="Cari kode atau nama produk" value="{{ request('keyword') }}"
                                    maxlength="100" />
                            </div>

                            <div class="col-12 col-md-6 col-xl-3 mb-5">
                                <x-form-select name="status" id="status" label="Status Selisih">
                                    <option value="">Semua</option>
                                    <option value="match" @selected(request('status') === 'match')>Sesuai</option>
                                    <option value="negative" @selected(request('status') === 'negative')>Selisih Kurang</option>
                                    <option value="positive" @selected(request('status') === 'positive')>Selisih Lebih</option>
                                    <option value="unavailable" @selected(request('status') === 'unavailable')>Belum Dapat Direkonsiliasi
                                    </option>
                                </x-form-select>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol reset dan terapkan filter berukuran sama --}}
                    <div class="card-footer">
                        <div class="row justify-content-end gx-3 gy-3">
                            <div class="col-12 col-sm-6 col-md-4 col-xl-2 order-2 order-sm-1">
                                <x-button type="button" id="buttonReset" color="light" startIcon="restart_alt"
                                    class="border" fullWidth>
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

    {{-- Keterangan ketersediaan snapshot stok awal dan stok akhir --}}
    <div class="row gx-7">
        <div class="col-12 mt-7">
            <div class="border rounded-3 p-4 small d-flex align-items-center gap-3" id="snapshotNotice">
                <x-icon class="fs-5">info</x-icon>
                <div id="snapshotNoticeText" class="text-body-secondary">Memuat informasi snapshot stok...</div>
            </div>
        </div>
    </div>

    {{-- Kartu ringkasan: sebaran status rekonsiliasi (jumlah produk, bukan jumlah kuantitas lintas satuan).
         Layar lebar: 3 kartu pada baris pertama dan 2 kartu pada baris kedua. --}}
    <div class="row gx-7">
        @php
            $kpis = [
                [
                    'id' => 'kpiTotal',
                    'cols' => 'col-xl-4',
                    'label' => 'Produk Dilaporkan',
                    'icon' => 'inventory_2',
                    'color' => 'primary',
                    'hint' => 'Punya snapshot atau mutasi',
                ],
                [
                    'id' => 'kpiMatch',
                    'cols' => 'col-xl-4',
                    'label' => 'Sesuai',
                    'icon' => 'check_circle',
                    'color' => 'success',
                    'hint' => 'Selisih 0',
                ],
                [
                    'id' => 'kpiNegative',
                    'cols' => 'col-xl-4',
                    'label' => 'Selisih Kurang',
                    'icon' => 'trending_down',
                    'color' => 'danger',
                    'hint' => 'Stok aktual < Book Balance',
                ],
                [
                    'id' => 'kpiPositive',
                    'cols' => 'col-xl-6',
                    'label' => 'Selisih Lebih',
                    'icon' => 'trending_up',
                    'color' => 'info',
                    'hint' => 'Stok aktual > Book Balance',
                ],
                [
                    'id' => 'kpiUnavailable',
                    'cols' => 'col-xl-6',
                    'label' => 'Belum Direkonsiliasi',
                    'icon' => 'help',
                    'color' => 'secondary',
                    'hint' => 'Snapshot belum lengkap',
                ],
            ];
        @endphp

        @foreach ($kpis as $kpi)
            <div class="col-12 col-sm-6 {{ $kpi['cols'] }} mt-7">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div style="min-width: 0;">
                                <div class="text-body-secondary small fw-semibold text-uppercase">{{ $kpi['label'] }}
                                </div>
                                <div class="fs-3 fw-bold text-break mt-2" id="{{ $kpi['id'] }}">0</div>
                            </div>

                            <div class="rounded-3 bg-{{ $kpi['color'] }} bg-opacity-10 text-body d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 3rem; height: 3rem;">
                                <x-icon class="fs-4">{{ $kpi['icon'] }}</x-icon>
                            </div>
                        </div>

                        <div class="small text-body-secondary mt-2">{{ $kpi['hint'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Card tabel rekonsiliasi --}}
    <div class="row gx-7">
        <div class="col-12 mt-7 mb-7">
            <div class="card shadow-sm">
                <div class="card-header py-4">
                    <h2 class="fs-5 mb-0">Rekonsiliasi Stok per Produk</h2>
                    <p class="text-body-secondary mt-1 mb-0" id="periodLabel">&nbsp;</p>
                </div>

                <div class="card-body">
                    <table id="stockReportTable" class="table table-sm table-hover align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal detail mutasi produk --}}
    <x-modal id="modalDetail" title="Detail Mutasi Produk" size="xl" fullScreen="md">
        {{-- Informasi produk dan hasil rekonsiliasi --}}
        <div class="row gx-6 gy-3 mb-5 small" id="detailInfo"></div>

        <table id="mutationTable" class="table table-sm table-hover align-middle w-100"></table>

        <div class="small text-body-secondary mt-4">
            Urutan pada tanggal yang sama: barang masuk (pembelian, pengembalian) terlebih dahulu, lalu penjualan.
            Urutan ini hanya memengaruhi saldo berjalan, bukan total.
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
                    this.mutationTable = null;
                    this.form = $('#formReport');
                    this.buttonFilter = $('#buttonFilter');
                    this.monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
                        'September', 'Oktober', 'November', 'Desember'
                    ];
                    this.statusMap = {
                        match: ['Sesuai', 'check_circle', 'text-bg-success'],
                        negative: ['Kurang', 'trending_down', 'text-bg-danger'],
                        positive: ['Lebih', 'trending_up', 'bg-primary bg-opacity-10 text-body border'],
                        unavailable: ['Belum direkonsiliasi', 'help', 'border text-body-secondary'],
                    };
                    this.typeLabels = {
                        opening: 'Stok Awal',
                        purchase: 'Pembelian',
                        return: 'Pengembalian Penjualan',
                        sale: 'Penjualan'
                    };
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
                 * Format kuantitas (2 desimal); null ditampilkan sebagai strip.
                 */
                qty(value) {
                    return value === null || value === undefined ? '-' : app.numberFormat(Number(value));
                }

                /**
                 * Format selisih dengan tanda + / - agar mudah dikenali.
                 */
                signed(value) {
                    if (value === null || value === undefined) return '-';

                    const number = Number(value);
                    const text = app.numberFormat(Math.abs(number));

                    return number > 0 ? `+${text}` : (number < 0 ? `-${text}` : text);
                }

                /**
                 * Badge status rekonsiliasi (ikon dan teks, bukan hanya warna).
                 */
                statusBadge(status) {
                    const [label, icon, classes] = this.statusMap[status] ?? [this.escapeHtml(status), 'help', 'border'];

                    return `<span class="badge ${classes} d-inline-flex align-items-center gap-1">${app.icon(icon, 'fs-6')}<span>${label}</span></span>`;
                }

                /**
                 * Nilai filter saat ini dari form (nilai kosong tidak dikirim).
                 */
                filters() {
                    const filters = {
                        month: $('#month').val(),
                        year: $('#year').val(),
                        category_id: $('#categoryId').val(),
                        keyword: $('#keyword').val().trim(),
                        status: $('#status').val(),
                    };

                    Object.keys(filters).forEach((key) => {
                        if (!filters[key]) delete filters[key];
                    });

                    return filters;
                }

                /**
                 * Label periode (bulan tahun) dari bulan dan tahun.
                 */
                periodText(month, year) {
                    return `${this.monthNames[Number(month) - 1]} ${year}`;
                }

                /**
                 * Inisialisasi DataTables rekonsiliasi stok (server side).
                 */
                #reportDataTableInit() {
                    const quantity = (data) => this.qty(data);

                    const columns = [{
                            title: 'Kode',
                            name: 'x.code',
                            data: 'code',
                            className: 'text-nowrap fw-semibold',
                            render: (data) => this.escapeHtml(data),
                        },
                        {
                            title: 'Produk',
                            name: 'x.name',
                            data: 'name',
                            width: '150px',
                            render: (data, type, row) => (`
                                <div>${this.escapeHtml(data)}</div>
                                <div class="small text-body-secondary">${this.escapeHtml(row.category_name ?? '-')}</div>
                            `),
                        },
                        {
                            title: 'Satuan Dasar',
                            data: 'base_unit',
                            className: 'text-nowrap',
                            orderable: false,
                            searchable: false,
                            render: (data) => this.escapeHtml(data ?? '-'),
                        },
                        {
                            title: 'Stok Awal',
                            name: 'opening',
                            data: 'opening',
                            className: 'text-nowrap text-end',
                            searchable: false,
                            render: quantity
                        },
                        {
                            title: 'Pembelian',
                            name: 'purchase_qty',
                            data: 'purchase_qty',
                            className: 'text-nowrap text-end',
                            searchable: false,
                            render: quantity
                        },
                        {
                            title: 'Pengembalian',
                            name: 'return_qty',
                            data: 'return_qty',
                            className: 'text-nowrap text-end',
                            searchable: false,
                            render: quantity
                        },
                        {
                            title: 'Penjualan',
                            name: 'sale_qty',
                            data: 'sale_qty',
                            className: 'text-nowrap text-end',
                            searchable: false,
                            render: quantity
                        },
                        {
                            title: 'Book Balance',
                            name: 'book_balance',
                            data: 'book_balance',
                            className: 'text-nowrap text-end fw-semibold',
                            searchable: false,
                            render: quantity
                        },
                        {
                            title: 'Stok Akhir',
                            name: 'closing',
                            data: 'closing',
                            className: 'text-nowrap text-end',
                            searchable: false,
                            render: quantity
                        },
                        {
                            title: 'Selisih',
                            name: 'difference',
                            data: 'difference',
                            className: 'text-nowrap text-end fw-bold',
                            searchable: false,
                            render: (data) => this.signed(data),
                        },
                        {
                            title: 'Status',
                            name: 'status',
                            data: 'status',
                            className: 'text-nowrap text-center',
                            searchable: false,
                            orderable: false,
                            render: (data) => this.statusBadge(data),
                        },
                        {
                            title: app.icon('settings', 'fs-6'),
                            name: 'x.id',
                            data: 'id',
                            className: 'text-nowrap text-center',
                            searchable: false,
                            orderable: false,
                            render: (data) => (`
                                <button type="button"
                                    class="btn btn-light"
                                    data-action="detail"
                                    data-id="${this.attr(data)}"
                                    title="Lihat detail mutasi"
                                    aria-label="Lihat detail mutasi"
                                >
                                    ${app.icon('visibility', 'fs-6')}
                                </button>
                            `),
                        },
                    ];

                    return app.dataTable('#stockReportTable', {
                        columns,
                        serverSide: true,
                        deferLoading: 0,
                        fixedColumns: {
                            start: 2,
                            end: 1,
                        },
                        language: {
                            search: '',
                            searchPlaceholder: 'Cari...',
                            lengthMenu: '_MENU_',
                            info: '_START_ - _END_ dari _TOTAL_',
                            infoEmpty: '0 - 0 dari 0',
                            infoFiltered: '',
                            zeroRecords: 'Tidak ditemukan produk yang sesuai dengan pencarian.',
                            emptyTable: 'Tidak ada produk pada periode atau filter yang dipilih. Pastikan snapshot stok awal bulan sudah dibuat, atau klik Reset.',
                        },
                        ajax: {
                            url: @json(route('reports.stocks')),
                            // Kirim filter terbaru pada setiap request tabel.
                            data: (params) => Object.assign(params, this.filters()),
                        },
                        order: [
                            [0, 'asc']
                        ],
                    });
                }

                /**
                 * Permintaan ajax GET dengan filter aktif.
                 */
                #get(url, extra = {}) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'get',
                            url,
                            dataType: 'json',
                            data: {
                                ...this.filters(),
                                ...extra
                            },
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            success: (response) => resolve(response),
                            error: (xhr) => reject({
                                ...xhr.responseJSON,
                                status: xhr.status
                            }),
                        });
                    });
                }

                /**
                 * Menampilkan sebaran status dan keterangan snapshot.
                 */
                renderSummary(summary) {
                    $('#kpiTotal').text(app.numberFormat(Number(summary?.total_products ?? 0), 0));
                    $('#kpiMatch').text(app.numberFormat(Number(summary?.matched ?? 0), 0));
                    $('#kpiNegative').text(app.numberFormat(Number(summary?.negative ?? 0), 0));
                    $('#kpiPositive').text(app.numberFormat(Number(summary?.positive ?? 0), 0));
                    $('#kpiUnavailable').text(app.numberFormat(Number(summary?.unavailable ?? 0), 0));

                    const filters = this.filters();
                    const opening = this.periodText(filters.month, filters.year);
                    const nextMonth = Number(filters.month) === 12 ? 1 : Number(filters.month) + 1;
                    const nextYear = Number(filters.month) === 12 ? Number(filters.year) + 1 : Number(filters.year);
                    const closing = this.periodText(nextMonth, nextYear);

                    const messages = [];
                    messages.push(summary?.opening_available ?
                        `Stok awal: snapshot ${opening} tersedia.` :
                        `Snapshot stok awal ${opening} belum tersedia.`);
                    messages.push(summary?.closing_available ?
                        `Stok akhir aktual: snapshot ${closing} tersedia.` :
                        `Snapshot stok awal ${closing} (stok akhir aktual) belum tersedia.`);

                    const complete = summary?.opening_available && summary?.closing_available;
                    $('#snapshotNoticeText').text(complete ?
                        messages.join(' ') :
                        `${messages.join(' ')} Produk belum dapat direkonsiliasi dan selisih tidak dihitung.`);
                }

                /**
                 * Terapkan filter: perbarui query string, link export, ringkasan, dan tabel.
                 */
                async apply() {
                    app.clearAlert();
                    app.clearFormErrors(this.form);

                    const filters = this.filters();

                    if (!filters.month || !filters.year) {
                        app.alert('danger', 'Bulan dan tahun wajib diisi.');
                        return;
                    }

                    const queryString = new URLSearchParams(filters).toString();

                    // Perbarui query string URL agar reload halaman memakai filter yang sama.
                    const url = new URL(window.location);
                    url.search = queryString;
                    window.history.pushState({}, '', url);

                    $('#periodLabel').text(`Periode ${this.periodText(filters.month, filters.year)}`);

                    // Arahkan tombol export ke filter yang sama dengan tabel.
                    $('#buttonExportExcel').attr('href', @json(route('reports.stocks.export.excel')) + '?' + queryString);
                    $('#buttonExportPdf').attr('href', @json(route('reports.stocks.export.pdf')) + '?' + queryString);

                    this.buttonFilter.prop('disabled', true);
                    app.showPreloader();

                    try {
                        // Ringkasan dan tabel memakai filter yang sama.
                        const [summary] = await Promise.all([
                            this.#get(@json(route('reports.stocks.summary'))),
                            new Promise((resolve) => {
                                // Selesai saat tabel tergambar atau saat terjadi error ajax (agar preloader tidak menggantung).
                                this.table.one('draw.dt error.dt', resolve);
                                this.table.ajax.reload();
                            }),
                        ]);

                        this.renderSummary(summary.data);
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
                 * Kembalikan filter ke nilai awal (bulan sebelumnya, tanpa filter lain).
                 */
                reset() {
                    const previous = new Date();
                    previous.setDate(1);
                    previous.setMonth(previous.getMonth() - 1);

                    $('#month').val(previous.getMonth() + 1);
                    $('#year').val(previous.getFullYear());
                    $('#categoryId, #status').val('');
                    $('#keyword').val('');

                    this.apply();
                }

                /**
                 * Membuka detail mutasi satu produk.
                 */
                async openDetail(productId) {
                    app.clearAlert();
                    app.showPreloader();

                    try {
                        const response = await this.#get(app.url(`/reports/stocks/${productId}/mutations`));
                        const {
                            product,
                            mutations
                        } = response.data;
                        const filters = this.filters();

                        // Informasi produk dan hasil rekonsiliasi.
                        const info = [
                            ['Kode Produk', product.code],
                            ['Nama Produk', product.name],
                            ['Kategori', product.category_name ?? '-'],
                            ['Satuan Dasar', product.base_unit ?? '-'],
                            ['Periode', this.periodText(filters.month, filters.year)],
                            ['Stok Awal', this.qty(product.opening)],
                            ['Book Balance', this.qty(product.book_balance)],
                            ['Stok Akhir Aktual', this.qty(product.closing)],
                            ['Selisih', this.signed(product.difference)],
                        ];

                        $('#detailInfo').html(info.map(([label, value]) => `
                            <div class="col-6 col-md-4 col-xl-3">
                                <div class="text-body-secondary">${this.escapeHtml(label)}</div>
                                <div class="fw-semibold text-break">${this.escapeHtml(value)}</div>
                            </div>
                        `).join('') + `
                            <div class="col-6 col-md-4 col-xl-3">
                                <div class="text-body-secondary">Status</div>
                                <div>${this.statusBadge(product.status)}</div>
                            </div>
                        `);

                        // Baris pertama: stok awal sebagai saldo awal, lalu mutasi dengan saldo berjalan.
                        const rows = [{
                            date: '-',
                            type: 'opening',
                            reference: 'Snapshot stok awal',
                            unit_code: '-',
                            quantity: null,
                            base: null,
                            in_qty: null,
                            out_qty: null,
                            balance: product.opening,
                            user_name: '-',
                        }].concat(mutations.map((m) => ({
                            date: m.date,
                            type: m.type,
                            reference: m.reference ?? '(tanpa nomor referensi)',
                            unit_code: m.unit_code,
                            quantity: m.quantity,
                            multiples: m.multiples_quantity,
                            base: Number(m.quantity) * Number(m.multiples_quantity),
                            in_qty: m.in_qty,
                            out_qty: m.out_qty,
                            balance: m.balance,
                            user_name: m.user_name ?? '-',
                        })));

                        this.#renderMutationTable(rows);
                        this.modalDetail.show();
                    } catch (error) {
                        app.alert('danger', 'Gagal memuat detail mutasi produk. Silakan coba lagi.');
                    } finally {
                        app.hidePreloader();
                    }
                }

                /**
                 * Menampilkan tabel mutasi (client side; data sudah terurut dan saldo dihitung di server).
                 */
                #renderMutationTable(rows) {
                    if (this.mutationTable !== null) {
                        this.mutationTable.destroy();
                        $('#mutationTable').empty();
                    }

                    this.mutationTable = app.modalDataTable('#mutationTable', {
                        data: rows,
                        ordering: false,
                        scrollX: true,
                        paging: rows.length > 25,
                        searching: false,
                        columns: [{
                                title: 'Tanggal',
                                data: 'date',
                                className: 'text-nowrap'
                            },
                            {
                                title: 'Jenis',
                                data: 'type',
                                className: 'text-nowrap',
                                render: (data) => this.typeLabels[data] ?? this.escapeHtml(data)
                            },
                            {
                                title: 'Referensi',
                                data: 'reference',
                                render: (data) => this.escapeHtml(data)
                            },
                            {
                                title: 'Satuan',
                                data: 'unit_code',
                                className: 'text-nowrap',
                                render: (data) => this.escapeHtml(data)
                            },
                            {
                                title: 'Qty Transaksi',
                                data: 'quantity',
                                className: 'text-nowrap text-end',
                                render: (data) => this.qty(data)
                            },
                            {
                                title: 'Kelipatan',
                                data: 'multiples',
                                className: 'text-nowrap text-end',
                                render: (data) => this.qty(data)
                            },
                            {
                                title: 'Masuk (Dasar)',
                                data: 'in_qty',
                                className: 'text-nowrap text-end',
                                render: (data) => data === null || Number(data) === 0 ? '-' : this.qty(data)
                            },
                            {
                                title: 'Keluar (Dasar)',
                                data: 'out_qty',
                                className: 'text-nowrap text-end',
                                render: (data) => data === null || Number(data) === 0 ? '-' : this.qty(data)
                            },
                            {
                                title: 'Saldo',
                                data: 'balance',
                                className: 'text-nowrap text-end fw-semibold',
                                render: (data) => this.qty(data)
                            },
                            {
                                title: 'Petugas',
                                data: 'user_name',
                                className: 'text-nowrap',
                                render: (data) => this.escapeHtml(data)
                            },
                        ],
                    });
                }
            }

            window.service = new Service();

            // Error ajax tabel: tampilkan pesan umum (tanpa detail internal).
            service.table.on('error.dt', function() {
                app.alert('danger', 'Terjadi kesalahan saat mengambil data laporan. Silakan coba lagi.');
            });

            // Terapkan filter.
            service.form.submit(function(e) {
                e.preventDefault();
                service.apply();
            });

            // Reset filter.
            $('#buttonReset').click(function(e) {
                e.preventDefault();
                service.reset();
            });

            // Delegasi pada wrapper tabel karena kolom aksi tetap (fixed columns) berupa klon di luar elemen tabel asli.
            $(document).on('click', '#stockReportTable_wrapper button[data-action="detail"]', function() {
                service.openDetail($(this).attr('data-id'));
            });

            // Halaman siap: tampilkan data awal sesuai filter default atau query string.
            $(document).ready(function() {
                service.apply();
            });
        </script>
    </x-slot:script>
</x-layouts.auth>
