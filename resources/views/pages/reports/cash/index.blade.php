<x-layouts.auth title="Laporan Kas" :access="$access">
    {{-- Header halaman: judul, deskripsi, dan tombol export --}}
    <x-page-header title="Laporan Kas" icon="payments">
        <x-slot:action>
            <div class="d-flex justify-content-lg-end gap-2">
                <x-button type="link" id="buttonExportExcel" href="#" color="light" startIcon="download" class="border">
                    Excel
                </x-button>

                <x-button type="link" id="buttonExportPdf" href="#" target="_blank" color="light" startIcon="picture_as_pdf" class="border">
                    PDF
                </x-button>
            </div>
        </x-slot:action>

        <p class="text-body-secondary mt-4 mb-0">
            Laporan transaksi kas, rekap mutasi bulanan, dan rekonsiliasi saldo kas. Excel memuat ketiga laporan; PDF mengikuti tab yang dibuka.
        </p>
    </x-page-header>

    {{-- Card filter --}}
    <div class="row gx-7">
        <div class="col-12 mt-7">
            <div class="card shadow-sm">
                <div class="card-header py-4">
                    <h2 class="fs-5 mb-0">Filter Laporan</h2>
                    <p class="text-body-secondary mt-1 mb-0">Rekap bulanan dan rekonsiliasi menampilkan bulan penuh yang tercakup periode (maksimal 36 bulan).</p>
                </div>

                <form id="formReport" autocomplete="off">
                    <div class="card-body pb-0">
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

                            <div class="col-12 col-md-6 col-xl-3 mb-5">
                                <x-form-select name="type" id="filterType" label="Jenis Transaksi">
                                    <option value="">Semua</option>
                                    <option value="INCOMING" @selected(request('type') === 'INCOMING')>Kas Masuk</option>
                                    <option value="OUTGOING" @selected(request('type') === 'OUTGOING')>Kas Keluar</option>
                                </x-form-select>
                            </div>

                            <div class="col-12 col-md-6 col-xl-3 mb-5">
                                <x-form-select name="user_id" id="filterUser" label="Pencatat">
                                    <option value="">Semua</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user['id'] }}" @selected(request('user_id') === $user['id'])>{{ $user['name'] }}</option>
                                    @endforeach
                                </x-form-select>
                            </div>
                        </div>
                    </div>

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

    {{-- Total transaksi pada periode --}}
    <div class="row gx-7">
        @php
            $cards = [
                ['id' => 'totalIncoming', 'label' => 'Total Kas Masuk', 'icon' => 'south_west', 'color' => 'success'],
                ['id' => 'totalOutgoing', 'label' => 'Total Kas Keluar', 'icon' => 'north_east', 'color' => 'danger'],
                ['id' => 'totalNet', 'label' => 'Mutasi Bersih', 'icon' => 'balance', 'color' => 'primary'],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="col-12 col-md-4 mt-7">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-start justify-content-between gap-3">
                        <div style="min-width: 0;">
                            <div class="text-body-secondary small fw-semibold text-uppercase">{{ $card['label'] }}</div>
                            <div class="fs-3 fw-bold text-break mt-2" id="{{ $card['id'] }}">Rp 0.00</div>
                            <div class="small text-body-secondary mt-1">Sesuai filter aktif</div>
                        </div>

                        <div class="rounded-3 bg-{{ $card['color'] }} bg-opacity-10 text-body d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width: 3rem; height: 3rem;">
                            <x-icon class="fs-4">{{ $card['icon'] }}</x-icon>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Tiga laporan dalam tab --}}
    <div class="row gx-7">
        <div class="col-12 mt-7 mb-7">
            <div class="card shadow-sm">
                <div class="card-header pb-0 pt-4">
                    <ul class="nav nav-tabs card-header-tabs border-bottom-0" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tabTransactions" data-bs-toggle="tab" data-bs-target="#paneTransactions" data-report="transactions" type="button" role="tab">
                                Transaksi Kas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tabMonthly" data-bs-toggle="tab" data-bs-target="#paneMonthly" data-report="monthly" type="button" role="tab">
                                Rekap Mutasi Bulanan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tabReconciliation" data-bs-toggle="tab" data-bs-target="#paneReconciliation" data-report="reconciliation" type="button" role="tab">
                                Rekonsiliasi Bulanan
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="paneTransactions" role="tabpanel">
                            <table id="transactionsTable" class="table table-sm table-hover align-middle w-100"></table>
                        </div>

                        <div class="tab-pane fade" id="paneMonthly" role="tabpanel">
                            <table id="monthlyTable" class="table table-sm table-hover align-middle w-100"></table>
                        </div>

                        <div class="tab-pane fade" id="paneReconciliation" role="tabpanel">
                            <table id="reconciliationTable" class="table table-sm table-hover align-middle w-100"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    <x-slot:script>
        <script>
            "use strict"

            // Error ajax DataTables ditangani sendiri (tidak memakai alert bawaan).
            DataTable.ext.errMode = 'none';

            class Service {
                constructor() {
                    this.form = $('#formReport');
                    this.startDate = $('#startDate');
                    this.endDate = $('#endDate');
                    this.buttonFilter = $('#buttonFilter');
                    this.monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    this.report = 'transactions';
                    this.queryString = '';
                    this.statusMap = {
                        match: ['Sesuai', 'check_circle', 'text-bg-success'],
                        difference: ['Selisih', 'error', 'text-bg-danger'],
                        unreconciled: ['Belum direkonsiliasi', 'hourglass_empty', 'border text-body-secondary'],
                        incomplete: ['Data belum lengkap', 'help', 'border text-body-secondary'],
                    };
                    this.table = this.#transactionsTableInit();
                    this.monthlyTable = null;
                    this.reconciliationTable = null;
                }

                escapeHtml(value) {
                    return $('<div>').text(value ?? '').html();
                }

                rupiah(value) {
                    if (value === null || value === undefined) return '-';

                    const number = Number(value);
                    const text = `Rp ${app.numberFormat(Math.abs(number))}`;

                    return number < 0 ? `-${text}` : text;
                }

                signedRupiah(value) {
                    if (value === null || value === undefined) return '-';

                    return Number(value) > 0 ? `+${this.rupiah(value)}` : this.rupiah(value);
                }

                statusBadge(status) {
                    const [label, icon, classes] = this.statusMap[status] ?? ['-', 'help', 'border'];

                    return `<span class="badge ${classes} d-inline-flex align-items-center gap-1">${app.icon(icon, 'fs-6')}<span>${label}</span></span>`;
                }

                typeBadge(type) {
                    return type === 'INCOMING'
                        ? '<span class="badge text-bg-success">Kas Masuk</span>'
                        : '<span class="badge text-bg-danger">Kas Keluar</span>';
                }

                filters() {
                    const filters = {
                        start_date: this.startDate.val(),
                        end_date: this.endDate.val(),
                        type: $('#filterType').val(),
                        user_id: $('#filterUser').val(),
                    };

                    Object.keys(filters).forEach((key) => {
                        if (!filters[key]) delete filters[key];
                    });

                    return filters;
                }

                #transactionsTableInit() {
                    return app.dataTable('#transactionsTable', {
                        serverSide: true,
                        deferLoading: 0,
                        language: {
                            search: '',
                            searchPlaceholder: 'Cari nomor atau catatan...',
                            lengthMenu: '_MENU_',
                            info: '_START_ - _END_ dari _TOTAL_',
                            infoEmpty: '0 - 0 dari 0',
                            infoFiltered: '',
                            zeroRecords: 'Tidak ditemukan transaksi yang sesuai dengan pencarian.',
                            emptyTable: 'Tidak ada transaksi kas pada periode atau filter yang dipilih.',
                        },
                        columns: [
                            { title: 'Tanggal', name: 'cash_transactions.transaction_date', data: 'transaction_date', className: 'text-nowrap' },
                            { title: 'No. Transaksi', name: 'cash_transactions.transaction_number', data: 'transaction_number', className: 'text-nowrap fw-semibold', render: (data) => this.escapeHtml(data) },
                            { title: 'Jenis', name: 'cash_transactions.type', data: 'type', className: 'text-nowrap', searchable: false, render: (data) => this.typeBadge(data) },
                            { title: 'Nominal', name: 'cash_transactions.amount', data: 'amount', className: 'text-nowrap text-end fw-semibold', searchable: false, render: (data) => this.rupiah(data) },
                            { title: 'Catatan', name: 'cash_transactions.notes', data: 'notes', render: (data) => this.escapeHtml(data ?? '-') },
                            { title: 'Referensi', name: 'cash_transactions.reference_number', data: 'reference_number', className: 'text-nowrap', render: (data) => this.escapeHtml(data ?? '-') },
                            { title: 'Pencatat', name: 'users.full_name', data: 'user_name', className: 'text-nowrap', render: (data) => this.escapeHtml(data ?? '-') },
                        ],
                        ajax: {
                            url: @json(route('reports.cash')),
                            data: (params) => Object.assign(params, this.filters()),
                        },
                        order: [[0, 'desc'], [1, 'desc']],
                    });
                }

                request(url, data) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'get',
                            url,
                            dataType: 'json',
                            data,
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                            success: (response) => resolve(response),
                            error: (xhr) => reject({ ...xhr.responseJSON, status: xhr.status }),
                        });
                    });
                }

                /**
                 * Menampilkan rekap bulanan dan rekonsiliasi (tabel client side, data sudah dihitung di server).
                 */
                renderMonthly(rows) {
                    const period = (data, type, row) => `${this.monthNames[row.month - 1]} ${row.year}`;
                    const money = (data) => this.rupiah(data);
                    const language = { emptyTable: 'Tidak ada data pada periode yang dipilih.' };

                    [this.monthlyTable, this.reconciliationTable].forEach((table) => table?.destroy());
                    $('#monthlyTable, #reconciliationTable').empty();

                    this.monthlyTable = app.dataTable('#monthlyTable', {
                        data: rows,
                        ordering: false,
                        paging: false,
                        searching: false,
                        info: false,
                        layout: { topStart: null, topEnd: null, bottomStart: null, bottomEnd: null },
                        language,
                        columns: [
                            { title: 'Periode', data: 'month', className: 'text-nowrap fw-semibold', render: period },
                            { title: 'Saldo Awal', data: 'opening_balance', className: 'text-nowrap text-end', render: money },
                            { title: 'Kas Masuk', data: 'incoming', className: 'text-nowrap text-end', render: money },
                            { title: 'Kas Keluar', data: 'outgoing', className: 'text-nowrap text-end', render: money },
                            { title: 'Saldo Teoritis Akhir', data: 'theoretical_closing', className: 'text-nowrap text-end fw-semibold', render: money },
                        ],
                    });

                    this.reconciliationTable = app.dataTable('#reconciliationTable', {
                        data: rows,
                        ordering: false,
                        paging: false,
                        searching: false,
                        info: false,
                        layout: { topStart: null, topEnd: null, bottomStart: null, bottomEnd: null },
                        language,
                        columns: [
                            { title: 'Periode', data: 'month', className: 'text-nowrap fw-semibold', render: period },
                            { title: 'Saldo Awal', data: 'opening_balance', className: 'text-nowrap text-end', render: money },
                            { title: 'Kas Masuk', data: 'incoming', className: 'text-nowrap text-end', render: money },
                            { title: 'Kas Keluar', data: 'outgoing', className: 'text-nowrap text-end', render: money },
                            { title: 'Saldo Teoritis', data: 'theoretical_closing', className: 'text-nowrap text-end', render: money },
                            { title: 'Saldo Aktual Berikutnya', data: 'next_opening_balance', className: 'text-nowrap text-end', render: money },
                            { title: 'Selisih', data: 'difference', className: 'text-nowrap text-end fw-bold', render: (data) => this.signedRupiah(data) },
                            { title: 'Status', data: 'status', className: 'text-nowrap', render: (data) => this.statusBadge(data) },
                            { title: 'Catatan', data: 'notes', render: (data) => this.escapeHtml(data ?? '-') },
                        ],
                    });
                }

                /**
                 * Memperbarui tautan export sesuai filter dan tab aktif.
                 */
                updateExportLinks() {
                    $('#buttonExportExcel').attr('href', @json(route('reports.cash.export.excel')) + '?' + this.queryString);
                    $('#buttonExportPdf').attr('href', @json(route('reports.cash.export.pdf')) + '?' + this.queryString + '&report=' + this.report);
                }

                /**
                 * Terapkan filter: perbarui query string, total, ketiga laporan, dan tautan export.
                 */
                async apply() {
                    app.clearAlert();
                    app.clearFormErrors(this.form);

                    const filters = this.filters();

                    if (!filters.start_date || !filters.end_date) {
                        app.alert('danger', 'Tanggal mulai dan tanggal akhir wajib diisi.');
                        return;
                    }

                    if (filters.end_date < filters.start_date) {
                        app.alert('danger', 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.');
                        return;
                    }

                    this.queryString = new URLSearchParams(filters).toString();

                    const url = new URL(window.location);
                    url.search = this.queryString;
                    window.history.pushState({}, '', url);

                    this.updateExportLinks();

                    this.buttonFilter.prop('disabled', true);
                    app.showPreloader();

                    try {
                        const [totals, monthly] = await Promise.all([
                            this.request(@json(route('reports.cash.totals')), filters),
                            this.request(@json(route('reports.cash.monthly')), filters),
                            new Promise((resolve) => {
                                this.table.one('draw.dt error.dt', resolve);
                                this.table.ajax.reload();
                            }),
                        ]);

                        const incoming = Number(totals.data.incoming);
                        const outgoing = Number(totals.data.outgoing);
                        $('#totalIncoming').text(this.rupiah(incoming));
                        $('#totalOutgoing').text(this.rupiah(outgoing));
                        $('#totalNet').text(this.rupiah(incoming - outgoing));

                        this.renderMonthly(monthly.data);
                    } catch (error) {
                        if (error?.status === 422 && error?.errors) {
                            app.setFormErrors(this.form, error);
                            return;
                        }

                        app.alert('danger', 'Terjadi kesalahan saat mengambil laporan kas. Silakan coba lagi.');
                    } finally {
                        this.buttonFilter.prop('disabled', false);
                        app.hidePreloader();
                    }
                }

                reset() {
                    const now = new Date();
                    const pad = (value) => String(value).padStart(2, '0');
                    const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
                    const prefix = `${now.getFullYear()}-${pad(now.getMonth() + 1)}`;

                    this.startDate.val(`${prefix}-01`);
                    this.endDate.val(`${prefix}-${pad(lastDay)}`);
                    $('#filterType, #filterUser').val('');

                    this.apply();
                }
            }

            window.service = new Service();

            service.table.on('error.dt', function () {
                app.alert('danger', 'Terjadi kesalahan saat mengambil laporan kas. Silakan coba lagi.');
            });

            service.form.submit(function (e) {
                e.preventDefault();
                service.apply();
            });

            $('#buttonReset').click(function (e) {
                e.preventDefault();
                service.reset();
            });

            // Tab aktif menentukan jenis laporan PDF; tabel pada tab tersembunyi disesuaikan lebarnya saat ditampilkan.
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
                service.report = $(this).attr('data-report');
                service.updateExportLinks();

                [service.table, service.monthlyTable, service.reconciliationTable].forEach((table) => table?.columns.adjust());
            });

            $(document).ready(function () {
                service.apply();
            });
        </script>
    </x-slot:script>
</x-layouts.auth>
