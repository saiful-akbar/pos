<x-layouts.auth title="Transaksi Kas" :access="$access">
    {{-- Header halaman: judul dan tombol tambah kas masuk / kas keluar --}}
    <x-page-header title="Transaksi Kas" icon="receipt_long">
        <x-slot:action>
            @if ($access?->create ?? false)
                <div class="d-flex justify-content-lg-end gap-2">
                    <x-button type="button" id="buttonAddIncoming" startIcon="south_west">
                        Kas Masuk
                    </x-button>

                    <x-button type="button" id="buttonAddOutgoing" color="light" startIcon="north_east" class="border">
                        Kas Keluar
                    </x-button>
                </div>
            @endif
        </x-slot:action>

        <p class="text-body-secondary mt-4 mb-0">
            Catat dan telusuri kas masuk dan kas keluar toko. Keterangan transaksi diisi pada kolom catatan.
        </p>
    </x-page-header>

    {{-- Card filter --}}
    <div class="row gx-7">
        <div class="col-12 mt-7">
            <div class="card shadow-sm">
                <div class="card-header py-4">
                    <h2 class="fs-5 mb-0">Filter Transaksi</h2>
                    <p class="text-body-secondary mt-1 mb-0">Pilih periode dan saring jenis transaksi.</p>
                </div>

                <form id="formFilter" autocomplete="off">
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

                            @if (count($users) > 0)
                                <div class="col-12 col-md-6 col-xl-3 mb-5">
                                    <x-form-select name="user_id" id="filterUser" label="Pencatat">
                                        <option value="">Semua</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user['id'] }}" @selected(request('user_id') === $user['id'])>{{ $user['name'] }}</option>
                                        @endforeach
                                    </x-form-select>
                                </div>
                            @endif
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

    {{-- Total periode --}}
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

    {{-- Tabel transaksi --}}
    <div class="row gx-7">
        <div class="col-12 mt-7 mb-7">
            <div class="card shadow-sm">
                <div class="card-header py-4">
                    <h2 class="fs-5 mb-0">Daftar Transaksi Kas</h2>
                    <p class="text-body-secondary mt-1 mb-0" id="periodLabel">&nbsp;</p>
                </div>

                <div class="card-body">
                    <table id="cashTable" class="table table-sm table-hover align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal form tambah / ubah transaksi --}}
    @if (($access?->create ?? false) || ($access?->update ?? false))
        <form action="#" method="post" id="formTransaction" autocomplete="off">
            <x-modal id="modalForm" title="Transaksi Kas">
                <div class="mb-5">
                    <x-form-select name="type" id="type" label="Jenis Transaksi" required>
                        <option value="INCOMING">Kas Masuk</option>
                        <option value="OUTGOING">Kas Keluar</option>
                    </x-form-select>
                </div>

                <div class="mb-5">
                    <x-form-input type="text" name="transaction_date" id="transactionDate" label="Tanggal Transaksi"
                        placeholder="Pilih tanggal" value="{{ date('Y-m-d') }}" data-flatpickr="" required />
                </div>

                <div class="mb-5">
                    <x-form-input type="number" name="amount" id="amount" label="Nominal (Rp)" placeholder="0.00"
                        min="0.01" step="0.01" required />
                </div>

                <div class="mb-5">
                    <x-form-input type="textarea" name="notes" id="notes" label="Catatan (opsional)"
                        placeholder="Contoh: pembayaran listrik, tambahan modal kas..." />
                </div>

                <x-form-input type="text" name="reference_number" id="referenceNumber" label="Nomor Referensi (opsional)"
                    placeholder="Contoh: nomor bukti atau kuitansi" maxlength="100" />

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

    {{-- Modal detail transaksi --}}
    <x-modal id="modalDetail" title="Detail Transaksi Kas">
        <div class="row gx-6 gy-4" id="detailInfo"></div>
    </x-modal>

    {{-- JavaScript --}}
    <x-slot:script>
        <script>
            "use strict"

            // Error ajax DataTables ditangani sendiri (tidak memakai alert bawaan).
            DataTable.ext.errMode = 'none';

            class Service {
                constructor() {
                    this.access = { update: @json((bool) ($access?->update ?? false)), remove: @json((bool) ($access?->remove ?? false)) };
                    this.form = $('#formFilter');
                    this.formTransaction = $('#formTransaction');
                    this.startDate = $('#startDate');
                    this.endDate = $('#endDate');
                    this.buttonFilter = $('#buttonFilter');
                    this.modalForm = $('#modalForm').length > 0 ? new bootstrap.Modal('#modalForm', { backdrop: 'static' }) : null;
                    this.modalDetail = new bootstrap.Modal('#modalDetail');
                    this.editId = null;
                    this.table = this.#tableInit();
                }

                escapeHtml(value) {
                    return $('<div>').text(value ?? '').html();
                }

                attr(value) {
                    return this.escapeHtml(value).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                }

                /**
                 * Format Rupiah.
                 */
                rupiah(value) {
                    const number = Number(value ?? 0);
                    const text = `Rp ${app.numberFormat(Math.abs(number))}`;

                    return number < 0 ? `-${text}` : text;
                }

                /**
                 * Badge jenis transaksi.
                 */
                typeBadge(type) {
                    return type === 'INCOMING'
                        ? '<span class="badge text-bg-success">Kas Masuk</span>'
                        : '<span class="badge text-bg-danger">Kas Keluar</span>';
                }

                /**
                 * Nilai filter saat ini (nilai kosong tidak dikirim).
                 */
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

                /**
                 * Inisialisasi DataTables transaksi kas (server side).
                 */
                #tableInit() {
                    const columns = [
                        { title: 'Tanggal', name: 'cash_transactions.transaction_date', data: 'transaction_date', className: 'text-nowrap' },
                        {
                            title: 'No. Transaksi',
                            name: 'cash_transactions.transaction_number',
                            data: 'transaction_number',
                            className: 'text-nowrap fw-semibold',
                            render: (data) => this.escapeHtml(data),
                        },
                        {
                            title: 'Jenis',
                            name: 'cash_transactions.type',
                            data: 'type',
                            className: 'text-nowrap',
                            searchable: false,
                            render: (data) => this.typeBadge(data),
                        },
                        {
                            title: 'Nominal',
                            name: 'cash_transactions.amount',
                            data: 'amount',
                            className: 'text-nowrap text-end fw-semibold',
                            searchable: false,
                            render: (data, type, row) => `${row.type === 'OUTGOING' ? '-' : '+'}${this.rupiah(data)}`,
                        },
                        {
                            title: 'Catatan',
                            name: 'cash_transactions.notes',
                            data: 'notes',
                            render: (data) => this.escapeHtml(data ?? '-'),
                        },
                        {
                            title: 'Referensi',
                            name: 'cash_transactions.reference_number',
                            data: 'reference_number',
                            className: 'text-nowrap',
                            render: (data) => this.escapeHtml(data ?? '-'),
                        },
                        {
                            title: 'Pencatat',
                            name: 'users.full_name',
                            data: 'user_name',
                            className: 'text-nowrap',
                            render: (data) => this.escapeHtml(data ?? '-'),
                        },
                        {
                            title: app.icon('settings', 'fs-6'),
                            name: 'cash_transactions.id',
                            data: 'id',
                            className: 'text-nowrap text-center',
                            searchable: false,
                            orderable: false,
                            render: (data) => {
                                const id = this.attr(data);
                                let html = `<button type="button" class="btn btn-sm btn-light" data-action="detail" data-id="${id}" title="Lihat detail" aria-label="Lihat detail">${app.icon('visibility', 'fs-6')}</button>`;

                                if (this.access.update) {
                                    html += ` <button type="button" class="btn btn-sm btn-light" data-action="edit" data-id="${id}" title="Ubah" aria-label="Ubah">${app.icon('edit', 'fs-6')}</button>`;
                                }

                                if (this.access.remove) {
                                    html += ` <button type="button" class="btn btn-sm btn-light" data-action="remove" data-id="${id}" title="Hapus" aria-label="Hapus">${app.icon('delete', 'fs-6')}</button>`;
                                }

                                return html;
                            },
                        },
                    ];

                    return app.dataTable('#cashTable', {
                        columns,
                        serverSide: true,
                        // Data awal dimuat oleh apply() agar tidak ada request ganda.
                        deferLoading: 0,
                        fixedColumns: { start: 0, end: 1 },
                        language: {
                            search: '',
                            searchPlaceholder: 'Cari nomor atau catatan...',
                            lengthMenu: '_MENU_',
                            info: '_START_ - _END_ dari _TOTAL_',
                            infoEmpty: '0 - 0 dari 0',
                            infoFiltered: '',
                            zeroRecords: 'Tidak ditemukan transaksi yang sesuai dengan pencarian.',
                            emptyTable: 'Belum ada transaksi kas pada periode atau filter yang dipilih.',
                        },
                        ajax: {
                            url: @json(route('cash.transactions')),
                            data: (params) => Object.assign(params, this.filters()),
                        },
                        order: [[0, 'desc'], [1, 'desc']],
                    });
                }

                /**
                 * Permintaan ajax umum (JSON).
                 */
                request(type, url, data = {}) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type,
                            url,
                            dataType: 'json',
                            data: type === 'get' ? data : { ...data, _token: app.csrfToken },
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                            success: (response) => resolve(response),
                            error: (xhr) => reject({ ...xhr.responseJSON, status: xhr.status }),
                        });
                    });
                }

                /**
                 * Terapkan filter: perbarui query string, total, dan tabel.
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

                    const url = new URL(window.location);
                    url.search = new URLSearchParams(filters).toString();
                    window.history.pushState({}, '', url);

                    $('#periodLabel').text(`Periode ${filters.start_date} s/d ${filters.end_date}`);

                    this.buttonFilter.prop('disabled', true);
                    app.showPreloader();

                    try {
                        const [totals] = await Promise.all([
                            this.request('get', @json(route('cash.transactions.totals')), filters),
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
                    } catch (error) {
                        if (error?.status === 422 && error?.errors) {
                            app.setFormErrors(this.form, error);
                            return;
                        }

                        app.alert('danger', 'Terjadi kesalahan saat mengambil data transaksi kas. Silakan coba lagi.');
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

                /**
                 * Membuka form tambah (jenis transaksi dapat diisi awal) atau ubah transaksi.
                 */
                openForm(type, transaction = null) {
                    app.clearFormErrors(this.formTransaction);
                    this.formTransaction.trigger('reset');
                    this.editId = transaction?.id ?? null;

                    $('#modalForm .modal-title').text(transaction ? `Ubah Transaksi ${transaction.transaction_number}` : (type === 'OUTGOING' ? 'Tambah Kas Keluar' : 'Tambah Kas Masuk'));
                    $('#type').val(transaction?.type ?? type);
                    $('#transactionDate').val(transaction?.transaction_date ?? new Date().toISOString().slice(0, 10));
                    $('#amount').val(transaction?.amount ?? '');
                    $('#notes').val(transaction?.notes ?? '');
                    $('#referenceNumber').val(transaction?.reference_number ?? '');

                    this.modalForm?.show();
                }
            }

            window.service = new Service();

            service.table.on('error.dt', function () {
                app.alert('danger', 'Terjadi kesalahan saat mengambil data transaksi kas. Silakan coba lagi.');
            });

            service.form.submit(function (e) {
                e.preventDefault();
                service.apply();
            });

            $('#buttonReset').click(function (e) {
                e.preventDefault();
                service.reset();
            });

            $('#buttonAddIncoming').click(() => service.openForm('INCOMING'));
            $('#buttonAddOutgoing').click(() => service.openForm('OUTGOING'));

            // Delegasi pada wrapper tabel karena kolom aksi tetap (fixed columns) berupa klon di luar elemen tabel asli.
            $(document).on('click', '#cashTable_wrapper button[data-action]', async function () {
                const id = $(this).attr('data-id');
                const action = $(this).attr('data-action');

                if (action === 'remove') {
                    app.confirm('Hapus Transaksi', 'Yakin ingin menghapus transaksi kas ini? Transaksi tidak akan dihitung lagi pada saldo dan laporan.', async (confirmed) => {
                        if (!confirmed) return;

                        app.clearAlert();
                        app.showPreloader();

                        try {
                            const response = await service.request('post', app.url(`/cash/transactions/${id}`), { _method: 'delete' });
                            app.alert('success', response.message);
                            service.apply();
                        } catch (error) {
                            app.alert('danger', error?.message ?? 'Terjadi kesalahan saat menghapus transaksi kas.');
                        } finally {
                            app.hidePreloader();
                        }
                    });

                    return;
                }

                app.clearAlert();
                app.showPreloader();

                try {
                    const response = await service.request('get', app.url(`/cash/transactions/${id}`));
                    const transaction = response.data;

                    if (action === 'edit') {
                        service.openForm(transaction.type, transaction);
                        return;
                    }

                    // Detail transaksi.
                    const info = [
                        ['No. Transaksi', transaction.transaction_number],
                        ['Tanggal', transaction.transaction_date],
                        ['Jenis', transaction.type_label],
                        ['Nominal', service.rupiah(transaction.amount)],
                        ['Nomor Referensi', transaction.reference_number ?? '-'],
                        ['Pencatat', transaction.user.full_name],
                        ['Catatan', transaction.notes ?? '-'],
                        ['Dibuat', transaction.created_at ?? '-'],
                        ['Diperbarui', transaction.updated_at ?? '-'],
                    ];

                    $('#detailInfo').html(info.map(([label, value]) => `
                        <div class="col-12 col-md-6">
                            <div class="text-body-secondary small">${service.escapeHtml(label)}</div>
                            <div class="fw-semibold text-break">${service.escapeHtml(value)}</div>
                        </div>
                    `).join(''));

                    service.modalDetail.show();
                } catch (error) {
                    app.alert('danger', error?.message ?? 'Terjadi kesalahan saat mengambil data transaksi kas.');
                } finally {
                    app.hidePreloader();
                }
            });

            // Simpan transaksi (tambah atau ubah).
            service.formTransaction.submit(async function (e) {
                e.preventDefault();

                app.clearAlert();
                app.clearFormErrors(service.formTransaction);
                app.showPreloader();

                const data = {
                    type: $('#type').val(),
                    transaction_date: $('#transactionDate').val(),
                    amount: $('#amount').val(),
                    notes: $('#notes').val(),
                    reference_number: $('#referenceNumber').val(),
                };

                try {
                    const response = service.editId === null
                        ? await service.request('post', @json(route('cash.transactions.store')), data)
                        : await service.request('post', app.url(`/cash/transactions/${service.editId}`), { ...data, _method: 'patch' });

                    service.modalForm?.hide();
                    app.alert('success', response.message);
                    service.apply();
                } catch (error) {
                    if (error?.status === 422 && error?.errors) {
                        app.setFormErrors(service.formTransaction, error);
                        return;
                    }

                    service.modalForm?.hide();
                    app.alert('danger', error?.message ?? 'Terjadi kesalahan saat menyimpan transaksi kas.');
                } finally {
                    app.hidePreloader();
                }
            });

            $(document).ready(function () {
                service.apply();
            });
        </script>
    </x-slot:script>
</x-layouts.auth>
