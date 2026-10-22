
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
                    <form id="sales-filter-form">
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-lg-4 col-xl-5">
                                <x-form-input
                                    type="text"
                                    name="start_date"
                                    id="start_date"
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
                                    id="end_date"
                                    label="Tanggal Akhir"
                                    value="{{ request('end_date', date('Y-m-d')) }}"
                                    data-flatpickr=""
                                    required
                                />
                            </div>
    
                            <div class="col-12 col-lg-4 col-xl-2 mt-6 mt-lg-0">
                                <x-button type="submit" id="btn-filter-sales" startIcon="search" fullWidth>
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
                        <table id="sales-table" class="table table-sm table-hover align-middle w-100"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    <x-slot:script>
        <script>
            $(function () {
                const $form = $('#sales-filter-form');
                const $startDate = $('#start_date');
                const $endDate = $('#end_date');
                const $submitButton = $('#btn-filter-sales');

                const access = {
                    read: $('html').data('read') === 1,
                    create: $('html').data('create') === 1,
                    update: $('html').data('update') === 1,
                    remove: $('html').data('remove') === 1,
                    delete: $('html').data('delete') === 1,
                }

                let currentRequest = null;

                /**
                 * Inisialisasi DataTables melalui helper app.
                 */
                const table = app.dataTable('#sales-table', {
                    order: [[0, 'asc'], [1, 'asc']],
                    fixedColumns: {
                        start: 0,
                        end: 1,
                    },
                    columns: [
                        {
                            data: 'transaction_date',
                            title: 'Tanggal Transaksi',
                            className: 'text-nowrap',
                            type: 'date',
                            render: (data) => formatDate(data)
                        },
                        {
                            data: 'invoice_number',
                            title: 'No. Invoice',
                            className: 'text-nowrap',
                            type: 'string',
                            render: (data) => escapeHtml(data ?? '-')
                        },
                        {
                            data: 'user',
                            title: 'Kasir',
                            className: 'text-nowrap',
                            type: 'string',
                            render: (data) => escapeHtml(getPersonName(data))
                        },
                        {
                            data: 'customer',
                            title: 'Pelanggan',
                            className: 'text-nowrap',
                            type: 'string',
                            render: (data) => escapeHtml(getPersonName(data))
                        },
                        {
                            data: 'total_product',
                            title: 'Total Produk',
                            className: 'text-nowrap',
                            type: 'string',
                            render: (data) => app.numberFormat(Number(data ?? 0))
                        },
                        {
                            data: 'total_quantity',
                            title: 'Total Kuantitas',
                            className: 'text-nowrap',
                            type: 'num',
                            render: (data) => app.numberFormat(Number(data ?? 0))
                        },
                        {
                            data: 'total_amount',
                            title: 'Total Belanja',
                            className: 'text-nowrap',
                            render: (data) => app.numberFormat(Number(data ?? 0))
                        },
                        {
                            data: 'discount',
                            title: 'Diskon (%)',
                            className: 'text-nowrap',
                            type: 'num',
                            render: (data) => app.numberFormat(Number(data ?? 0))
                        },
                        {
                            data: 'grand_total',
                            title: 'Total Akhir',
                            className: 'text-nowrap fw-semibold',
                            type: 'num',
                            render: (data) => app.numberFormat(Number(data ?? 0))
                        },
                        {
                            data: 'payment_method',
                            title: 'Metode Pembayaran',
                            className: 'text-nowrap',
                            type: 'string',
                            render: (data) => escapeHtml(getPaymentMethod(data))
                        },
                        {
                            data: null,
                            title: app.icon('settings', 'fs-6'),
                            className: 'text-nowrap text-center no-export',
                            searchable: false,
                            orderable: false,
                            render: function (data, type, row) {
                                let html = `
                                    <a
                                        href="${app.baseUrl}/transactions/sales/${data.id}/receipt"
                                        target="_blank"
                                        role="button"
                                        class="btn btn-sm btn-light rounded"
                                        title="Cetak struk"
                                        aria-label="Cetak struk"
                                    >
                                        ${app.icon('receipt_long', 'fs-6')}
                                    </a>
                                `;

                                if (access.remove) {
                                    html += `
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-light rounded btn-delete-sale"
                                            data-id="${escapeHtml(row.id)}"
                                            title="Hapus Penjualan"
                                            aria-label="Hapus Penjualan"
                                        >
                                            ${app.icon('delete', 'fs-6')}
                                        </button>
                                    `;
                                }

                                return html;
                            }
                        }
                    ],
                });

                /**
                 * Menghindari HTML dari data server dirender sebagai markup.
                 */
                function escapeHtml(value) {
                    return $('<div>').text(String(value ?? '')).html();
                }

                /**
                 * Mendukung relasi user dan customer dalam bentuk object.
                 */
                function getPersonName(value) {
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
                 * Format tanggal tanpa menggeser tanggal akibat timezone.
                 */
                function formatDate(value) {
                    if (!value) {
                        return '-';
                    }

                    const match = String(value).match(
                        /^(\d{4})-(\d{2})-(\d{2})/
                    );

                    return match
                        ? `${match[3]}-${match[2]}-${match[1]}`
                        : escapeHtml(value);
                }
                
                /**
                 * Mengambil jenis pembayaran.
                 */
                function getPaymentMethod(value) {
                    const labels = {
                        cash: 'Tunai',
                        qris: 'QRIS',
                        debit: 'Debit',
                        credit: 'Kredit'
                    };

                    return labels[value] ?? value ?? '-';
                }

                /**
                 * Mengambil data berdasarkan rentang tanggal.
                 */
                function loadSales(updateUrl = true) {
                    const startDate = $startDate.val();
                    const endDate = $endDate.val();

                    app.clearAlert();
                    app.clearFormErrors($form);

                    if (!startDate || !endDate) {
                        app.alert('warning', 'Tanggal mulai dan tanggal akhir wajib diisi.');
                        return;
                    }

                    if (endDate < startDate) {
                        app.alert('warning', 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.');
                        return;
                    }

                    if (updateUrl) {
                        app.updateQueryParams({
                            start_date: startDate,
                            end_date: endDate
                        });
                    }

                    // Batalkan request sebelumnya jika masih berjalan.
                    if (currentRequest) {
                        currentRequest.abort();
                    }

                    $submitButton.prop('disabled', true);
                    app.showPreloader();

                    currentRequest = $.ajax({
                        url: @json(route('transactions.sales')),
                        method: 'GET',
                        dataType: 'json',
                        data: {
                            start_date: startDate,
                            end_date: endDate
                        },
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .done(function (response) {
                        if (!Array.isArray(response.data)) {
                            table.clear().draw();

                            app.alert('danger', 'Format respons data penjualan tidak valid.');
                            return;
                        }

                        table.clear();
                        table.rows.add(response.data);
                        table.draw();

                        if (response.message) {
                            app.clearAlert();
                        }
                    })
                    .fail(function (xhr, textStatus) {
                        if (textStatus === 'abort') {
                            return;
                        }

                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            app.setFormErrors($form, xhr.responseJSON);
                            app.alert(
                                'danger',
                                'Periksa kembali rentang tanggal yang Anda masukkan.'
                            );
                            return;
                        }

                        app.alert('danger', xhr.responseJSON?.message ?? 'Terjadi kesalahan saat mengambil data penjualan.'
                        );
                    })
                    .always(function () {
                        currentRequest = null;
                        $submitButton.prop('disabled', false);
                        app.hidePreloader();
                    });
                }

                /**
                 * Submit form tanpa reload halaman.
                 */
                $form.on('submit', function (event) {
                    event.preventDefault();
                    loadSales();
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
                        String(today.getDate()).padStart(2, '0')
                    ].join('-');

                    $startDate.val(params.get('start_date') || todayString);
                    $endDate.val(params.get('end_date') || todayString);

                    loadSales(false);
                });

                /**
                 * Menghapus data penjualan.
                 */
                $('#sales-table').on('click', '.btn-delete-sale', function () {
                    const saleId = $(this).data('id');

                    app.delete('Penjualan', function (confirmed) {
                        if (!confirmed) {
                            return;
                        }

                        app.showPreloader();

                        $.ajax({
                            url: app.url(`/transactions/sales/${saleId}/remove`),
                            method: 'DELETE',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': app.csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .done((response) => {
                            loadSales(false);
                            app.alert('success', response.message ?? 'Data penjualan berhasil dihapus.');
                        })
                        .fail(function (xhr) {
                            app.alert('danger', xhr.responseJSON?.message ?? 'Terjadi kesalahan saat menghapus data penjualan.');
                        })
                        .always(function () {
                            app.hidePreloader();
                        });
                    });
                });

                /**
                 * Request pertama saat halaman dibuka.
                 */
                loadSales(false);
            });
        </script>
    </x-slot:script>
</x-layouts.auth>
