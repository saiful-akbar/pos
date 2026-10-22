
<x-layouts.auth title="Penjualan" :access="$access">
    {{-- Header --}}
    <x-page-header title="Penjualan" icon="shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end">
                @if ($access?->create ?? false)
                    <div class="col-12 col-lg-6 col-xl-4">
                        <x-button
                            type="link"
                            href="{{ route('transactions.sales.carts') }}"
                            id="buttonNewSale"
                            color="secondary"
                            startIcon="add_shopping_cart"
                            fullWidth
                        >
                            Mulai Transaksi
                        </x-button>
                    </div>
                @endif
            </div>
        </x-slot:action>
    </x-page-header>

    {{-- Filter tanggal --}}
    <div class="row gx-7">
        <div class="col-12 mt-7">
            <div class="card shadow-sm mb-4 sales-filter-card">
                <div class="card-header">
                    <h3 class="fs-6 mb-0">
                        Filter Data Penjualan
                    </h3>
                </div>

                <div class="card-body">
                    <form id="formSales">
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-lg-4 col-xl-5">
                                <x-form-input
                                    type="text"
                                    name="start_date"
                                    id="startDate"
                                    label="Tanggal Mulai"
                                    value="{{ request('start_date', date('Y-m-d')) }}"
                                    data-flatpickr=""
                                    required
                                />
                            </div>

                            <div class="col-12 col-lg-4 col-xl-5 mt-6 mt-lg-0">
                                <x-form-input
                                    type="text"
                                    name="end_date"
                                    id="endDate"
                                    label="Tanggal Akhir"
                                    value="{{ request('end_date', date('Y-m-d')) }}"
                                    data-flatpickr=""
                                    required
                                />
                            </div>

                            <div class="col-12 col-lg-4 col-xl-2 mt-6 mt-lg-0">
                                <x-button type="submit" id="buttonFilterSales" startIcon="search" fullWidth>
                                    Tampilkan
                                </x-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel penjualan --}}
    <div class="row gx-7">
        <div class="col-12 mt-7">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="fs-6 mb-0">
                        Daftar Penjualan
                    </h3>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="salesTable" class="table table-sm table-hover align-middle w-100"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.access = this.#getAccess();
                    this.form = $('#formSales');
                    this.startDate = $('#startDate');
                    this.endDate = $('#endDate');
                    this.buttonFilter = $('#buttonFilterSales');
                    this.buttonNewSale = $('#buttonNewSale');
                    this.table = this.#salesDataTableInit();
                    this.currentRequest = null;
                }

                /**
                 * Mengambil hak akses user.
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
                 * Inisialisasi sales datatables.
                 */
                #salesDataTableInit() {
                    const columns = [
                        {
                            title: 'Tanggal',
                            name: 'transaction_date',
                            data: 'transaction_date',
                            className: 'text-nowrap',
                            type: 'date',
                            render: (data) => this.#formatDate(data),
                        },
                        {
                            title: 'Nomor Faktur',
                            name: 'invoice_number',
                            data: 'invoice_number',
                            className: 'text-nowrap',
                            type: 'string',
                            render: (data) => this.#escapeHtml(data ?? '-'),
                        },
                        {
                            title: 'Kasir',
                            name: 'user',
                            data: 'user',
                            className: 'text-nowrap',
                            type: 'string',
                            render: (data) => this.#escapeHtml(this.#getPersonName(data)),
                        },
                        {
                            title: 'Pelanggan',
                            name: 'customer',
                            data: 'customer',
                            className: 'text-nowrap',
                            type: 'string',
                            render: (data) => this.#escapeHtml(this.#getPersonName(data)),
                        },
                        {
                            title: 'Total Produk',
                            name: 'total_product',
                            data: 'total_product',
                            className: 'text-nowrap',
                            type: 'string',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                        {
                            title: 'Total Qty',
                            name: 'total_quantity',
                            data: 'total_quantity',
                            className: 'text-nowrap',
                            type: 'num',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                        {
                            title: 'Total Belanja',
                            name: 'total_amount',
                            data: 'total_amount',
                            className: 'text-nowrap',
                            type: 'num',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                        {
                            title: 'Diskon (%)',
                            name: 'discount',
                            data: 'discount',
                            className: 'text-nowrap',
                            type: 'num',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                        {
                            title: 'Grand Total',
                            name: 'grand_total',
                            data: 'grand_total',
                            className: 'text-nowrap fw-semibold',
                            type: 'num',
                            render: (data) => app.numberFormat(Number(data ?? 0)),
                        },
                        {
                            title: 'Metode Pembayaran',
                            name: 'payment_method',
                            data: 'payment_method',
                            className: 'text-nowrap',
                            type: 'string',
                            render: (data) => this.#escapeHtml(this.#getPaymentMethod(data)),
                        },
                        {
                            title: app.icon('settings', 'fs-6'),
                            name: 'id',
                            data: 'id',
                            className: 'text-nowrap text-center no-export',
                            type: 'html',
                            searchable: false,
                            orderable: false,
                            render: (data) => {
                                let html = `
                                    <a
                                        href="${app.baseUrl}/transactions/sales/${data}/receipt"
                                        target="_blank"
                                        role="button"
                                        class="btn btn-sm btn-light rounded"
                                        title="Cetak struk"
                                        aria-label="Cetak struk"
                                    >
                                        ${app.icon('receipt_long', 'fs-6')}
                                    </a>
                                `;

                                if (this.access.remove) {
                                    html += `
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-light"
                                            data-action="remove"
                                            data-id="${this.#escapeHtml(data)}"
                                            title="Hapus Penjualan"
                                            aria-label="Hapus Penjualan"
                                        >
                                            ${app.icon('delete', 'fs-6')}
                                        </button>
                                    `;
                                }

                                return html;
                            },
                        },
                    ];

                    return app.dataTable('#salesTable', {
                        columns,
                        order: [[0, 'asc'], [1, 'asc']],
                        fixedColumns: {
                            start: 0,
                            end: this.access.remove ? 1 : 0,
                        },
                    });
                }

                /**
                 * Menghindari HTML dari data server dirender sebagai markup.
                 */
                #escapeHtml(value) {
                    return $('<div>').text(String(value ?? '')).html();
                }

                /**
                 * Mendukung relasi user dan customer dalam bentuk object.
                 */
                #getPersonName(value) {
                    if (typeof value === 'string') {
                        return value;
                    }

                    if (!value || typeof value !== 'object') {
                        return '-';
                    }

                    return value.name
                        ?? value.full_name
                        ?? value.fullName
                        ?? value.nama
                        ?? '-';
                }

                /**
                 * Mengambil jenis pembayaran.
                 */
                #getPaymentMethod(value) {
                    const labels = {
                        cash: 'Tunai',
                        qris: 'QRIS',
                        debit: 'Debit',
                        credit: 'Kredit'
                    };

                    return labels[value] ?? value ?? '-';
                }

                /**
                 * Format tanggal tanpa menggeser tanggal akibat timezone.
                 */
                #formatDate(value) {
                    if (!value) {
                        return '-';
                    }

                    const match = String(value).match(/^(\d{4})-(\d{2})-(\d{2})/);

                    return match
                        ? `${match[3]}-${match[2]}-${match[1]}`
                        : this.#escapeHtml(value);
                }

                /**
                 * Mengambil data penjualan berdasarkan rentang tanggal.
                 */
                getSales(startDate, endDate) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'get',
                            url: @json(route('transactions.sales')),
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
                 * Hapus (soft delete) data penjualan.
                 */
                remove(id) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'delete',
                            url: app.url(`/transactions/sales/${id}/remove`),
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
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }
            }

            window.service = new Service();
        </script>

        <script>
            "use strict"

            /**
             * Submit form filter.
             */
            service.form.submit(async function (e) {
                e.preventDefault();

                const startDate = service.startDate.val();
                const endDate = service.endDate.val();

                app.clearAlert();
                app.clearFormErrors(service.form);

                if (!startDate || !endDate) {
                    app.alert('warning', 'Tanggal mulai dan tanggal akhir wajib diisi.');
                    return;
                }

                if (endDate < startDate) {
                    app.alert('warning', 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.');
                    return;
                }

                app.updateQueryParams({
                    start_date: startDate,
                    end_date: endDate,
                });

                // Batalkan request sebelumnya jika masih berjalan.
                if (service.currentRequest) {
                    service.currentRequest.abort();
                }

                service.buttonFilter.prop('disabled', true);
                app.showPreloader();

                try {
                    const response = await service.getSales(startDate, endDate);
                    const { data, message } = response;

                    if (!Array.isArray(data)) {
                        service.table.clear().draw();
                        app.alert('danger', 'Format respons data penjualan tidak valid.');
                        return;
                    }

                    service.table.clear();
                    service.table.rows.add(data);
                    service.table.draw();

                    if (message) {
                        app.clearAlert();
                    }
                } catch (error) {
                    if (error?.status === 422 && error?.errors) {
                        app.setFormErrors(service.form, error);
                        app.alert('danger', 'Periksa kembali rentang tanggal yang Anda masukkan.');
                        return;
                    }

                    app.alert('danger', error?.message ?? 'Terjadi kesalahan saat mengambil data penjualan.');
                } finally {
                    service.currentRequest = null;
                    service.buttonFilter.prop('disabled', false);
                    app.hidePreloader();
                }
            });

            /**
             * Navigasi Back/Forward browser.
             */
            window.addEventListener('popstate', function () {
                const params = new URLSearchParams(window.location.search);

                const today = new Date();
                const todayString = [
                    today.getFullYear(),
                    String(today.getMonth() + 1).padStart(2, '0'),
                    String(today.getDate()).padStart(2, '0'),
                ].join('-');

                service.startDate.val(params.get('start_date') || todayString);
                service.endDate.val(params.get('end_date') || todayString);

                service.form.trigger('submit');
            });

            /**
             * Hapus data penjualan.
             */
            service.table.on('click', '[data-action="remove"]', function (e) {
                const id = $(this).data('id');

                app.delete('Penjualan', async (removed) => {
                    if (!removed) return;

                    app.showPreloader();

                    try {
                        const response = await service.remove(id);
                        const { status, message } = response;

                        if (status === 200) {
                            app.alert('success', message);
                            service.form.trigger('submit');
                        }
                    } catch (error) {
                        app.alert('danger', error?.message ?? 'Terjadi kesalahan saat menghapus data penjualan.');
                    } finally {
                        app.hidePreloader();
                    }
                });
            });

            /**
             * Request pertama saat halaman dibuka.
             */
            service.form.trigger('submit');
        </script>
    </x-slot:script>
</x-layouts.auth>
