<x-layouts.sales :active="$active" :access="$access">
    <div class="row mt-7">
        <div class="col-12">
            <form action="{{ route('transactions.sales.return') }}" method="get" id="formFilter">
                @csrf @method('GET')

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-6 mb-lg-0">
                                <x-form-floating-input
                                    type="text"
                                    name="from_date"
                                    id="fromDate"
                                    label="Dari tanggal"
                                    value="{{ request('from_date') }}"
                                    data-flatpickr=""
                                    required
                                />
                            </div>

                            <div class="col-12 col-lg-6">
                                <x-form-floating-input
                                    type="text"
                                    name="to_date"
                                    id="toDate"
                                    label="Sampai tanggal"
                                    value="{{ request('to_date') }}"
                                    data-flatpickr=""
                                    required
                                />
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row justify-content-end">
                            <div class="col-12 col-lg-4 col-xl-2">
                                <x-button type="submit" start-icon="filter_list" full-width>Filter</x-button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row mt-7">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="tableSaleReturn" class="table table-hover align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.access = this.getAccess();
                    this.formFilter = $('#formFilter');
                    this.tableSaleReturn = this.tableSaleReturn();
                }

                /**
                 * Mengambil akses izin user
                 */
                getAccess() {
                    return {
                        create: $('html').data('create') === 1,
                        read: $('html').data('read') === 1,
                        update: $('html').data('update') === 1,
                        remove: $('html').data('remove') === 1,
                        delete: $('html').data('delete') === 1,
                    }
                }

                /**
                 * Kolom untuk tabel sales
                 */
                tableSaleReturnColumns() {
                    const columns = [
                        {
                            title: 'Nomor Pengembalian',
                            name: 'return_number',
                            data: 'return_number',
                            className: 'px-3 text-nowrap',
                            type: 'string',
                        },
                        {
                            title: 'Nomor Faktur Penjualan',
                            name: 'sale.invoice_number',
                            data: 'sale.invoice_number',
                            className: 'px-3 text-nowrap',
                            type: 'string',
                        },
                        {
                            title: 'Tanggal',
                            name: 'transaction_date',
                            data: 'transaction_date',
                            className: 'px-3 text-nowrap',
                            type: 'date',
                            render: (data) => app.dateFormat(data),
                        },
                        {
                            title: 'Quantity',
                            name: 'total_quantity',
                            data: 'total_quantity',
                            className: 'px-3 text-nowrap',
                            type: 'num-fmt',
                            render: (data) => app.numberFormat(data),
                        },
                        {
                            title: 'Total',
                            name: 'total_amount',
                            data: 'total_amount',
                            className: 'px-3 text-nowrap',
                            type: 'num-fmt',
                            render: (data) => app.numberFormat(data),
                        },
                    ];


                    if (this.access.remove) {
                        columns.push({
                            title: `<span class="material-symbols-outlined">settings</span>`,
                            name: 'id',
                            data: 'id',
                            className: 'text-nowrap text-center no-export',
                            type: 'html',
                            orderable: false,
                            searchable: false,
                            render: (data) => {
                                return `
                                    <button type="button" class="btn btn-light" title="Hapus" onclick="removeSaleReturn(event, '${data}')">
                                        <span class="material-symbols-outlined fs-6">delete</span>
                                    </button>
                                `;
                            },
                        });
                    }

                    return columns;
                }

                /**
                 * Inisialisasi datatable sales.
                 */
                tableSaleReturn() {
                    return app.buttonDataTable('#tableSaleReturn', {
                        fixedColumns: {
                            start: 0,
                            end: 1,
                        },
                        order: [[2, 'asc']],
                        columns: this.tableSaleReturnColumns(),
                    });
                }

                /**
                 * Request data sales
                 */
                getSaleReturns() {
                    return new Promise((resollve, reject) => {
                        $.ajax({
                            type: "get",
                            url: this.formFilter.attr('action'),
                            data: this.formFilter.serialize(),
                            dataType: "json",
                            success: (response) => resollve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }

                /**
                 * Hapus sales
                 */
                removeSaleReturn(id) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "delete",
                            url: app.url(`/transactions/sales/return/${id}`),
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
                const params = new URLSearchParams(window.location.search);
                const fromDate = params.get('from_date');
                const toDate = params.get('to_date');

                if (fromDate !== null && toDate !== null) {
                    app.showPreloader();
                    app.clearFormErrors(service.formFilter);

                    try {
                        const response = await service.getSaleReturns();
                        const table = service.tableSaleReturn;

                        table.clear().draw();
                        table.rows.add(response.data).draw();
                    } catch (error) {
                        app.setFormErrors(service.formFilter, error);
                    } finally {
                        app.hidePreloader();
                    }
                }
            });

            /**
             * Submit form filter.
             */
            service.formFilter.submit(async function (e) { 
                e.preventDefault();
                
                const fromDate = service.formFilter.find('input[name="from_date"]').val();
                const toDate = service.formFilter.find('input[name="to_date"]').val();
                
                const params = {
                    from_date: fromDate,
                    to_date: toDate,
                };
                
                app.updateQueryParams(params);
                app.showPreloader();
                app.clearFormErrors(service.formFilter);

                try {
                    const response = await service.getSaleReturns();
                    const table = service.tableSaleReturn;

                    table.clear().draw();
                    table.rows.add(response.data).draw();
                } catch (error) {
                    app.setFormErrors(service.formFilter, error);
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Fungsi untuk remove sales
             */
            const removeSaleReturn = (event, id) => {
                event.preventDefault();

                app.delete('Transaksi Pengembalian', async (deleted) => {
                    if (!deleted) return;

                    app.showPreloader();
    
                    try {
                        const removed = await service.removeSaleReturn(id);
                        const sales = await service.getSaleReturns();
    
                        app.alert('success', removed.message);
                        service.tableSaleReturn.clear().draw();
                        service.tableSaleReturn.rows.add(sales.data).draw();
                    } catch (error) {
                        app.alert('danger', error.message);
                    } finally {
                        app.hidePreloader();
                    }
                });
            }
        </script>
    </x-slot:script>
</x-layouts.sales>