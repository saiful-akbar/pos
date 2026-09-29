<x-layouts.auth title="Laporan Stok Bulanan">
    <x-page-header title="Laporan stok bulanan" icon="monitoring"></x-page-header>

    <div class="row mt-6">
        <div class="col-12">
            <form action="{{ route('reports.stocks') }}" method="get" autocomplete="off" id="formFilter">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h2 class="fs-5 mb-0">Filter</h2>
                    </div>
    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-6 mb-lg-0">
                                <x-form-floating-input
                                    type="number"
                                    name="year"
                                    id="year"
                                    label="Tahun"
                                    value="{{ old('year', request('year', date('Y'))) }}"
                                    error="{{ $errors->has('year') }}"
                                    required
                                />
                            </div>

                            <div class="col-12 col-lg-6">
                                <x-form-floating-select name="month" id="month" label="Bulan" error="{{ $errors->has('month') }}" required>
                                    @foreach (months() as $month)
                                        <option value="{{ $month['key'] }}" @selected(old('month', request('month', date('m'))) == $month['key'])>
                                            {{ $month['name'] }}
                                        </option>
                                    @endforeach
                                </x-form-floating-select>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row justify-content-end align-items-center">
                            <div class="col-12 col-lg-4 col-xl-2">
                                <x-button type="submit" color="secondary" startIcon="filter_list" fullWidth>
                                    Filter
                                </x-button>
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
                    <table id="tableMonitoring" class="table table-hover table-sm align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.formFilter = $('#formFilter');
                    this.tableMonitoring = this.monitoringDataTable();
                }

                /**
                 * Monitoring datatable
                 */
                monitoringDataTable() {
                    return app.buttonDataTable('#tableMonitoring', {
                        fixedColumns: {
                            start: 2,
                        },
                        order: [[1, 'asc']],
                        columns: [
                            {
                                title: 'Kategori Produk',
                                name: 'category_name',
                                data: 'category_name',
                                type: 'string',
                                className: 'text-nowrap',
                                footer: 'Total',
                            },
                            {
                                title: 'Kode Produk',
                                name: 'product_code',
                                data: 'product_code',
                                type: 'string',
                                className: 'text-nowrap',
                                footer: '',
                            },
                            {
                                title: 'Nama Produk',
                                name: 'product_name',
                                data: 'product_name',
                                type: 'string',
                                className: 'text-nowrap',
                                footer: '',
                            },
                            {
                                title: 'Satuan',
                                name: 'unit_code',
                                data: 'unit_code',
                                type: 'string',
                                className: 'text-nowrap',
                                footer: '',
                            },
                            {
                                title: 'Stok Awal',
                                name: 'beginning_stock',
                                data: 'beginning_stock',
                                type: 'num',
                                className: 'text-nowrap',
                                footer: app.numberFormat(0),
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Pembelian',
                                name: 'purchase_quantity',
                                data: 'purchase_quantity',
                                type: 'num',
                                className: 'text-nowrap',
                                footer: app.numberFormat(0),
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Pengembalian',
                                name: 'sale_return_quantity',
                                data: 'sale_return_quantity',
                                type: 'num',
                                className: 'text-nowrap',
                                footer: app.numberFormat(0),
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Penjualan',
                                name: 'sale_quantity',
                                data: 'sale_quantity',
                                type: 'num',
                                className: 'text-nowrap',
                                footer: app.numberFormat(0),
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Book Balance',
                                name: 'book_balance_quantity',
                                data: 'book_balance_quantity',
                                type: 'num',
                                className: 'text-nowrap',
                                footer: app.numberFormat(0),
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Stock Akhir',
                                name: 'ending_stock',
                                data: 'ending_stock',
                                type: 'num',
                                className: 'text-nowrap',
                                footer: app.numberFormat(0),
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Selisih',
                                name: 'difference_quantity',
                                data: 'difference_quantity',
                                type: 'num',
                                className: 'text-nowrap',
                                footer: app.numberFormat(0),
                                render: (data) => {
                                    let textColor = 'text-body-primary';

                                    if (data < 0) {
                                        textColor = 'text-danger';
                                    }

                                    if (data > 0) {
                                        textColor = 'text-success';
                                    }

                                    return `<span class="${textColor} fw-bold">${app.numberFormat(data)}</span>`
                                },
                            },
                        ],
                        footerCallback: function() {
                            const api = this.api();
                            const totalBeginningStock = api.column(4).data().reduce((a, b) => a + b, 0);
                            const totalPurchases = api.column(5).data().reduce((a, b) => a + b, 0);
                            const totalSaleReturns = api.column(6).data().reduce((a, b) => a + b, 0);
                            const totalSales = api.column(7).data().reduce((a, b) => a + b, 0);
                            const totalBookBalance = api.column(8).data().reduce((a, b) => a + b, 0);
                            const totalEndingStock = api.column(9).data().reduce((a, b) => a + b, 0);
                            const totalDifference = api.column(10).data().reduce((a, b) => a + b, 0);

                            $(api.column(4).footer()).text(app.numberFormat(totalBeginningStock));
                            $(api.column(5).footer()).text(app.numberFormat(totalPurchases));
                            $(api.column(6).footer()).text(app.numberFormat(totalSaleReturns));
                            $(api.column(7).footer()).text(app.numberFormat(totalSales));
                            $(api.column(8).footer()).text(app.numberFormat(totalBookBalance));
                            $(api.column(9).footer()).text(app.numberFormat(totalEndingStock));
                            $(api.column(10).footer()).text(app.numberFormat(totalDifference));
                        }
                    })
                }

                /**
                 * Request data monitoring stock.
                 */
                getMonitoringMonthlyStock() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "GET",
                            url: this.formFilter.attr('action'),
                            data: this.formFilter.serialize(),
                            dataType: "json",
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    })
                }
            }

            window.service = new Service();
        </script>

        <script>
            "use strict"

            /**
             * Request data monitoring saat dokumen selesai dimuat.
             */
            $(document).ready(async function () {
                const params = new URLSearchParams(window.location.search);
                const year = params.get('year');
                const month = params.get('month');

                if (year === null || month === null) {
                    return;
                }

                app.showPreloader();
                app.clearFormErrors(service.formFilter);
                service.tableMonitoring.clear().draw();

                try {
                    const response = await service.getMonitoringMonthlyStock();
                    const { data } = response;

                    service.tableMonitoring.rows.add(data).draw();
                } catch (error) {
                    app.setFormErrors(service.formFilter, error);
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Submit form filter
             */
            service.formFilter.submit(async function (e) { 
                e.preventDefault();
                
                const year = service.formFilter.find('[name="year"]').val();
                const month = service.formFilter.find('[name="month"]').val();

                if (year === '' || month === '') {
                    return;
                }

                app.updateQueryParams({ year, month });
                app.showPreloader();
                app.clearFormErrors(service.formFilter);
                service.tableMonitoring.clear().draw();

                try {
                    const response = await service.getMonitoringMonthlyStock();
                    const { data } = response;

                    service.tableMonitoring.rows.add(data).draw();
                } catch (error) {
                    app.setFormErrors(service.formFilter, error);
                } finally {
                    app.hidePreloader();
                }
            });
        </script>
    </x-slot:script>
</x-layouts.auth>