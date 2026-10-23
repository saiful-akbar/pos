<x-layouts.auth title="Stok Bulanan" :access="$access">
    {{-- Header halaman: judul, ikon, dan tombol pilih stok harian (hanya untuk user dengan akses create) --}}
    <x-page-header title="Stok Bulanan" icon="calendar_month">
        <x-slot:action>
            <div class="row justify-content-end">
                @if ($access?->create ?? false)
                    <div class="col-12 col-lg-6 col-xl-4">
                        <x-button type="button" id="buttonSelectDaily" startIcon="event" fullWidth>
                            Pilih Stok Harian
                        </x-button>
                    </div>
                @endif
            </div>
        </x-slot:action>
    </x-page-header>

    {{-- Card form filter data stok bulanan berdasarkan rentang tanggal --}}
    <div class="row gx-7">
        <div class="col-12 mt-7">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h2 class="fs-5 mb-0">Filter Data Stok</h2>
                </div>

                <div class="card-body">
                    <form id="formMonthlyStocks" autocomplete="off">
                        <div class="row g-3 align-items-end">
                            {{-- Tahun (default tahun saat ini, maksimal tahun saat ini) --}}
                            <div class="col-12 col-lg-4 col-xl-5">
                                <x-form-input type="number" name="year" id="filterYear" label="Tahun"
                                    value="{{ request('year', date('Y')) }}" min="2000" max="{{ date('Y') }}" required />
                            </div>

                            {{-- Bulan (default semua bulan dalam setahun) --}}
                            <div class="col-12 col-lg-4 col-xl-5 mt-6 mt-lg-0">
                                <x-form-select name="month" id="filterMonth" label="Bulan">
                                    <option value="">Semua</option>
                                    @foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $monthName)
                                        <option value="{{ $index + 1 }}" @selected((int) request('month') === $index + 1)>{{ $monthName }}</option>
                                    @endforeach
                                </x-form-select>
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

    {{-- Card daftar hasil filter stok bulanan (DataTables) --}}
    <div class="row gx-7">
        <div class="col-12 mt-7">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h2 class="fs-5 mb-0">Daftar Stok Bulanan</h2>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="monthlyStocksTable" class="table table-sm table-hover align-middle w-100"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal detail stok bulanan (daftar produk dan unit) dengan tombol export excel pada header --}}
    <x-modal id="modalDetail" title="Detail Stok" size="xl" fullScreen="md">
        <x-slot:headerAction>
            {{-- Ringkasan stok (periode dan jumlah produk) di kiri, tombol export excel di kanan --}}
            <div class="d-flex align-items-center gap-3 ms-auto ps-3">
                <span id="detailSummary" class="badge border fw-normal text-wrap d-none d-md-inline"></span>

                @if ($access?->update ?? false)
                    {{-- Tombol aksi (hanya ikon) untuk user dengan akses update --}}
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

    {{-- Modal pilih stok harian untuk dijadikan stok bulanan (hanya untuk user dengan akses create) --}}
    @if ($access?->create ?? false)
        <form action="#" method="post" id="formSelectDaily" autocomplete="off">
            <x-modal id="modalSelectDaily" title="Pilih Stok Harian" fullScreen="sm">
                {{-- Tanggal stok harian yang dicari --}}
                <div class="mb-5">
                    <x-form-input type="text" name="date" id="selectDate" label="Tanggal Stok Harian"
                        placeholder="Pilih tanggal" data-flatpickr="" required />
                </div>

                {{-- Tahun stok bulanan (default tahun ini, maksimal tahun ini) --}}
                <div class="mb-5">
                    <x-form-input type="number" name="year" id="selectYear" label="Tahun" value="{{ date('Y') }}"
                        min="2000" max="{{ date('Y') }}" required />
                </div>

                {{-- Bulan stok bulanan (default bulan ini) --}}
                <x-form-select name="month" id="selectMonth" label="Bulan" required>
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

    {{-- JavaScript --}}
    <x-slot:script>
        <script>
            "use strict"

            class Service {
                constructor() {
                    this.access = this.#getAccess();
                    this.form = $('#formMonthlyStocks');
                    this.year = $('#filterYear');
                    this.month = $('#filterMonth');
                    this.buttonFilter = $('#buttonFilter');
                    this.buttonSelectDaily = $('#buttonSelectDaily');
                    this.modalDetail = new bootstrap.Modal('#modalDetail');
                    this.detailTable = null;
                    this.detailUrl = null;
                    this.detailId = null;
                    this.formSelectDaily = $('#formSelectDaily');
                    this.modalSelectDaily = $('#modalSelectDaily').length > 0 ? new bootstrap.Modal('#modalSelectDaily', { backdrop: 'static' }) : null;
                    this.table = this.#monthlyStocksDataTableInit();
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
                 * Mengubah karakter HTML dari data server agar tidak dirender sebagai markup.
                 */
                #escapeHtml(value) {
                    return $('<div>').text(value ?? '').html();
                }

                /**
                 * Membuat stok bulanan dari stok harian pada tanggal yang dipilih (ajax).
                 */
                store(data) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'post',
                            url: @json(route('stocks.monthly.store')),
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
                 * Konfirmasi lalu jadikan stok bulanan sebagai stok aktif produk (dipakai menu tabel dan tombol header modal detail).
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
                 * Menjadikan stok bulanan sebagai stok aktif produk (ajax).
                 */
                activate(id) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'patch',
                            url: app.url(`/stocks/monthly/${id}/activate`),
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
                 * Menghapus permanen stok bulanan (ajax).
                 */
                destroy(id) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'post',
                            url: app.url(`/stocks/monthly/${id}`),
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
                 * Inisialisasi DataTables stok bulanan (kolom sesuai tabel monthly_stocks).
                 */
                #monthlyStocksDataTableInit() {
                    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                    const columns = [
                        {
                            title: 'Tahun',
                            name: 'monthly_stocks.year',
                            data: 'year',
                            className: 'text-nowrap',
                        },
                        {
                            title: 'Bulan',
                            name: 'monthly_stocks.month',
                            data: 'month',
                            className: 'text-nowrap',
                            render: (data) => monthNames[Number(data) - 1] ?? '-',
                        },
                        {
                            title: 'Tanggal Stok Harian',
                            name: 'daily_stocks.date',
                            data: 'daily_stock_date',
                            className: 'text-nowrap',
                            render: (data) => this.#escapeHtml(data ?? '-'),
                        },
                        {
                            title: 'Dibuat Oleh',
                            name: 'users.full_name',
                            data: 'user_name',
                            className: 'text-nowrap',
                            render: (data) => this.#escapeHtml(data ?? '-'),
                        },
                        {
                            title: 'Total Produk',
                            name: 'monthly_stocks.total_product',
                            data: 'total_product',
                            className: 'text-nowrap',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                        {
                            title: 'Total Qty',
                            name: 'monthly_stocks.total_quantity',
                            data: 'total_quantity',
                            className: 'text-nowrap',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                        {
                            title: 'Catatan',
                            name: 'monthly_stocks.notes',
                            data: 'notes',
                            render: (data) => this.#escapeHtml(data ?? '-'),
                        },
                        {
                            // Kolom aksi: tombol yang membuka dropdown menu.
                            title: app.icon('settings', 'fs-6'),
                            name: 'monthly_stocks.id',
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

                                // Menu aktifkan stok hanya untuk user dengan akses update.
                                if (this.access.update) {
                                    items += `
                                        <li>
                                            <a class="dropdown-item" href="#" data-action="activate" data-id="${id}">
                                                Jadikan sebagai stok aktif produk
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

                    return app.dataTable('#monthlyStocksTable', {
                        columns,
                        serverSide: true,
                        ajax: {
                            url: @json(route('stocks.monthly')),
                            // Kirim nilai filter terbaru pada setiap request.
                            data: (params) => {
                                params.year = this.year.val();
                                params.month = this.month.val();
                            },
                        },
                        order: [[1, 'asc']],
                    });
                }
            }

            window.service = new Service();

            /**
             * Daftarkan dropdown menu aksi setiap tabel selesai digambar. Strategi 'fixed' agar menu tidak
             * terpotong oleh area scroll tabel.
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
             * Menu "Hapus stok": konfirmasi lalu hapus permanen via ajax.
             */
            service.table.on('click', '.dropdown-item[data-action="remove"]', function (e) {
                e.preventDefault();

                const id = $(this).data('id');

                app.confirm(
                    'Hapus Stok Bulanan',
                    'Yakin ingin menghapus permanen stok bulanan ini beserta detailnya? Data tidak dapat dikembalikan.',
                    async (confirmed) => {
                        if (!confirmed) return;

                        app.clearAlert();
                        app.showPreloader();

                        try {
                            const response = await service.destroy(id);
                            app.alert('success', response.message);

                            // Muat ulang tabel agar baris yang dihapus hilang.
                            service.table.ajax.reload();
                        } catch (error) {
                            app.alert('danger', error?.message ?? 'Terjadi kesalahan saat menghapus stok bulanan.');
                        } finally {
                            app.hidePreloader();
                        }
                    },
                );
            });

            /**
             * Menu "Lihat detail": buka modal lalu muat daftar detail stok (DataTables server side).
             */
            service.table.on('click', '.dropdown-item[data-action="detail"]', function (e) {
                e.preventDefault();

                const id = $(this).data('id');
                service.detailUrl = app.url(`/stocks/monthly/${id}/details`);
                service.detailId = id;

                // Ringkasan pada header modal: periode dan jumlah produk (dari baris tabel).
                const row = service.table.row($(this).closest('tr')).data();
                const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                $('#detailSummary').text(
                    row ? `${monthNames[Number(row.month) - 1] ?? '-'} ${row.year} • ${app.numberFormat(Number(row.total_product ?? 0))} produk` : ''
                );

                // Arahkan tombol export excel ke stok yang dipilih.
                $('#buttonExportDetail').attr('href', app.url(`/stocks/monthly/${id}/details/export`));

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
                    scrollX: true,
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
                            width: '300px',
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
                            name: 'monthly_stock_details.quantity',
                            data: 'quantity',
                            type: 'num',
                            className: 'text-nowrap text-end',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                        {
                            title: 'Kelipatan Qty',
                            name: 'monthly_stock_details.multiples_quantity',
                            data: 'multiples_quantity',
                            type: 'num',
                            className: 'text-nowrap text-end',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                    ],
                });
            });

            /**
             * Submit form filter: muat ulang tabel server side berdasarkan tahun dan bulan.
             */
            service.form.submit(function (e) {
                e.preventDefault();

                app.clearAlert();

                // Perbarui query string URL sesuai filter lalu muat ulang tabel.
                app.updateQueryParams({
                    year: service.year.val(),
                    month: service.month.val(),
                });

                service.table.ajax.reload();
            });

            /**
             * Buka modal pilih stok harian.
             */
            service.buttonSelectDaily.click(function (e) {
                e.preventDefault();

                app.clearFormErrors(service.formSelectDaily);
                service.modalSelectDaily?.show();
            });

            /**
             * Submit form pilih stok harian via ajax.
             */
            service.formSelectDaily.submit(async function (e) {
                e.preventDefault();

                app.clearAlert();
                app.clearFormErrors(service.formSelectDaily);
                app.showPreloader();

                try {
                    const response = await service.store({
                        date: service.formSelectDaily.find('[name="date"]').val(),
                        year: service.formSelectDaily.find('[name="year"]').val(),
                        month: service.formSelectDaily.find('[name="month"]').val(),
                    });

                    // Berhasil: tutup modal, reset tanggal, lalu tampilkan notifikasi.
                    service.modalSelectDaily?.hide();
                    service.formSelectDaily.find('[name="date"]').val('');
                    app.alert('success', response.message);

                    // Muat ulang tabel agar stok bulanan baru langsung tampil.
                    service.table.ajax.reload();
                } catch (error) {
                    // Error validasi (422): tampilkan pada field terkait di dalam modal.
                    if (error?.status === 422 && error?.errors) {
                        app.setFormErrors(service.formSelectDaily, error);
                        return;
                    }

                    service.modalSelectDaily?.hide();
                    app.alert('danger', error?.message ?? 'Terjadi kesalahan saat membuat stok bulanan.');
                } finally {
                    app.hidePreloader();
                }
            });

            service.table.on('click', '.dropdown-item[data-action]', function (e) {
                e.preventDefault();
            });
        </script>
    </x-slot:script>
</x-layouts.auth>
