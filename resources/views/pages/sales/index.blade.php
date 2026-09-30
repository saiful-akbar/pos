<x-layouts.sales :active="$active" :access="$access">
    <div class="row mt-7">
        <div class="col-12">
            <form action="{{ route('transactions.sales') }}" method="get" id="formFilter">
                @csrf @method('GET')

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-6 mb-lg-0">
                                <x-form-floating-input
                                    type="text"
                                    name="from_date"
                                    id="fromDateFilter"
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
                                    id="toDateFilter"
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
                    <table id="saleTable" class="table table-hover table-sm align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.formFilter = $('#formFilter');
                    this.saleTable = this.saleTable();
                    this.access = this.getAccess();
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
                 * Inisialisasi datatable sales.
                 */
                saleTable() {
                    return app.buttonDataTable('#saleTable', {
                        fixedColumns: {
                            start: 0,
                            end: 1,
                        },
                        order: [[1, 'asc']],
                        columns: [
                            {
                                title: 'Nomor Faktur',
                                name: 'invoice_number',
                                data: 'invoice_number',
                                className: 'text-nowrap',
                                type: 'string',
                            },
                            {
                                title: 'Tanggal',
                                name: 'transaction_date',
                                data: 'transaction_date',
                                className: 'text-nowrap',
                                type: 'date',
                                render: (data) => app.dateFormat(data),
                            },
                            {
                                title: 'Total Quantity',
                                name: 'total_quantity',
                                data: 'total_quantity',
                                className: 'text-nowrap',
                                type: 'num-fmt',
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Total Harga',
                                name: 'total_amount',
                                data: 'total_amount',
                                className: 'text-nowrap',
                                type: 'num-fmt',
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: `<span class="material-symbols-outlined">settings</span>`,
                                name: 'id',
                                data: 'id',
                                className: 'text-nowrap text-center no-export',
                                type: 'html',
                                orderable: false,
                                searchable: false,
                                render: (data) => {
                                    let html = `
                                        <a href="${app.baseUrl}/transactions/sales/${data}/receipt" class="btn btn-light" title="Cetak struk" target="_blank">
                                            <span class="material-symbols-outlined fs-6">receipt</span>
                                        </a>
                                    `;

                                    if (this.access.remove) {
                                        html += `
                                            <button type="button" class="btn btn-light" title="Hapus" onclick="removeSale(event, '${data}')">
                                                <span class="material-symbols-outlined fs-6">delete</span>
                                            </button>
                                        `;
                                    }

                                    return html;
                                },
                            },
                        ],
                    });
                }

                /**
                 * Request data sales
                 */
                getSales() {
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
                removeSale(id) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "delete",
                            url: app.url(`/transactions/sales/${id}`),
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
            "use strict"

            /**
             * Request data sales setelah document seleci dimuat.
             */
            $(document).ready(async function () {
                const params = new URLSearchParams(window.location.search);
                const fromDate = params.get('from_date');                
                const toDate = params.get('to_date');
                
                if (fromDate !== null && toDate !== null) {
                    app.showPreloader();
                    app.clearFormErrors(service.formFilter);

                    try {
                        const response = await service.getSales();

                        service.saleTable.clear().draw();
                        service.saleTable.rows.add(response.data).draw();
                    } catch (error) {
                        app.setFormErrors(service.formFilter, error);
                    } finally {
                        app.hidePreloader();
                    }
                }
            });

            /**
             * Submit form filter
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
                    const response = await service.getSales();

                    service.saleTable.clear().draw();
                    service.saleTable.rows.add(response.data).draw();
                } catch (error) {
                    app.setFormErrors(service.formFilter, error);
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Fungsi untuk remove sales
             */
            const removeSale = (event, id) => {
                event.preventDefault();

                app.delete('Transaksi Penjualan', async (deleted) => {
                    if (!deleted) return;

                    app.showPreloader();
    
                    try {
                        const removed = await service.removeSale(id);
                        const sales = await service.getSales();
    
                        app.alert('success', removed.message);
                        service.saleTable.clear().draw();
                        service.saleTable.rows.add(sales.data).draw();
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