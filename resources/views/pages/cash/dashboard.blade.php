<x-layouts.auth title="Ringkasan Kas" :access="$access">
    {{-- Header halaman: judul dan tautan cepat --}}
    <x-page-header title="Ringkasan Kas" icon="payments">
        <x-slot:action>
            <div class="d-flex justify-content-lg-end gap-2">
                <x-button type="link" href="{{ route('cash.transactions') }}" color="light" startIcon="receipt_long"
                    class="border">
                    Transaksi Kas
                </x-button>

                <x-button type="link" href="{{ route('cash.reconciliation') }}" color="light" startIcon="fact_check"
                    class="border">
                    Rekonsiliasi
                </x-button>
            </div>
        </x-slot:action>

        <p class="text-body-secondary mt-4 mb-0">
            Pantau saldo awal, kas masuk, kas keluar, dan selisih kas setiap bulan.
        </p>
    </x-page-header>

    {{-- Card filter periode --}}
    <div class="row gx-7">
        <div class="col-12 mt-7">
            <div class="card shadow-sm">
                <form id="formPeriod" autocomplete="off">
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
                        $selectedMonth = (int) request('month', now()->month);
                        $selectedYear = (int) request('year', now()->year);
                    @endphp

                    <div class="card-body pb-0">
                        <div class="row gx-6">
                            <div class="col-12 col-md-6 col-xl-5 mb-5">
                                <x-form-select name="month" id="month" label="Bulan" required>
                                    @foreach ($monthNames as $index => $monthName)
                                        <option value="{{ $index + 1 }}" @selected($selectedMonth === $index + 1)>
                                            {{ $monthName }}</option>
                                    @endforeach
                                </x-form-select>
                            </div>

                            <div class="col-12 col-md-6 col-xl-5 mb-5">
                                <x-form-input type="number" name="year" id="year" label="Tahun"
                                    value="{{ $selectedYear }}" min="2000" max="{{ date('Y') + 1 }}" required />
                            </div>

                            <div class="col-12 col-md-6 col-xl-2 mb-5 d-flex align-items-end">
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
            <div class="border rounded-3 p-4 d-flex align-items-start gap-3" id="statusNotice">
                <x-icon class="fs-4" id="statusIcon">info</x-icon>
                <div style="min-width: 0;">
                    <div class="fw-semibold" id="statusLabel">Memuat ringkasan...</div>
                    <p class="text-body-secondary mt-1 mb-0" id="statusText"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Kartu ringkasan kas --}}
    <div class="row gx-7">
        @php
            $cards = [
                [
                    'id' => 'cardOpening',
                    'label' => 'Saldo Awal',
                    'icon' => 'account_balance_wallet',
                    'color' => 'primary',
                    'hint' => 'Hasil penghitungan kas awal bulan',
                    'cols' => 'col-xl-3',
                ],
                [
                    'id' => 'cardIncoming',
                    'label' => 'Total Kas Masuk',
                    'icon' => 'south_west',
                    'color' => 'success',
                    'hint' => 'Transaksi kas masuk bulan ini',
                    'cols' => 'col-xl-3',
                ],
                [
                    'id' => 'cardOutgoing',
                    'label' => 'Total Kas Keluar',
                    'icon' => 'north_east',
                    'color' => 'danger',
                    'hint' => 'Transaksi kas keluar bulan ini',
                    'cols' => 'col-xl-3',
                ],
                [
                    'id' => 'cardTheoretical',
                    'label' => 'Saldo Teoritis Akhir',
                    'icon' => 'calculate',
                    'color' => 'info',
                    'hint' => 'Saldo awal + masuk - keluar',
                    'cols' => 'col-xl-3',
                ],
                [
                    'id' => 'cardNextOpening',
                    'label' => 'Saldo Aktual Bulan Berikutnya',
                    'icon' => 'event_available',
                    'color' => 'primary',
                    'hint' => 'Saldo awal bulan berikutnya',
                    'cols' => 'col-xl-6',
                ],
                [
                    'id' => 'cardDifference',
                    'label' => 'Selisih Kas',
                    'icon' => 'compare_arrows',
                    'color' => 'secondary',
                    'hint' => 'Saldo aktual - saldo teoritis',
                    'cols' => 'col-xl-6',
                ],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="col-12 col-sm-6 {{ $card['cols'] }} mt-7">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div style="min-width: 0;">
                                <div class="text-body-secondary small fw-semibold text-uppercase">{{ $card['label'] }}
                                </div>
                                <div class="fs-3 fw-bold text-break mt-2" id="{{ $card['id'] }}">-</div>
                            </div>

                            <div class="rounded-3 bg-{{ $card['color'] }} bg-opacity-10 text-body d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 3rem; height: 3rem;">
                                <x-icon class="fs-4">{{ $card['icon'] }}</x-icon>
                            </div>
                        </div>

                        <div class="small text-body-secondary mt-2">{{ $card['hint'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mb-7"></div>

    {{-- JavaScript --}}
    <x-slot:script>
        <script>
            "use strict"

            class Service {
                constructor() {
                    this.form = $('#formPeriod');
                    this.buttonFilter = $('#buttonFilter');
                    this.monthNames = @json($monthNames);
                    this.statusMap = {
                        match: [
                            'Sesuai',
                            'check_circle',
                            'Saldo aktual awal bulan berikutnya sama dengan saldo teoritis akhir bulan ini.'
                        ],
                        difference: [
                            'Selisih',
                            'error',
                            'Saldo aktual awal bulan berikutnya berbeda dari saldo teoritis akhir bulan ini. Periksa transaksi kas bulan ini.'
                        ],
                        unreconciled: [
                            'Belum direkonsiliasi',
                            'hourglass_empty',
                            'Saldo awal bulan berikutnya belum dicatat, sehingga belum ada pembanding. Catat pada menu Rekonsiliasi Kas.'
                        ],
                        incomplete: [
                            'Data belum lengkap',
                            'help',
                            'Saldo awal bulan ini belum dicatat, sehingga saldo teoritis tidak dapat dihitung. Catat pada menu Rekonsiliasi Kas.'
                        ],
                    };
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

                /**
                 * Selisih dengan tanda + / - agar mudah dikenali.
                 */
                signedRupiah(value) {
                    if (value === null || value === undefined) return '-';

                    const number = Number(value);

                    return number > 0 ? `+${this.rupiah(value)}` : this.rupiah(value);
                }

                /**
                 * Mengambil ringkasan kas satu bulan (ajax).
                 */
                load() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'get',
                            url: @json(route('cash.dashboard.summary')),
                            dataType: 'json',
                            data: {
                                month: $('#month').val(),
                                year: $('#year').val()
                            },
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
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
                 * Menampilkan ringkasan pada kartu dan keterangan status.
                 */
                render(summary) {
                    $('#cardOpening').text(this.rupiah(summary.opening_balance));
                    $('#cardIncoming').text(this.rupiah(summary.incoming));
                    $('#cardOutgoing').text(this.rupiah(summary.outgoing));
                    $('#cardTheoretical').text(this.rupiah(summary.theoretical_closing));
                    $('#cardNextOpening').text(this.rupiah(summary.next_opening_balance));
                    $('#cardDifference').text(this.signedRupiah(summary.difference));

                    const [label, icon, text] = this.statusMap[summary.status] ?? ['-', 'info', ''];
                    $('#statusLabel').text(`${label} - ${this.monthNames[summary.month - 1]} ${summary.year}`);
                    $('#statusIcon').text(icon);
                    $('#statusText').text(text);
                }

                /**
                 * Terapkan periode: perbarui query string lalu muat ringkasan.
                 */
                async apply() {
                    app.clearAlert();
                    app.clearFormErrors(this.form);

                    const url = new URL(window.location);
                    url.search = new URLSearchParams({
                        month: $('#month').val(),
                        year: $('#year').val()
                    }).toString();
                    window.history.pushState({}, '', url);

                    this.buttonFilter.prop('disabled', true);
                    app.showPreloader();

                    try {
                        const response = await this.load();
                        this.render(response.data);
                    } catch (error) {
                        if (error?.status === 422 && error?.errors) {
                            app.setFormErrors(this.form, error);
                            return;
                        }

                        app.alert('danger', 'Terjadi kesalahan saat mengambil ringkasan kas. Silakan coba lagi.');
                    } finally {
                        this.buttonFilter.prop('disabled', false);
                        app.hidePreloader();
                    }
                }
            }

            window.service = new Service();

            service.form.submit(function(e) {
                e.preventDefault();
                service.apply();
            });

            $(document).ready(function() {
                service.apply();
            });
        </script>
    </x-slot:script>
</x-layouts.auth>
