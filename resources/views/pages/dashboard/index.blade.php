<x-layouts.auth title="Dashboard">
    {{-- Header halaman: judul dan tanggal hari ini --}}
    <x-page-header title="Dashboard" icon="dashboard">
        <p class="text-body-secondary mt-4 mb-0" id="todayLabel">Ringkasan operasional toko.</p>
    </x-page-header>

    {{-- Gaya khusus halaman dashboard --}}
    <x-slot:style>
        <style>
            .dash-link {
                transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
            }

            .dash-link:hover, .dash-link:focus-visible {
                border-color: var(--bs-primary);
                box-shadow: var(--bs-box-shadow) !important;
                transform: translateY(-1px);
            }

            .dash-link .dash-arrow {
                transition: transform .15s ease;
            }

            .dash-link:hover .dash-arrow {
                transform: translateX(3px);
            }
            
            .dash-icon {
                width: 3rem;
                height: 3rem;
            }

            .dash-table thead th {
                font-size: .75rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .03em;
                color: var(--bs-secondary-color);
                background: transparent;
                white-space: nowrap;
            }

            .dash-rank {
                width: 1.75rem;
                height: 1.75rem;
                font-size: .8rem;
            }

            .dash-chart {
                position: relative;
                height: clamp(260px, 42vh, 380px);
            }

            .dash-list {
                max-height: 26rem;
                overflow-y: auto;
            }

            @media (min-width: 1200px) {
                .dash-list {
                    max-height: 24.5rem;
                }
            }
        </style>
    </x-slot:style>

    {{-- Shortcut transaksi (hanya untuk pengguna yang berhak membuat transaksi) --}}
    @if ($canCreateSale || $canCreatePurchase)
        <div class="row gx-7">
            @if ($canCreateSale)
                <div class="col-12 col-md-6 mt-7">
                    <a href="{{ route('transactions.sales.carts') }}" class="dash-link card shadow-sm h-100 text-decoration-none text-body">
                        <div class="card-body d-flex align-items-center gap-4">
                            <div class="dash-icon rounded-3 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0">
                                <x-icon class="fs-3">point_of_sale</x-icon>
                            </div>

                            <div class="flex-grow-1" style="min-width: 0;">
                                <h2 class="fs-5 mb-0">Transaksi Penjualan</h2>
                                <p class="text-body-secondary mt-1 mb-0">Mulai transaksi penjualan</p>
                            </div>

                            <x-icon class="dash-arrow fs-4 text-body-secondary flex-shrink-0">arrow_forward</x-icon>
                        </div>
                    </a>
                </div>
            @endif

            @if ($canCreatePurchase)
                <div class="col-12 col-md-6 mt-7">
                    <a href="{{ route('transactions.purchases.carts') }}" class="dash-link card shadow-sm h-100 text-decoration-none text-body">
                        <div class="card-body d-flex align-items-center gap-4">
                            <div class="dash-icon rounded-3 bg-info bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0">
                                <x-icon class="fs-3">shopping_cart</x-icon>
                            </div>

                            <div class="flex-grow-1" style="min-width: 0;">
                                <h2 class="fs-5 mb-0">Transaksi Pembelian</h2>
                                <p class="text-body-secondary mt-1 mb-0">Mulai transaksi pembelian</p>
                            </div>

                            <x-icon class="dash-arrow fs-4 text-body-secondary flex-shrink-0">arrow_forward</x-icon>
                        </div>
                    </a>
                </div>
            @endif
        </div>
    @endif

    @if ($canSale)
        {{-- Tiga kartu total nilai penjualan --}}
        <div class="row gx-7">
            @php
                $cards = [
                    ['id' => 'Today', 'label' => 'Penjualan Hari Ini', 'icon' => 'today', 'color' => 'primary'],
                    ['id' => 'Month', 'label' => 'Penjualan Bulan Ini', 'icon' => 'calendar_month', 'color' => 'success'],
                    ['id' => 'Year', 'label' => 'Penjualan Tahun Ini', 'icon' => 'event_available', 'color' => 'info'],
                ];
            @endphp

            @foreach ($cards as $card)
                <div class="col-12 col-md-4 mt-7">
                    <div class="card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div style="min-width: 0;">
                                    <div class="text-body-secondary small fw-semibold text-uppercase">{{ $card['label'] }}</div>
                                    <div class="fs-3 fw-bold text-break mt-2 lh-sm" id="card{{ $card['id'] }}">Rp 0</div>
                                </div>

                                <div class="dash-icon rounded-3 bg-{{ $card['color'] }} bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0">
                                    <x-icon class="fs-4">{{ $card['icon'] }}</x-icon>
                                </div>
                            </div>

                            <div class="small text-body-secondary mt-auto pt-3 border-top" id="period{{ $card['id'] }}">&nbsp;</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Grafik penjualan bulanan dan stok menipis berdampingan pada layar lebar --}}
    @if ($canSale || $canProduct)
        <div class="row gx-7">
            @if ($canSale)
                <div class="col-12 {{ $canProduct ? 'col-xl-8' : '' }} mt-7">
                    <div class="card shadow-sm h-100">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 py-4">
                            <div style="min-width: 0;">
                                <h2 class="fs-5 mb-0">Penjualan Bulanan</h2>
                                <p class="text-body-secondary mt-1 mb-0">Quantity (satuan dasar) dan nilai penjualan per bulan.</p>
                            </div>

                            <div style="min-width: 7.5rem;">
                                <label for="chartYear" class="visually-hidden">Tahun</label>
                                <select id="chartYear" class="form-select border">
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="small text-body-secondary mb-3 d-none" id="chartEmpty">
                                Belum ada penjualan pada tahun ini. Semua bulan ditampilkan dengan nilai nol.
                            </div>

                            <div class="d-none text-center py-5" id="chartError">
                                <p class="text-body-secondary mb-3">Gagal memuat grafik penjualan.</p>
                                <x-button type="button" id="buttonRetryChart" color="light" startIcon="refresh" class="border">
                                    Coba lagi
                                </x-button>
                            </div>

                            <div class="dash-chart" id="chartWrapper">
                                <canvas id="salesChart" role="img" aria-label="Grafik batang quantity dan nilai penjualan per bulan"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($canProduct)
                <div class="col-12 {{ $canSale ? 'col-xl-4' : '' }} mt-7">
                    <div class="card shadow-sm h-100">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3 py-4">
                            <div style="min-width: 0;">
                                <h2 class="fs-5 mb-0">Stok Menipis</h2>
                                <p class="text-body-secondary mt-1 mb-0">Stok pada atau di bawah batas minimum.</p>
                            </div>

                            <x-button type="link" href="{{ route('masters.products') }}" color="light" size="sm" startIcon="inventory_2" class="border">
                                Produk
                            </x-button>
                        </div>

                        <div class="card-body p-0">
                            <div class="dash-list list-group list-group-flush" id="lowStockBody">
                                <div class="text-center text-body-secondary py-5">Memuat data...</div>
                            </div>
                        </div>

                        <div class="card-footer small text-body-secondary d-none" id="lowStockFooter"></div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @if ($canSale)
        {{-- Analisis produk: terlaris dan tidak laku pada periode yang sama --}}
        <div class="row gx-7">
            <div class="col-12 mt-7">
                <div class="card shadow-sm">
                    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3 py-4">
                        <div style="min-width: 0;">
                            <h2 class="fs-5 mb-0">Analisis Produk</h2>
                            <p class="text-body-secondary mt-1 mb-0" id="productPeriodLabel">&nbsp;</p>
                        </div>

                        <div style="min-width: 12rem;">
                            <label for="productPeriod" class="visually-hidden">Periode analisis</label>
                            <select id="productPeriod" class="form-select border">
                                <option value="month" selected>Bulan ini</option>
                                <option value="30days">30 hari terakhir</option>
                                <option value="year">Tahun ini</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gx-7">
            <div class="col-12 col-xl-6 mt-7">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex align-items-center gap-3 py-4">
                        <div class="rounded-3 bg-success bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.5rem; height: 2.5rem;">
                            <x-icon class="fs-5">trending_up</x-icon>
                        </div>

                        <div style="min-width: 0;">
                            <h2 class="fs-5 mb-0">Top 10 Produk Terlaris</h2>
                            <p class="text-body-secondary mt-1 mb-0">Berdasarkan kuantitas terjual (satuan dasar).</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="dash-table table table-sm table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-5">#</th>
                                    <th>Produk</th>
                                    <th class="text-end">Qty Terjual</th>
                                    <th class="text-end pe-5">Nilai</th>
                                </tr>
                            </thead>

                            <tbody id="fastBody">
                                <tr><td colspan="4" class="text-center text-body-secondary py-5">Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6 mt-7">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex align-items-center gap-3 py-4">
                        <div class="rounded-3 bg-danger bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.5rem; height: 2.5rem;">
                            <x-icon class="fs-5">trending_down</x-icon>
                        </div>

                        <div style="min-width: 0;">
                            <h2 class="fs-5 mb-0">10 Produk Tidak Laku</h2>
                            <p class="text-body-secondary mt-1 mb-0">Produk aktif yang masih memiliki stok, pada periode terpilih.</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="dash-table table table-sm table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-5">Produk</th>
                                    <th class="text-end">Stok</th>
                                    <th class="text-end">Qty Terjual</th>
                                    <th class="text-end">Nilai</th>
                                    <th class="pe-5">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="slowBody">
                                <tr><td colspan="5" class="text-center text-body-secondary py-5">Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="mb-7"></div>

    {{-- JavaScript --}}
    <x-slot:script>
        <script>
            "use strict"

            class Service {
                constructor() {
                    this.canSale = @json((bool) $canSale);
                    this.canProduct = @json((bool) $canProduct);
                    this.today = new Date(@json(now()->format('Y-m-d')) + 'T00:00:00');
                    this.chart = null;
                    this.money = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2
                    });
                    this.compactMoney = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        notation: 'compact',
                        maximumFractionDigits: 1
                    });
                    this.number = new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2
                    });
                    this.date = new Intl.DateTimeFormat('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                }

                escapeHtml(value) {
                    return $('<div>').text(value ?? '').html();
                }

                request(url, data = {}) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'get',
                            url,
                            dataType: 'json',
                            data,
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
                 * Baris pesan pada tabel widget (kosong atau error).
                 */
                messageRow(columns, text) {
                    return `<tr><td colspan="${columns}" class="text-center text-body-secondary py-5">${this.escapeHtml(text)}</td></tr>`;
                }

                /**
                 * Label periode kartu penjualan.
                 */
                renderPeriodLabels() {
                    const monthStart = new Date(this.today.getFullYear(), this.today.getMonth(), 1);
                    const yearStart = new Date(this.today.getFullYear(), 0, 1);

                    $('#todayLabel').text(`Ringkasan operasional toko - ${this.date.format(this.today)}`);
                    $('#periodToday').text(this.date.format(this.today));
                    $('#periodMonth').text(`${this.date.format(monthStart)} - ${this.date.format(this.today)}`);
                    $('#periodYear').text(`${this.date.format(yearStart)} - ${this.date.format(this.today)}`);
                }

                /**
                 * Memuat kartu total penjualan dan stok menipis.
                 */
                async loadSummary() {
                    if (this.canSale) {
                        $('#cardToday, #cardMonth, #cardYear').text('...');
                    }

                    if (this.canProduct) {
                        $('#lowStockBody').html(this.listMessage('Memuat data...'));
                    }

                    try {
                        const {
                            data
                        } = await this.request(@json(route('dashboard.summary')));

                        if (data.totals) {
                            $('#cardToday').text(this.money.format(Number(data.totals.today)));
                            $('#cardMonth').text(this.money.format(Number(data.totals.month)));
                            $('#cardYear').text(this.money.format(Number(data.totals.year)));
                        }

                        if (data.low_stock) {
                            this.renderLowStock(data.low_stock);
                        }
                    } catch (error) {
                        $('#cardToday, #cardMonth, #cardYear').text('-');
                        $('#lowStockBody').html(this.listMessage('Gagal memuat data. Muat ulang halaman untuk mencoba lagi.'));
                    }
                }

                /**
                 * Pesan pada widget berbentuk daftar (kosong atau error).
                 */
                listMessage(text) {
                    return `<div class="text-center text-body-secondary py-5 px-4">${this.escapeHtml(text)}</div>`;
                }

                renderLowStock(lowStock) {
                    const statuses = {
                        out: ['Habis', 'text-bg-danger'],
                        below: ['Di bawah minimum', 'bg-danger bg-opacity-10 text-body border'],
                        reached: ['Mencapai minimum', 'border text-body-secondary'],
                    };

                    if (lowStock.items.length === 0) {
                        $('#lowStockBody').html(this.listMessage('Semua produk aktif memiliki stok di atas batas minimum.'));
                        $('#lowStockFooter').addClass('d-none');
                        return;
                    }

                    $('#lowStockBody').html(lowStock.items.map((item) => {
                        const [label, classes] = statuses[item.status] ?? [item.status, 'border'];
                        const difference = Number(item.difference);

                        return `
                            <div class="list-group-item px-5 py-3">
                                <div class="d-flex align-items-start justify-content-between gap-3">
                                    <div style="min-width: 0;">
                                        <div class="fw-semibold text-break">${this.escapeHtml(item.name ?? '-')}</div>
                                        <div class="small text-body-secondary text-break">${this.escapeHtml(item.code)} &bull; ${this.escapeHtml(item.category_name ?? '-')}</div>
                                    </div>
                                    <span class="badge ${classes} flex-shrink-0">${label}</span>
                                </div>

                                <div class="d-flex flex-wrap gap-3 small mt-2">
                                    <span><span class="text-body-secondary">Stok</span> <span class="fw-semibold">${this.number.format(Number(item.current_stock))}</span></span>
                                    <span><span class="text-body-secondary">Minimum</span> <span class="fw-semibold">${this.number.format(Number(item.minimum_stock))}</span></span>
                                    <span><span class="text-body-secondary">Selisih</span> <span class="fw-semibold">${difference > 0 ? '+' : ''}${this.number.format(difference)}</span></span>
                                </div>
                            </div>
                        `;
                    }).join(''));

                    $('#lowStockFooter')
                        .removeClass('d-none')
                        .text(
                            `Menampilkan ${lowStock.items.length} dari ${lowStock.total} produk yang stoknya menipis (paling mendesak lebih dahulu).`
                        );
                }

                /**
                 * Memuat grafik penjualan bulanan satu tahun.
                 */
                async loadChart() {
                    $('#chartError').addClass('d-none');
                    $('#chartWrapper').removeClass('d-none');

                    try {
                        const {
                            data
                        } = await this.request(@json(route('dashboard.sales')), {
                            year: $('#chartYear').val()
                        });

                        $('#chartEmpty').toggleClass('d-none', data.has_data);
                        this.renderChart(data);
                    } catch (error) {
                        $('#chartWrapper').addClass('d-none');
                        $('#chartEmpty').addClass('d-none');
                        $('#chartError').removeClass('d-none');
                    }
                }

                /**
                 * Grouped bar chart: quantity (sumbu kiri) dan nilai Rupiah (sumbu kanan).
                 */
                renderChart(data) {
                    const css = getComputedStyle(document.documentElement);
                    const primary = css.getPropertyValue('--bs-primary').trim() || '#2c7be5';
                    const secondary = css.getPropertyValue('--bs-secondary').trim() || '#6e84a3';

                    this.chart?.destroy();

                    this.chart = new Chart(document.getElementById('salesChart'), {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                    label: 'Quantity',
                                    data: data.quantities.map(Number),
                                    backgroundColor: primary,
                                    borderRadius: 4,
                                    yAxisID: 'yQuantity',
                                },
                                {
                                    label: 'Nilai Penjualan',
                                    data: data.amounts.map(Number),
                                    backgroundColor: secondary,
                                    borderRadius: 4,
                                    yAxisID: 'yValue',
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: (context) => context.datasetIndex === 0 ?
                                            `Quantity: ${this.number.format(context.parsed.y)}` :
                                            `Nilai: ${this.money.format(context.parsed.y)}`,
                                    },
                                },
                            },
                            scales: {
                                yQuantity: {
                                    type: 'linear',
                                    position: 'left',
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Quantity (satuan dasar)'
                                    },
                                    ticks: {
                                        callback: (value) => this.number.format(value)
                                    },
                                },
                                yValue: {
                                    type: 'linear',
                                    position: 'right',
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Nilai (Rp)'
                                    },
                                    grid: {
                                        drawOnChartArea: false
                                    },
                                    ticks: {
                                        callback: (value) => this.compactMoney.format(value)
                                    },
                                },
                            },
                        },
                    });
                }

                /**
                 * Memuat produk terlaris dan tidak laku pada periode terpilih.
                 */
                async loadProducts() {
                    $('#fastBody').html(this.messageRow(4, 'Memuat data...'));
                    $('#slowBody').html(this.messageRow(5, 'Memuat data...'));

                    try {
                        const {
                            data
                        } = await this.request(@json(route('dashboard.products')), {
                            period: $('#productPeriod').val()
                        });
                        const from = this.date.format(new Date(data.from + 'T00:00:00'));
                        const to = this.date.format(new Date(data.to + 'T00:00:00'));

                        $('#productPeriodLabel').text(`Periode ${data.period_label.toLowerCase()}: ${from} - ${to}`);

                        $('#fastBody').html(data.fast_moving.length === 0 ?
                            this.messageRow(4, 'Belum ada penjualan pada periode ini.') :
                            data.fast_moving.map((item, index) => `
                                <tr>
                                    <td class="ps-5"><span class="dash-rank rounded-circle d-inline-flex align-items-center justify-content-center fw-semibold ${index < 3 ? 'bg-success bg-opacity-10 border' : 'border text-body-secondary'}">${index + 1}</span></td>
                                    <td><div>${this.escapeHtml(item.name ?? '-')}</div><div class="small text-body-secondary">${this.escapeHtml(item.code)}</div></td>
                                    <td class="text-end text-nowrap">${this.number.format(Number(item.quantity))}</td>
                                    <td class="text-end text-nowrap pe-5">${this.money.format(Number(item.value))}</td>
                                </tr>
                            `).join(''));

                        $('#slowBody').html(data.slow_moving.length === 0 ?
                            this.messageRow(5, 'Tidak ada produk aktif yang memiliki stok pada periode ini.') :
                            data.slow_moving.map((item) => `
                                <tr>
                                    <td class="ps-5"><div>${this.escapeHtml(item.name ?? '-')}</div><div class="small text-body-secondary">${this.escapeHtml(item.code)}</div></td>
                                    <td class="text-end text-nowrap">${this.number.format(Number(item.current_stock))}</td>
                                    <td class="text-end text-nowrap">${this.number.format(Number(item.quantity))}</td>
                                    <td class="text-end text-nowrap">${this.money.format(Number(item.value))}</td>
                                    <td class="pe-5 text-nowrap"><span class="badge ${item.sold ? 'border text-body-secondary' : 'text-bg-danger'}">${item.sold ? 'Penjualan Rendah' : 'Tidak Terjual'}</span></td>
                                </tr>
                            `).join(''));
                    } catch (error) {
                        $('#fastBody').html(this.messageRow(4,
                            'Gagal memuat data. Pilih periode lagi untuk mencoba kembali.'));
                        $('#slowBody').html(this.messageRow(5,
                            'Gagal memuat data. Pilih periode lagi untuk mencoba kembali.'));
                    }
                }
            }

            window.service = new Service();
            service.renderPeriodLabels();

            $('#chartYear').on('change', () => service.loadChart());
            $('#buttonRetryChart').on('click', () => service.loadChart());
            $('#productPeriod').on('change', () => service.loadProducts());

            $(document).ready(function() {
                if (service.canSale || service.canProduct) service.loadSummary();
                if (service.canSale) {
                    service.loadChart();
                    service.loadProducts();
                }
            });
        </script>
    </x-slot:script>
</x-layouts.auth>
