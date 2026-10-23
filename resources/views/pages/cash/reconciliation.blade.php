<x-layouts.auth title="Rekonsiliasi Kas" :access="$access">
    {{-- Header halaman --}}
    <x-page-header title="Rekonsiliasi Kas" icon="fact_check">
        <p class="text-body-secondary mt-4 mb-0">
            Catat saldo awal aktual setiap bulan dari hasil penghitungan kas, lalu bandingkan dengan saldo teoritis bulan sebelumnya.
        </p>
    </x-page-header>

    {{-- Card pemilihan periode --}}
    <div class="row gx-7">
        <div class="col-12 mt-7">
            <div class="card shadow-sm">
                <form id="formPeriod" autocomplete="off">
                    @php
                        $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        $selectedMonth = (int) request('month', now()->month);
                        $selectedYear = (int) request('year', now()->year);
                    @endphp

                    <div class="card-body pb-0">
                        <div class="row gx-6">
                            <div class="col-12 col-md-6 col-xl-3 mb-5">
                                <x-form-select name="month" id="month" label="Bulan" required>
                                    @foreach ($monthNames as $index => $monthName)
                                        <option value="{{ $index + 1 }}" @selected($selectedMonth === $index + 1)>{{ $monthName }}</option>
                                    @endforeach
                                </x-form-select>
                            </div>

                            <div class="col-12 col-md-6 col-xl-3 mb-5">
                                <x-form-input type="number" name="year" id="year" label="Tahun" value="{{ $selectedYear }}"
                                    min="2000" max="{{ date('Y') + 1 }}" required />
                            </div>

                            <div class="col-12 col-md-6 col-xl-3 mb-5 d-flex align-items-end">
                                <x-button type="submit" id="buttonFilter" startIcon="filter_alt" fullWidth>
                                    Tampilkan
                                </x-button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Status rekonsiliasi dan penjelasannya --}}
    <div class="row gx-7">
        <div class="col-12 mt-7">
            <div class="border rounded-3 p-4 d-flex align-items-start gap-3">
                <x-icon class="fs-4" id="statusIcon">info</x-icon>
                <div style="min-width: 0;">
                    <div class="fw-semibold" id="statusLabel">Memuat data...</div>
                    <p class="text-body-secondary mt-1 mb-0" id="statusText"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Kartu ringkasan periode --}}
    <div class="row gx-7">
        @php
            $cards = [
                ['id' => 'cardOpening', 'label' => 'Saldo Awal', 'icon' => 'account_balance_wallet', 'color' => 'primary'],
                ['id' => 'cardIncoming', 'label' => 'Kas Masuk', 'icon' => 'south_west', 'color' => 'success'],
                ['id' => 'cardOutgoing', 'label' => 'Kas Keluar', 'icon' => 'north_east', 'color' => 'danger'],
                ['id' => 'cardTheoretical', 'label' => 'Saldo Teoritis Akhir', 'icon' => 'calculate', 'color' => 'info'],
                ['id' => 'cardNextOpening', 'label' => 'Saldo Aktual Bulan Berikutnya', 'icon' => 'event_available', 'color' => 'primary'],
                ['id' => 'cardDifference', 'label' => 'Selisih Kas', 'icon' => 'compare_arrows', 'color' => 'secondary'],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="col-12 col-sm-6 col-xl-4 mt-7">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-start justify-content-between gap-3">
                        <div style="min-width: 0;">
                            <div class="text-body-secondary small fw-semibold text-uppercase">{{ $card['label'] }}</div>
                            <div class="fs-4 fw-bold text-break mt-2" id="{{ $card['id'] }}">-</div>
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

    {{-- Form saldo awal aktual --}}
    @if (($access?->create ?? false) || ($access?->update ?? false))
        <div class="row gx-7">
            <div class="col-12 mt-7">
                <div class="card shadow-sm">
                    <div class="card-header py-4">
                        <h2 class="fs-5 mb-0">Saldo Awal Aktual <span id="balancePeriod"></span></h2>
                        <p class="text-body-secondary mt-1 mb-0">
                            Isi dengan hasil penghitungan kas fisik pada awal bulan. Saldo tidak diisi otomatis dari saldo teoritis bulan sebelumnya.
                        </p>
                    </div>

                    <form id="formBalance" autocomplete="off">
                        <div class="card-body pb-0">
                            <div class="row gx-6">
                                <div class="col-12 col-md-4 mb-5">
                                    <x-form-input type="number" name="opening_balance" id="openingBalance" label="Saldo Awal Aktual (Rp)"
                                        placeholder="0.00" min="0" step="0.01" required />
                                </div>

                                <div class="col-12 col-md-8 mb-5">
                                    <x-form-input type="text" name="notes" id="balanceNotes" label="Catatan (opsional)"
                                        placeholder="Contoh: dihitung bersama pemilik toko" maxlength="500" />
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row justify-content-end">
                                <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                    <x-button type="submit" id="buttonSave" startIcon="save" fullWidth>
                                        Simpan Saldo
                                    </x-button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Histori rekonsiliasi bulanan --}}
    <div class="row gx-7">
        <div class="col-12 mt-7 mb-7">
            <div class="card shadow-sm">
                <div class="card-header py-4">
                    <h2 class="fs-5 mb-0">Histori Rekonsiliasi</h2>
                    <p class="text-body-secondary mt-1 mb-0" id="historyLabel">&nbsp;</p>
                </div>

                <div class="card-body">
                    <table id="historyTable" class="table table-sm table-hover align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    <x-slot:script>
        <script>
            "use strict"

            class Service {
                constructor() {
                    this.form = $('#formPeriod');
                    this.formBalance = $('#formBalance');
                    this.buttonFilter = $('#buttonFilter');
                    this.monthNames = @json($monthNames);
                    this.canCreate = @json((bool) ($access?->create ?? false));
                    this.canUpdate = @json((bool) ($access?->update ?? false));
                    this.current = null;
                    this.historyYear = null;
                    this.statusMap = {
                        match: ['Sesuai', 'check_circle', 'Saldo aktual awal bulan berikutnya sama dengan saldo teoritis akhir bulan ini.', 'text-bg-success'],
                        difference: ['Selisih', 'error', 'Saldo aktual awal bulan berikutnya berbeda dari saldo teoritis akhir bulan ini. Periksa transaksi kas bulan ini.', 'text-bg-danger'],
                        unreconciled: ['Belum direkonsiliasi', 'hourglass_empty', 'Saldo awal bulan berikutnya belum dicatat, sehingga belum ada pembanding.', 'border text-body-secondary'],
                        incomplete: ['Data belum lengkap', 'help', 'Saldo awal bulan ini belum dicatat, sehingga saldo teoritis tidak dapat dihitung.', 'border text-body-secondary'],
                    };
                    this.historyTable = null;
                }

                escapeHtml(value) {
                    return $('<div>').text(value ?? '').html();
                }

                /**
                 * Format Rupiah; null (belum tersedia) ditampilkan sebagai strip, bukan nol.
                 */
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
                    const [label, icon, , classes] = this.statusMap[status] ?? ['-', 'help', '', 'border'];

                    return `<span class="badge ${classes} d-inline-flex align-items-center gap-1">${app.icon(icon, 'fs-6')}<span>${label}</span></span>`;
                }

                request(url, data = {}, type = 'get') {
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
                 * Menampilkan ringkasan periode dan mengisi form saldo.
                 */
                render(summary) {
                    this.current = summary;

                    $('#cardOpening').text(this.rupiah(summary.opening_balance));
                    $('#cardIncoming').text(this.rupiah(summary.incoming));
                    $('#cardOutgoing').text(this.rupiah(summary.outgoing));
                    $('#cardTheoretical').text(this.rupiah(summary.theoretical_closing));
                    $('#cardNextOpening').text(this.rupiah(summary.next_opening_balance));
                    $('#cardDifference').text(this.signedRupiah(summary.difference));

                    const period = `${this.monthNames[summary.month - 1]} ${summary.year}`;
                    const [label, icon, text] = this.statusMap[summary.status] ?? ['-', 'info', ''];
                    $('#statusLabel').text(`${label} - ${period}`);
                    $('#statusIcon').text(icon);
                    $('#statusText').text(text);

                    // Form saldo: terisi jika sudah dicatat; tombol sesuai hak akses (simpan baru / perbarui).
                    $('#balancePeriod').text(period);
                    $('#openingBalance').val(summary.opening_balance ?? '');
                    $('#balanceNotes').val(summary.notes ?? '');

                    const exists = summary.balance_id !== null;
                    const allowed = exists ? this.canUpdate : this.canCreate;
                    $('#buttonSave').prop('disabled', !allowed).html(`${app.icon('save', 'me-1')} ${exists ? 'Perbarui Saldo' : 'Simpan Saldo'}`);
                }

                /**
                 * Memuat histori satu tahun.
                 */
                async loadHistory(year) {
                    const response = await this.request(@json(route('cash.reconciliation.history')), { year });
                    this.historyYear = year;
                    $('#historyLabel').text(`Tahun ${year}`);

                    if (this.historyTable !== null) {
                        this.historyTable.destroy();
                        $('#historyTable').empty();
                    }

                    this.historyTable = app.dataTable('#historyTable', {
                        data: response.data,
                        paging: false,
                        searching: false,
                        ordering: false,
                        info: false,
                        layout: { topStart: null, topEnd: null, bottomStart: null, bottomEnd: null },
                        columns: [
                            { title: 'Periode', data: 'month', className: 'text-nowrap fw-semibold', render: (data, type, row) => `${this.monthNames[data - 1]} ${row.year}` },
                            { title: 'Saldo Awal', data: 'opening_balance', className: 'text-nowrap text-end', render: (data) => this.rupiah(data) },
                            { title: 'Kas Masuk', data: 'incoming', className: 'text-nowrap text-end', render: (data) => this.rupiah(data) },
                            { title: 'Kas Keluar', data: 'outgoing', className: 'text-nowrap text-end', render: (data) => this.rupiah(data) },
                            { title: 'Saldo Teoritis', data: 'theoretical_closing', className: 'text-nowrap text-end', render: (data) => this.rupiah(data) },
                            { title: 'Saldo Aktual Berikutnya', data: 'next_opening_balance', className: 'text-nowrap text-end', render: (data) => this.rupiah(data) },
                            { title: 'Selisih', data: 'difference', className: 'text-nowrap text-end fw-bold', render: (data) => this.signedRupiah(data) },
                            { title: 'Status', data: 'status', className: 'text-nowrap', render: (data) => this.statusBadge(data) },
                            { title: 'Catatan', data: 'notes', render: (data) => this.escapeHtml(data ?? '-') },
                            {
                                title: app.icon('settings', 'fs-6'),
                                data: 'month',
                                className: 'text-nowrap text-center',
                                render: (data, type, row) => `<button type="button" class="btn btn-sm btn-light" data-month="${row.month}" data-year="${row.year}" title="Pilih periode" aria-label="Pilih periode">${app.icon('open_in_new', 'fs-6')}</button>`,
                            },
                        ],
                    });
                }

                /**
                 * Terapkan periode: perbarui query string, ringkasan, dan histori tahun periode.
                 */
                async apply() {
                    app.clearAlert();
                    app.clearFormErrors(this.form);
                    app.clearFormErrors(this.formBalance);

                    const month = $('#month').val();
                    const year = $('#year').val();

                    const url = new URL(window.location);
                    url.search = new URLSearchParams({ month, year }).toString();
                    window.history.pushState({}, '', url);

                    this.buttonFilter.prop('disabled', true);
                    app.showPreloader();

                    try {
                        const response = await this.request(@json(route('cash.reconciliation.data')), { month, year });
                        this.render(response.data);
                        await this.loadHistory(year);
                    } catch (error) {
                        if (error?.status === 422 && error?.errors) {
                            app.setFormErrors(this.form, error);
                            return;
                        }

                        app.alert('danger', 'Terjadi kesalahan saat mengambil data rekonsiliasi kas. Silakan coba lagi.');
                    } finally {
                        this.buttonFilter.prop('disabled', false);
                        app.hidePreloader();
                    }
                }
            }

            window.service = new Service();

            service.form.submit(function (e) {
                e.preventDefault();
                service.apply();
            });

            // Pilih periode dari histori.
            $(document).on('click', '#historyTable button[data-month]', function () {
                $('#month').val($(this).attr('data-month'));
                $('#year').val($(this).attr('data-year'));
                service.apply();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });

            // Simpan atau perbarui saldo awal aktual periode terpilih.
            service.formBalance.submit(async function (e) {
                e.preventDefault();

                const summary = service.current;
                const exists = summary?.balance_id !== null;

                const proceed = async () => {
                    app.clearAlert();
                    app.clearFormErrors(service.formBalance);
                    app.showPreloader();

                    const data = {
                        year: summary.year,
                        month: summary.month,
                        opening_balance: $('#openingBalance').val(),
                        notes: $('#balanceNotes').val(),
                    };

                    try {
                        const response = await service.request(
                            @json(route('cash.reconciliation.store')),
                            exists ? { ...data, _method: 'patch' } : data,
                            'post',
                        );

                        app.alert('success', response.message);
                        await service.apply();
                    } catch (error) {
                        if (error?.status === 422 && error?.errors) {
                            app.setFormErrors(service.formBalance, error);
                            return;
                        }

                        app.alert('danger', error?.message ?? 'Terjadi kesalahan saat menyimpan saldo awal kas.');
                    } finally {
                        app.hidePreloader();
                    }
                };

                // Perubahan saldo yang sudah dicatat memerlukan konfirmasi (tercatat pada log aktifitas).
                if (exists) {
                    app.confirm('Perbarui Saldo Awal', 'Saldo awal periode ini sudah dicatat. Perubahan akan memengaruhi rekonsiliasi dan tercatat pada log aktifitas. Lanjutkan?', (confirmed) => {
                        if (confirmed) proceed();
                    });

                    return;
                }

                proceed();
            });

            $(document).ready(function () {
                service.apply();
            });
        </script>
    </x-slot:script>
</x-layouts.auth>
