<x-layouts.auth title="Pembelian" :access="$access">
    <x-page-header title="Pembelian" icon="add_shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end align-items-center">
                @if ($access?->create)
                    <div class="col-12 col-lg-6 col-xl-4">
                        <a href="{{ route('transactions.purchases.cart') }}" class="btn btn-secondary w-100" role="button">
                            <x-icon class="me-1">add</x-icon>
                            <span>Tambah</span>
                        </a>
                    </div>
                @endif
            </div>
        </x-slot:action>
    </x-page-header>

    <div class="row mt-6">
        <div class="col-12">
            <form action="{{ route('transactions.purchases') }}" method="get" autocomplete="off" id="formFilter">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h2 class="fs-5 mb-0">Filter</h2>
                    </div>

                    <div class="card-body">
                        <div class="row align-items-top">
                            <div class="col-12 col-lg-4 mb-6 mb-lg-0">
                                <div class="form-floating" data-invalid="from_date">
                                    <input
                                        type="text"
                                        name="from_date"
                                        id="from_date"
                                        placeholder="Dari tanggal"
                                        class="form-control"
                                        data-invalid="from_date"
                                        value="{{ request('from_date') }}"
                                        data-flatpickr=""
                                    />

                                    <label for="fromDate">
                                        Dari tanggal <span class="text-danger">*</span>
                                    </label>
                                </div>

                                <div class="invalid-feedback" data-invalid-feedback="from_date"></div>
                            </div>

                            <div class="col-12 col-lg-4 mb-6 mb-lg-0">
                                <div class="form-floating" data-invalid="to_date">
                                    <input
                                        type="text"
                                        name="to_date"
                                        id="to_date"
                                        placeholder="Sampai tanggal"
                                        class="form-control"
                                        data-invalid="to_date"
                                        value="{{ request('to_date') }}"
                                        data-flatpickr=""
                                    />

                                    <label for="toDate">
                                        Sampai tanggal <span class="text-danger">*</span>
                                    </label>
                                </div>

                                <div class="invalid-feedback" data-invalid-feedback="to_date"></div>
                            </div>

                            <div class="col-12 col-lg-4">
                                <div class="form-floating" data-invalid="payment_status">
                                    <select name="payment_status" id="payment_status" class="form-select" data-invalid="payment_status">
                                        <option value="all" @selected(request('payment_status', 'all') == 'all')>Semua</option>
                                        <option value="paid" @selected(request('payment_status', 'all') == 'paid')>Lunas</option>
                                        <option value="partial" @selected(request('payment_status', 'all') == 'partial')>Dibayar sebagian</option>
                                        <option value="unpaid" @selected(request('payment_status', 'all') == 'unpaid')>Belum dibayar</option>
                                    </select>

                                    <label for="toDate">
                                        Status pembayaran <span class="text-danger">*</span>
                                    </label>
                                </div>

                                <div class="invalid-feedback" data-invalid-feedback="payment_status"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row justify-content-end">
                            <div class="col-12 col-lg-4 col-xl-2">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <x-icon class="me-1">filter_list</x-icon>
                                    <span>Submit</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row mt-6">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="tablePurchase" class="table table-sm table-hover align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>

        {{-- CLass form filter --}}
        <script>
            class Service {
                constructor() {
                    this.formFilter = $('#formFilter');
                    this.tablePurchase = this.purchaseDataTable();
                    this.access = this.getAccess();
                }

                /**
                 * Mengabil akses user.
                 */
                getAccess() {
                    return {
                        create: $('html').data('create') === 1,
                        read: $('html').data('read') === 1,
                        update: $('html').data('update') === 1,
                        remove: $('html').data('remove') === 1,
                        delete: $('html').data('delete') === 1,
                    };
                }

                /**
                 * Purchase datatable
                 */
                purchaseDataTable() {
                    return app.buttonDataTable('#tablePurchase', {
                        fixedColumns: {
                            start: 0,
                            end: 1,
                        },
                        columns: [
                            {
                                title: 'Pemasok',
                                name: 'suppliers.name',
                                data: 'supplier.name',
                                className: 'text-nowrap',
                                type: 'string',
                            },
                            {
                                title: 'Nomor Faktur',
                                name: 'purchases.invoice_number',
                                data: 'invoice_number',
                                className: 'text-nowrap',
                            },
                            {
                                title: 'Tanggal Pembelian',
                                name: 'purchases.purchase_date',
                                data: 'purchase_date',
                                className: 'text-nowrap',
                                render: (data) => app.dateFormat(data),
                            },
                            {
                                title: 'Total Quantity',
                                name: 'purchases.total_quantity',
                                data: 'total_quantity',
                                className: 'text-nowrap',
                                type: 'num',
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Total Harga',
                                name: 'purchases.total_price',
                                data: 'total_price',
                                className: 'text-nowrap',
                                type: 'num',
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Pembayaran',
                                name: 'paid_amount',
                                data: 'paid_amount',
                                className: 'text-nowrap',
                                type: 'num',
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Kurang Bayar',
                                className: 'text-nowrap',
                                type: 'num',
                                render: (data, type, row) => app.numberFormat(row.total_price - row.paid_amount),
                            },
                            {
                                title: 'Status Pembayaran',
                                name: 'purchases.payment_status',
                                data: 'payment_status',
                                className: 'text-nowrap',
                                render: (data) => {
                                    if (data === 'paid') return `<span class="badge bg-success-subtle text-success fw-bold">${data.toUpperCase()}</span>`;
                                    if (data === 'partial') return `<span class="badge bg-warning-subtle text-warning fw-bold">${data.toUpperCase()}</span>`;
                                    return `<span class="badge bg-danger-subtle text-danger fw-bold">${data.toUpperCase()}</span>`;
                                }
                            },
                            {
                                title: `<span class="material-symbols-outlined">settings</span>`,
                                name: 'id',
                                data: 'id',
                                className: 'text-center text-nowrap no-export',
                                orderable: false,
                                searchable: false,
                                render: (data, type, row) => {
                                    let html = '';

                                    if (this.access.read) {
                                        html += `
                                            <a class="btn btn-light" href="${app.baseUrl}/transactions/purchases/${data}" title="Detail" target="_blank">
                                                <span class="material-symbols-outlined fs-6">visibility</span>
                                            </a>
                                        `;
                                    }

                                    if (this.access.create) {
                                        html += `
                                            <a class="btn btn-light" href="${app.baseUrl}/transactions/purchases/${data}/payments" title="Pembayaran" target="_blank">
                                                <span class="material-symbols-outlined fs-6">payments</span>
                                            </a>
                                        `;
                                    }

                                    if (this.access.remove) {
                                        html += `
                                            <button type="button" class="btn btn-light" title="Hapus" onclick="removePurchase(event, '${data}')">
                                                <span class="material-symbols-outlined fs-6">delete</span>
                                            </button>
                                        `;
                                    }

                                    return html;
                                }
                            }
                        ],
                    });
                }

                /**
                 * Submit form filter
                 */
                getPurchases() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "get",
                            url: "{{ route('transactions.purchases') }}",
                            data: this.formFilter.serialize(),
                            dataType: "json",
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }

                /**
                 * Hapus pembelian
                 */
                remove(id) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "delete",
                            url: app.url(`/transactions/purchases/${id}`),
                            data: {
                                _method: 'delete',
                                _token: app.csrfToken,
                            },
                            dataType: "json",
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    })
                }
            }

            window.service = new Service();
        </script>

        {{-- Global script --}}
        <script>
            $(document).ready(async function () {
                const param = new URLSearchParams(window.location.search);
                const fromDate = param.get('from_date'); 
                const toDate = param.get('to_date'); 
                const paymentStatus = param.get('payment_status');

                if (fromDate === null || toDate === null || paymentStatus === null) {
                    return;
                }

                app.clearFormErrors(service.formFilter);
                app.showPreloader();

                try {
                    const response = await service.getPurchases();

                    service.tablePurchase.clear().draw();
                    service.tablePurchase.rows.add(response.data).draw();
                } catch (error) {
                    app.formErrors(service.formFilter, error);
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Submit form filter
             */
            service.formFilter.submit(async (e) => {
                e.preventDefault();

                const fromDate = service.formFilter.find('input[name="from_date"]').val();
                const toDate = service.formFilter.find('input[name="to_date"]').val();
                const paymentStatus = service.formFilter.find('select[name="payment_status"]').val();

                const params = {
                    from_date: fromDate,
                    to_date: toDate,
                    payment_status: paymentStatus,
                };

                app.updateQueryParams(params);
                app.clearFormErrors(service.formFilter);
                app.showPreloader();

                try {
                    const response = await service.getPurchases();

                    service.tablePurchase.clear().draw();
                    service.tablePurchase.rows.add(response.data).draw();
                } catch (error) {
                    app.formErrors(service.formFilter, error);
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Fungsi untuk menghapus pembelian
             */
            const removePurchase = (event, id) => {
                event.preventDefault();

                app.delete('Transaksi Pembelian', async (removed) => {
                    if (!removed) return;

                    app.showPreloader();

                    try {
                        const removed = await service.remove(id);
                        const purchases = await service.getPurchases();

                        app.alert('success', removed.message);
                        service.tablePurchase.clear().draw();
                        service.tablePurchase.rows.add(purchases.data).draw();
                    } catch (error) {
                        app.alert('danger', error.message);
                    } finally {
                        app.hidePreloader();
                    }
                })
            }
        </script>
    
    </x-slot:script>
</x-layouts.auth>