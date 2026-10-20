<x-layouts.auth title="Pembelian" :access="$access">
    <x-page-header title="Pembelian" icon="shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end">
                @if ($access?->create ?? false)
                    <div class="col-12 col-lg-6 col-xl-4">
                        <x-button
                            type="link"
                            href="{{ route('transactions.purchases.carts') }}"
                            startIcon="add"
                            fullWidth
                        >
                            Tambah
                        </x-button>
                    </div>
                @endif
            </div>
        </x-slot:action>
    </x-page-header>

    <div class="row mt-7 gx-7">
        <div class="col-12">
            <form action="{{ route('transactions.purchases') }}" method="get" id="formFilter">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row gx-7">
                            <div class="col-12 col-lg-6">
                                <x-form-input
                                    required
                                    type="text"
                                    name="from_date"
                                    id="fromDateFilter"
                                    label="Tanggal awal"
                                    placeholder="Masukan tanggal awal..."
                                    error="{{ $errors->has('from_date') }}"
                                    value="{{ old('from_date', request('from_date', date('Y-m-d'))) }}"
                                    data-flatpickr=""
                                />
                            </div>
    
                            <div class="col-12 col-lg-6 mt-5 mt-lg-0">
                                <x-form-input
                                    required
                                    type="text"
                                    name="to_date"
                                    id="toDateFilter"
                                    label="Tanggal akhir"
                                    placeholder="Masukan tanggal akhir..."
                                    error="{{ $errors->has('to_date') }}"
                                    value="{{ old('to_date', request('to_date', date('Y-m-d'))) }}"
                                    data-flatpickr=""
                                />
                            </div>
                        </div>
                    </div>

                    <div class="card-footer w-100">
                        <div class="row justify-content-end">
                            <div class="col-12 col-lg-3 col-xl-2">
                                <x-button
                                    type="submit"
                                    color="secondary"
                                    startIcon="filter_list"
                                    fullWidth
                                >
                                    Filter
                                </x-button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row gx-7 mt-7">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="purchaseTable" class="table table-hover table-sm align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.formFilter = $('#formFilter');
                    this.purchaseTable = this.#purchaseDataTableInit();
                    this.access = this.#getAccess();
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
                 * Inisialisasi dataTables purchase.
                 */
                #purchaseDataTableInit() {
                    return app.buttonDataTable('#purchaseTable', {
                        scrollX: true,
                        fixedColumns: {
                            start: 0,
                            end: 1,
                        },
                        columns: [
                            {
                                title: 'Tanggal',
                                name: 'transaction_date',
                                data: 'transaction_date',
                                type: 'date',
                                className: 'text-nowrap',
                            },
                            {
                                title: 'Pemasok',
                                name: 'supplier.name',
                                data: 'supplier.name',
                                type: 'string',
                                className: 'text-nowrap',
                            },
                            {
                                title: 'Nomor Faktur',
                                name: 'invoice_number',
                                data: 'invoice_number',
                                type: 'string',
                                className: 'text-nowrap',
                            },
                            {
                                title: 'Total Produk',
                                name: 'total_product',
                                data: 'total_product',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data) => app.numberFormat(data, 0),
                            },
                            {
                                title: 'Total Qty',
                                name: 'total_quantity',
                                data: 'total_quantity',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Grand Total',
                                name: 'total_price',
                                data: 'total_price',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Kurang Bayar',
                                name: 'already_paid',
                                data: 'already_paid',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data, type, row) => {
                                    const underpayment = row.total_price- row.already_paid;

                                    if (underpayment <= 0) {
                                        return app.numberFormat(0);
                                    }

                                    return app.numberFormat(underpayment);
                                },
                            },
                            {
                                title: 'Status Pembayaran',
                                name: 'payment_status',
                                data: 'payment_status',
                                type: 'string',
                                className: 'text-nowrap',
                            },
                            {
                                title: 'Catatan',
                                name: 'notes',
                                data: 'notes',
                                type: 'string',
                                width: '200px',
                            },
                            {
                                title: app.icon('settings', 'fs-6'),
                                name: 'id',
                                data: 'id',
                                type: 'html',
                                className: 'text-nowrap text-center no-export',
                                searchable: false,
                                orderable: false,
                                render: (data) => {
                                    let html = `
                                        <a
                                            href="${app.baseUrl}/transactions/purchases/${data}"
                                            target="_blank"
                                            role="button"
                                            class="btn btn-light"
                                            title="Lihat detail"
                                        >
                                            ${app.icon('visibility', 'fs-6')}
                                        </a>
                                    `;

                                    if (this.access.remove) {
                                        html += `
                                            <button
                                                type="button"
                                                class="btn btn-light"
                                                title="Hapus"
                                                onclick="removePurchase(event, '${data}')"
                                            >
                                                ${app.icon('delete', 'fs-6')}
                                            </button>
                                        `;
                                    }

                                    return html;
                                },
                            },
                        ],
                    })
                }

                /**
                 * Mengambil data pembelian
                 */
                getPurchases() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "get",
                            url: this.formFilter.attr('action'),
                            data: this.formFilter.serialize(),
                            success: (response) => {
                                resolve(response);
                                this.purchaseTable.clear().draw();
                                this.purchaseTable.rows.add(response.data).draw();
                            },
                            error: (error) => {
                                reject(error.responseJSON);
                            },
                        });
                    });
                }

                /**
                 * Remove (soft delete)
                 */
                remove(purchaseId) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "delete",
                            url: app.url(`/transactions/purchases/${purchaseId}`),
                            data: {
                                _token: app.csrfToken,
                                _method: 'delete',
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
             * Ambil data pembelian setelah dokumen selesai dimuat.
             */
            $(document).ready(async function () {
                const fromDate = service.formFilter.find('input[name="from_date"]').val();
                const toDate = service.formFilter.find('input[name="to_date"]').val();

                app.updateQueryParams({
                    from_date: fromDate,
                    to_date: toDate,
                });

                try {
                    await service.getPurchases();
                } catch (error) {
                    app.setFormErrors(service.formFilter, error);
                }
            });

            /**
             * Submit form filter
             */
            service.formFilter.submit(async function (e) { 
                e.preventDefault();
                app.showPreloader();

                const fromDate = service.formFilter.find('input[name="from_date"]').val();
                const toDate = service.formFilter.find('input[name="to_date"]').val();

                app.updateQueryParams({
                    from_date: fromDate,
                    to_date: toDate,
                });

                try {
                    await service.getPurchases();
                } catch (error) {
                    app.setFormErrors(service.formFilter, error);
                } finally {
                    app.hidePreloader();
                }
                
            });

            /**
             * Remove (soft delete)
             */
            const removePurchase = (e, purchaseId) => {
                app.delete('Pembelian', async (deleted) => {
                    if (!deleted) return;

                    app.showPreloader();

                    try {
                        const response = await service.remove(purchaseId);
                        const { status, message } = response;

                        if (status === 200) {
                            app.alert('success', message);

                            await service.getPurchases();
                        }
                    } catch (error) {
                        app.alert('danger', error.message);
                    } finally {
                        app.hidePreloader();
                    }
                });
            }
        </script>
    </x-slot:script>
</x-layouts.auth>