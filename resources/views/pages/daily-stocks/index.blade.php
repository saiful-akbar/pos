<x-layouts.auth title="Stok Harian" :access="$access">
    {{-- Header halaman: judul, ikon, dan tombol import excel (hanya untuk user dengan akses create) --}}
    <x-page-header title="Stok Harian" icon="inventory_2">
        <x-slot:action>
            <div class="row justify-content-end">
                @if ($access?->create ?? false)
                    <div class="col-12 col-lg-6 col-xl-4">
                        <x-button type="button" id="buttonImport" startIcon="upload" fullWidth>
                            Import Excel
                        </x-button>
                    </div>
                @endif
            </div>
        </x-slot:action>
    </x-page-header>

    {{-- Card form filter data stok berdasarkan rentang tanggal --}}
    <div class="row gx-7">
        <div class="col-12 mt-7">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h2 class="fs-5 mb-0">Filter Data Stok</h2>
                </div>

                <div class="card-body">
                    <form id="formDailyStocks" autocomplete="off">
                        <div class="row g-3 align-items-end">
                            {{-- Tanggal awal --}}
                            <div class="col-12 col-lg-4 col-xl-5">
                                <x-form-input type="text" name="start_date" id="startDate" label="Tanggal Awal"
                                    placeholder="Pilih tanggal" value="{{ request('start_date') }}" data-flatpickr="" required />
                            </div>

                            {{-- Tanggal akhir --}}
                            <div class="col-12 col-lg-4 col-xl-5 mt-6 mt-lg-0">
                                <x-form-input type="text" name="end_date" id="endDate" label="Tanggal Akhir"
                                    placeholder="Pilih tanggal" value="{{ request('end_date') }}" data-flatpickr="" required />
                            </div>

                            {{-- Tombol submit filter --}}
                            <div class="col-12 col-lg-4 col-xl-2 mt-6 mt-lg-0">
                                <x-button type="submit" id="buttonFilter" startIcon="filter_alt" fullWidth>
                                    Filter
                                </x-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Card daftar hasil filter stok (DataTables) --}}
    <div class="row gx-7">
        <div class="col-12 mt-7">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h2 class="fs-5 mb-0">Daftar Stok Harian</h2>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dailyStocksTable" class="table table-sm table-hover align-middle w-100"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal detail stok harian (daftar produk dan unit) dengan tombol export excel pada header --}}
    <x-modal id="modalDetail" title="Detail Stok Harian" size="xl" fullScreen="md">
        <x-slot:headerAction>
            {{-- Ringkasan stok (tanggal dan jumlah produk) di kiri, tombol export excel di kanan --}}
            <div class="d-flex align-items-center gap-3 ms-auto ps-3">
                <span id="detailSummary" class="badge border fw-normal text-wrap d-none d-md-inline"></span>

                @if ($access?->update ?? false)
                    {{-- Tombol aksi (hanya ikon) untuk user dengan akses update --}}
                    <x-button type="button" id="buttonMonthlyDetail" color="light" size="sm" class="border rounded"
                        title="Jadikan sebagai stok awal bulan">
                        <x-icon class="fs-6">event_available</x-icon>
                    </x-button>
                    <x-button type="button" id="buttonActivateDetail" color="light" size="sm" class="border rounded"
                        title="Jadikan sebagai stok aktif produk">
                        <x-icon class="fs-6">playlist_add_check</x-icon>
                    </x-button>
                @endif

                <x-button type="link" id="buttonExportDetail" href="#" color="secondary" size="sm"
                    class="rounded" title="Export Excel" aria-label="Export Excel">
                    <x-icon class="fs-6">download</x-icon>
                </x-button>
            </div>
        </x-slot:headerAction>

        <table id="detailTable" class="table table-sm table-hover align-middle w-100"></table>
    </x-modal>

    {{-- Modal jadikan stok harian sebagai stok awal bulan (hanya untuk user dengan akses update) --}}
    @if ($access?->update ?? false)
        <form action="#" method="post" id="formMonthly" autocomplete="off">
            <x-modal id="modalMonthly" title="Jadikan Sebagai Stok Awal Bulan" fullScreen="sm">
                {{-- Tahun (default tahun ini, maksimal tahun ini) --}}
                <div class="mb-5">
                    <x-form-input type="number" name="year" id="monthlyYear" label="Tahun" value="{{ date('Y') }}"
                        min="2000" max="{{ date('Y') }}" required />
                </div>

                {{-- Bulan (Januari - Desember, default bulan ini) --}}
                <x-form-select name="month" id="monthlyMonth" label="Bulan" required>
                    @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $monthName)
                        <option value="{{ $index + 1 }}" @selected($index + 1 === (int) date('n'))>{{ $monthName }}</option>
                    @endforeach
                </x-form-select>

                <x-slot:footer>
                    <div class="row justify-content-between">
                        <div class="col-6">
                            <x-button type="button" color="light" data-bs-dismiss="modal" startIcon="close" fullWidth>
                                Batal
                            </x-button>
                        </div>

                        <div class="col-6">
                            <x-button type="submit" color="secondary" startIcon="save" fullWidth>
                                Simpan
                            </x-button>
                        </div>
                    </div>
                </x-slot:footer>
            </x-modal>
        </form>
    @endif

    {{-- Modal import excel stok harian (hanya untuk user dengan akses create) --}}
    @if ($access?->create ?? false)
        <form action="{{ route('stocks.daily.import') }}" method="post" id="formImport" enctype="multipart/form-data" autocomplete="off">
            @csrf

            <x-modal id="modalImport" title="Import Excel" fullScreen="sm">
                {{-- Tombol unduh template excel --}}
                <x-button type="link" href="{{ route('stocks.daily.import.template') }}" startIcon="download"
                    color="secondary" fullWidth>
                    Unduh Template
                </x-button>

                <hr class="my-5">

                {{-- Tanggal stok opname (default hari ini, wajib diisi) --}}
                <div class="mb-5">
                    <x-form-input type="text" name="date" id="importDate" label="Tanggal Stok Opname"
                        value="{{ old('date', date('Y-m-d')) }}" :error="$errors->has('date')" data-flatpickr="" required />
                </div>

                {{-- File import (.xlsx, .xls, .csv) --}}
                <div class="mb-5">
                    <x-form-input type="file" name="file" id="file" label="File Excel"
                        accept=".xlsx,.xls,.csv" :error="$errors->has('file')" required />
                </div>

                {{-- Catatan (opsional) --}}
                <x-form-input type="textarea" name="notes" id="importNotes" label="Catatan (opsional)"
                    placeholder="Masukan catatan..." :error="$errors->has('notes')" />

                <x-slot:footer>
                    <div class="row justify-content-between">
                        <div class="col-6">
                            <x-button type="button" color="light" data-bs-dismiss="modal" startIcon="close" fullWidth>
                                Batal
                            </x-button>
                        </div>

                        <div class="col-6">
                            <x-button type="submit" color="secondary" startIcon="save" fullWidth>
                                Simpan
                            </x-button>
                        </div>
                    </div>
                </x-slot:footer>
            </x-modal>
        </form>
    @endif

    {{-- Modal import update stok harian (hanya untuk user dengan akses update) --}}
    @if ($access?->update ?? false)
        <form action="#" method="post" id="formImportUpdate" enctype="multipart/form-data" autocomplete="off">
            <x-modal id="modalImportUpdate" title="Import Update" fullScreen="sm">
                {{-- Tombol unduh template excel (sama dengan template import) --}}
                <x-button type="link" href="{{ route('stocks.daily.import.template') }}" startIcon="download"
                    color="secondary" fullWidth>
                    Unduh Template
                </x-button>

                <hr class="my-5">

                {{-- Tanggal stok harian (hanya informasi, tidak diubah dan tidak dikirim) --}}
                <div class="mb-5">
                    <x-form-input type="text" name="date" id="importUpdateDate" label="Tanggal Stok Opname" disabled />
                </div>

                {{-- File import (.xlsx, .xls, .csv) --}}
                <div class="mb-5">
                    <x-form-input type="file" name="file" id="importUpdateFile" label="File Excel"
                        accept=".xlsx,.xls,.csv" required />
                </div>

                {{-- Catatan (opsional, default dari catatan stok harian) --}}
                <x-form-input type="textarea" name="notes" id="importUpdateNotes" label="Catatan (opsional)"
                    placeholder="Masukan catatan..." />

                <x-slot:footer>
                    <div class="row justify-content-between">
                        <div class="col-6">
                            <x-button type="button" color="light" data-bs-dismiss="modal" startIcon="close" fullWidth>
                                Batal
                            </x-button>
                        </div>

                        <div class="col-6">
                            <x-button type="submit" color="secondary" startIcon="save" fullWidth>
                                Simpan
                            </x-button>
                        </div>
                    </div>
                </x-slot:footer>
            </x-modal>
        </form>
    @endif

    {{-- JavaScript --}}
    <x-slot:script>
        <script>
            "use strict"

            class Service {
                constructor() {
                    this.access = this.#getAccess();
                    this.form = $('#formDailyStocks');
                    this.startDate = $('#startDate');
                    this.endDate = $('#endDate');
                    this.buttonFilter = $('#buttonFilter');
                    this.buttonImport = $('#buttonImport');
                    this.formImport = $('#formImport');
                    this.modalDetail = new bootstrap.Modal('#modalDetail');
                    this.formMonthly = $('#formMonthly');
                    this.formImportUpdate = $('#formImportUpdate');
                    this.modalImportUpdate = $('#modalImportUpdate').length > 0 ? new bootstrap.Modal('#modalImportUpdate', { backdrop: 'static' }) : null;
                    this.importUpdateDailyStockId = null;
                    this.modalMonthly = $('#modalMonthly').length > 0 ? new bootstrap.Modal('#modalMonthly', { backdrop: 'static' }) : null;
                    this.monthlyDailyStockId = null;
                    this.detailTable = null;
                    this.detailId = null;
                    this.importDate = $('#importDate');
                    this.modalImport = this.#modalImportInit();
                    this.table = this.#dailyStocksDataTableInit();
                }

                /**
                 * Mengambil hak akses user dari atribut <html>.
                 */
                #getAccess() {
                    return {
                        read: $('html').data('read') === 1,
                        create: $('html').data('create') === 1,
                        update: $('html').data('update') === 1,
                        remove: $('html').data('remove') === 1,
                        delete: $('html').data('delete') === 1,
                    }
                }

                /**
                 * Inisialisasi modal import excel (null jika user tidak punya akses create).
                 */
                #modalImportInit() {
                    if ($('#modalImport').length === 0) {
                        return null;
                    }

                    return new bootstrap.Modal('#modalImport', {
                        backdrop: 'static',
                    });
                }

                /**
                 * Mengubah karakter HTML dari data server agar tidak dirender sebagai markup.
                 */
                #escapeHtml(value) {
                    return $('<div>').text(value ?? '').html();
                }

                /**
                 * Inisialisasi DataTables stok harian (kolom sesuai tabel daily_stocks).
                 */
                #dailyStocksDataTableInit() {
                    const columns = [
                        {
                            title: 'Tanggal',
                            name: 'date',
                            data: 'date',
                            className: 'text-nowrap',
                        },
                        {
                            title: 'Total Produk',
                            name: 'total_product',
                            data: 'total_product',
                            className: 'text-nowrap',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                        {
                            title: 'Total Qty',
                            name: 'total_quantity',
                            data: 'total_quantity',
                            className: 'text-nowrap',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                        {
                            title: 'Catatan',
                            name: 'notes',
                            data: 'notes',
                            render: (data) => this.#escapeHtml(data ?? '-'),
                        },
                        {
                            // Kolom aksi: tombol yang membuka dropdown menu.
                            title: app.icon('settings', 'fs-6'),
                            name: 'id',
                            data: 'id',
                            className: 'text-nowrap text-center no-export',
                            type: 'html',
                            searchable: false,
                            orderable: false,
                            render: (data) => {
                                const id = this.#escapeHtml(data);
                                let items = '';

                                // Menu lihat detail tersedia untuk semua user yang dapat membuka halaman ini.
                                items += `
                                    <li>
                                        <a class="dropdown-item" href="#" data-action="detail" data-id="${id}">
                                            Lihat detail
                                        </a>
                                    </li>
                                `;

                                // Menu yang mengubah data hanya untuk user dengan akses update.
                                if (this.access.update) {
                                    items += `
                                        <li>
                                            <a class="dropdown-item" href="#" data-action="activate" data-id="${id}">
                                                Jadikan sebagai stok aktif produk
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" data-action="set-month-start" data-id="${id}">
                                                Jadikan sebagai stok awal bulan
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" data-action="import-update" data-id="${id}">
                                                Import update stok
                                            </a>
                                        </li>
                                    `;
                                }

                                // Menu hapus hanya untuk user dengan akses remove.
                                if (this.access.remove) {
                                    items += `
                                        <li>
                                            <a class="dropdown-item text-danger" href="#" data-action="remove" data-id="${id}">
                                                Hapus stok
                                            </a>
                                        </li>
                                    `;
                                }

                                if (!items) {
                                    return '-';
                                }

                                return `
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-light rounded" data-bs-toggle="dropdown" data-action="menu" aria-expanded="false" title="Aksi" aria-label="Aksi">
                                            ${app.icon('more_vert', 'fs-6')}
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border">${items}</ul>
                                    </div>
                                `;
                            },
                        },
                    ];

                    return app.dataTable('#dailyStocksTable', {
                        columns,
                        order: [[0, 'desc']],
                    });
                }

                /**
                 * Tanggal hari ini (YYYY-MM-DD) menurut waktu lokal browser.
                 */
                today() {
                    const now = new Date();

                    return [
                        now.getFullYear(),
                        String(now.getMonth() + 1).padStart(2, '0'),
                        String(now.getDate()).padStart(2, '0'),
                    ].join('-');
                }

                /**
                 * Konfirmasi lalu jadikan stok harian sebagai stok aktif produk (dipakai menu tabel dan tombol header modal detail).
                 */
                confirmActivate(id) {
                    app.confirm(
                        'Stok Aktif Produk',
                        'Yakin ingin menjadikan stok ini sebagai stok aktif produk? Stok produk saat ini akan diganti.',
                        async (confirmed) => {
                            if (!confirmed) return;

                            app.clearAlert();
                            app.showPreloader();

                            try {
                                const response = await this.activate(id);
                                app.alert('success', response.message);
                            } catch (error) {
                                app.alert('danger', error?.message ?? 'Terjadi kesalahan saat memperbarui stok produk.');
                            } finally {
                                app.hidePreloader();
                            }
                        },
                    );
                }

                /**
                 * Menjadikan stok harian sebagai stok aktif produk (ajax).
                 */
                activate(id) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'patch',
                            url: app.url(`/stocks/daily/${id}/activate`),
                            dataType: 'json',
                            data: {
                                _token: app.csrfToken,
                                _method: 'patch',
                            },
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
                 * Menjadikan stok harian sebagai stok awal bulan (ajax).
                 */
                storeMonthly(id, data) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'post',
                            url: app.url(`/stocks/daily/${id}/monthly`),
                            dataType: 'json',
                            data: { ...data, _token: app.csrfToken },
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
                 * Mengirim file import excel stok harian (ajax, multipart).
                 */
                importExcel(formData) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'post',
                            url: @json(route('stocks.daily.import')),
                            data: formData,
                            processData: false,
                            contentType: false,
                            dataType: 'json',
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
                 * Menghapus permanen stok harian (ajax).
                 */
                destroy(id) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'post',
                            url: app.url(`/stocks/daily/${id}`),
                            dataType: 'json',
                            data: {
                                _token: app.csrfToken,
                                _method: 'delete',
                            },
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
                 * Mengirim file import update stok harian (ajax, multipart).
                 */
                importUpdate(id, formData) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'post',
                            url: app.url(`/stocks/daily/${id}/import-update`),
                            data: formData,
                            processData: false,
                            contentType: false,
                            dataType: 'json',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': app.csrfToken,
                            },
                            success: (response) => resolve(response),
                            error: (xhr) => reject({ ...xhr.responseJSON, status: xhr.status }),
                        });
                    });
                }

                /**
                 * Mengambil data stok harian berdasarkan rentang tanggal (ajax).
                 */
                getDailyStocks(startDate, endDate) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'get',
                            url: @json(route('stocks.daily')),
                            dataType: 'json',
                            data: {
                                start_date: startDate,
                                end_date: endDate,
                            },
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }

                /**
                 * Mengambil data lalu menampilkannya pada DataTables.
                 */
                async load() {
                    const startDate = this.startDate.val();
                    const endDate = this.endDate.val();

                    app.clearFormErrors(this.form);

                    // Validasi sisi klien sebelum request.
                    if (!startDate || !endDate) {
                        app.alert('warning', 'Tanggal awal dan tanggal akhir wajib diisi.');
                        return;
                    }

                    if (endDate < startDate) {
                        app.alert('warning', 'Tanggal akhir tidak boleh lebih awal dari tanggal awal.');
                        return;
                    }

                    // Perbarui query string URL secara realtime sesuai nilai filter.
                    app.updateQueryParams({
                        start_date: startDate,
                        end_date: endDate,
                    });

                    this.buttonFilter.prop('disabled', true);
                    app.showPreloader();

                    try {
                        const { data } = await this.getDailyStocks(startDate, endDate);

                        if (!Array.isArray(data)) {
                            this.table.clear().draw();
                            app.alert('danger', 'Format respons data stok harian tidak valid.');
                            return;
                        }

                        // Ganti isi tabel dengan hasil filter.
                        this.table.clear();
                        this.table.rows.add(data);
                        this.table.draw();
                    } catch (error) {
                        // Error validasi (422): tampilkan pada form.
                        if (error?.status === 422 && error?.errors) {
                            app.setFormErrors(this.form, error);
                            app.alert('danger', 'Periksa kembali rentang tanggal yang Anda masukkan.');
                            return;
                        }

                        app.alert('danger', error?.message ?? 'Terjadi kesalahan saat mengambil data stok harian.');
                    } finally {
                        this.buttonFilter.prop('disabled', false);
                        app.hidePreloader();
                    }
                }
            }

            window.service = new Service();

            /**
             * Submit form filter: ambil data stok harian.
             */
            service.form.submit(function (e) {
                e.preventDefault();
                service.load();
            });

            /**
             * Daftarkan dropdown menu aksi setiap tabel selesai digambar. Strategi 'fixed' agar menu tidak
             * terpotong oleh area scroll tabel; buka/tutup (termasuk klik di luar atau klik item) dikelola
             * oleh Bootstrap melalui data-bs-toggle="dropdown".
             */
            service.table.on('draw', function () {
                service.table.table().node().querySelectorAll('[data-action="menu"]').forEach((button) => {
                    bootstrap.Dropdown.getOrCreateInstance(button, {
                        popperConfig: (config) => ({ ...config, strategy: 'fixed' }),
                    });
                });
            });

            /**
             * Menu "Jadikan sebagai stok aktif produk": konfirmasi lalu kirim request ajax.
             */
            service.table.on('click', '.dropdown-item[data-action="activate"]', function (e) {
                e.preventDefault();

                service.confirmActivate($(this).data('id'));
            });

            /**
             * Tombol "stok aktif produk" pada header modal detail.
             */
            $('#buttonActivateDetail').click(function (e) {
                e.preventDefault();

                service.confirmActivate(service.detailId);
            });

            /**
             * Menu "Lihat detail": buka modal lalu muat daftar detail stok (DataTables server side).
             */
            service.table.on('click', '.dropdown-item[data-action="detail"]', function (e) {
                e.preventDefault();

                const id = $(this).data('id');
                service.detailUrl = app.url(`/stocks/daily/${id}/details`);
                service.detailId = id;

                // Ringkasan pada header modal: tanggal stok dan jumlah produk (dari baris tabel).
                const row = service.table.row($(this).closest('tr')).data();
                $('#detailSummary').text(
                    row ? `${row.date} • ${app.numberFormat(Number(row.total_product ?? 0))} produk` : ''
                );

                // Arahkan tombol export excel ke stok yang dipilih.
                $('#buttonExportDetail').attr('href', app.url(`/stocks/daily/${id}/details/export`));

                service.modalDetail.show();
            });

            /**
             * Tabel detail dibuat saat modal tampil (agar lebar kolom benar); pembukaan berikutnya
             * cukup mengganti URL sumber data lalu memuat ulang.
             */
            $('#modalDetail').on('shown.bs.modal', function () {
                if (service.detailTable !== null) {
                    service.detailTable.ajax.url(service.detailUrl).load();
                    return;
                }

                service.detailTable = app.modalDataTable('#detailTable', {
                    serverSide: true,
                    scrollX: false,
                    ajax: service.detailUrl,
                    order: [[0, 'asc']],
                    columns: [
                        {
                            title: 'Kode Produk',
                            name: 'products.code',
                            data: 'product_code',
                            type: 'string',
                            className: 'text-nowrap',
                        },
                        {
                            title: 'Nama (Deskripsi)',
                            name: 'products.name',
                            data: 'product_name',
                            type: 'string',
                            width: '250px',
                        },
                        {
                            title: 'Unit (Satuan)',
                            name: 'units.name',
                            data: 'unit_name',
                            type: 'string',
                            className: 'text-nowrap',
                            render: (data, type, row) => `${row.unit_name} (${row.unit_code})`,
                        },
                        {
                            title: 'Qty',
                            name: 'daily_stock_details.quantity',
                            data: 'quantity',
                            type: 'num',
                            className: 'text-nowrap text-end',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                        {
                            title: 'Kelipatan Qty',
                            name: 'daily_stock_details.multiples_quantity',
                            data: 'multiples_quantity',
                            type: 'num',
                            className: 'text-nowrap text-end',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                    ],
                });
            });

            /**
             * Tombol "stok awal bulan" pada header modal detail: buka form tahun dan bulan di atas modal detail.
             */
            $('#buttonMonthlyDetail').click(function (e) {
                e.preventDefault();

                service.monthlyDailyStockId = service.detailId;
                app.clearFormErrors(service.formMonthly);
                service.modalMonthly?.show();
            });

            /**
             * Menu "Jadikan sebagai stok awal bulan": simpan id stok lalu buka modal pilih tahun dan bulan.
             */
            service.table.on('click', '.dropdown-item[data-action="set-month-start"]', function (e) {
                e.preventDefault();

                service.monthlyDailyStockId = $(this).data('id');
                app.clearFormErrors(service.formMonthly);
                service.modalMonthly?.show();
            });

            /**
             * Submit form stok awal bulan via ajax.
             */
            service.formMonthly.submit(async function (e) {
                e.preventDefault();

                app.clearAlert();
                app.clearFormErrors(service.formMonthly);
                app.showPreloader();

                try {
                    const response = await service.storeMonthly(service.monthlyDailyStockId, {
                        year: service.formMonthly.find('[name="year"]').val(),
                        month: service.formMonthly.find('[name="month"]').val(),
                    });

                    // Berhasil: tutup modal lalu tampilkan notifikasi.
                    service.modalMonthly?.hide();
                    app.alert('success', response.message);
                } catch (error) {
                    // Error validasi (422): tampilkan pada field terkait di dalam modal.
                    if (error?.status === 422 && error?.errors) {
                        app.setFormErrors(service.formMonthly, error);
                        return;
                    }

                    service.modalMonthly?.hide();
                    app.alert('danger', error?.message ?? 'Terjadi kesalahan saat menyimpan stok awal bulan.');
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Menu "Hapus stok": konfirmasi lalu hapus permanen via ajax.
             */
            service.table.on('click', '.dropdown-item[data-action="remove"]', function (e) {
                e.preventDefault();

                const id = $(this).data('id');

                app.confirm(
                    'Hapus Stok Harian',
                    'Yakin ingin menghapus permanen stok harian ini beserta detailnya? Data tidak dapat dikembalikan.',
                    async (confirmed) => {
                        if (!confirmed) return;

                        app.clearAlert();
                        app.showPreloader();

                        try {
                            const response = await service.destroy(id);
                            app.alert('success', response.message);

                            // Hapus baris dari tabel tanpa memuat ulang halaman.
                            service.load();
                        } catch (error) {
                            // Error validasi (422), mis. sudah dijadikan stok bulanan: tampilkan pesannya.
                            const message = error?.errors?.id?.[0] ?? error?.message ?? 'Terjadi kesalahan saat menghapus stok harian.';
                            app.alert('danger', message);
                        } finally {
                            app.hidePreloader();
                        }
                    },
                );
            });

            /**
             * Menu "Import update stok": isi tanggal dan catatan dari baris tabel lalu buka modal.
             */
            service.table.on('click', '.dropdown-item[data-action="import-update"]', function (e) {
                e.preventDefault();

                const row = service.table.row($(this).closest('tr')).data();

                service.importUpdateDailyStockId = $(this).data('id');
                service.formImportUpdate.trigger('reset');
                app.clearFormErrors(service.formImportUpdate);
                service.formImportUpdate.find('[name="date"]').val(row?.date ?? '');
                service.formImportUpdate.find('[name="notes"]').val(row?.notes ?? '');
                service.modalImportUpdate?.show();
            });

            /**
             * Submit form import update via ajax (tanpa reload halaman).
             */
            service.formImportUpdate.submit(async function (e) {
                e.preventDefault();

                app.clearAlert();
                app.clearFormErrors(service.formImportUpdate);
                app.showPreloader();

                try {
                    // Tanggal bersifat disabled sehingga tidak ikut terkirim.
                    const response = await service.importUpdate(service.importUpdateDailyStockId, new FormData(this));

                    // Berhasil: tutup modal, tampilkan notifikasi, lalu muat ulang tabel.
                    service.modalImportUpdate?.hide();
                    app.alert('success', response.message);
                    service.load();
                } catch (error) {
                    // Error validasi (422): tampilkan pada field terkait di dalam modal.
                    if (error?.status === 422 && error?.errors) {
                        app.setFormErrors(service.formImportUpdate, error);
                        return;
                    }

                    service.modalImportUpdate?.hide();
                    app.alert('danger', error?.message ?? 'Import update stok harian gagal.');
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Item menu aksi lainnya: logika masing-masing menu akan dihubungkan pada langkah berikutnya.
             */
            service.table.on('click', '.dropdown-item[data-action]', function (e) {
                e.preventDefault();
            });

            /**
             * Buka modal import excel.
             */
            service.buttonImport.click(function (e) {
                e.preventDefault();
                service.modalImport?.show();
            });

            /**
             * Submit form import excel via ajax (tanpa reload halaman).
             */
            service.formImport.submit(async function (e) {
                e.preventDefault();

                // Simpan tanggal yang diimport sebelum form di-reset.
                const importedDate = service.importDate.val();

                app.clearAlert();
                app.clearFormErrors(service.formImport);
                app.showPreloader();

                try {
                    const response = await service.importExcel(new FormData(this));

                    // Berhasil: tutup modal, reset form (tanggal kembali ke hari ini), lalu tampilkan notifikasi.
                    service.modalImport?.hide();
                    service.formImport.trigger('reset');
                    service.importDate.val(service.today());
                    app.alert('success', response.message);

                    // Tampilkan hasil import pada tanggal yang diimport (query string ikut diperbarui).
                    service.startDate.val(importedDate);
                    service.endDate.val(importedDate);
                    service.load();
                } catch (error) {
                    // Error validasi (422): tampilkan pada field terkait di dalam modal.
                    if (error?.status === 422 && error?.errors) {
                        app.setFormErrors(service.formImport, error);
                        return;
                    }

                    app.alert('danger', error?.message ?? 'Terjadi kesalahan saat import excel.');
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Saat halaman dimuat/direload: jika query string sudah berisi rentang tanggal,
             * langsung ambil data berdasarkan nilai tersebut.
             */
            $(document).ready(function () {
                if (service.startDate.val() && service.endDate.val()) {
                    service.load();
                }
            });

            /**
             * Navigasi Back/Forward browser: sinkronkan form dengan query string lalu ambil data.
             */
            window.addEventListener('popstate', function () {
                const params = new URLSearchParams(window.location.search);

                service.startDate.val(params.get('start_date') ?? '');
                service.endDate.val(params.get('end_date') ?? '');

                if (service.startDate.val() && service.endDate.val()) {
                    service.load();
                } else {
                    service.table.clear().draw();
                }
            });
        </script>
    </x-slot:script>
</x-layouts.auth>
