<x-layouts.auth title="Laporan Summary Penjualan">
    <x-page-header title="Laporan Penjualan" icon="monitoring">

        <hr class="my-6">

        <div class="row">
            <div class="col-12">
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item me-2">
                        <a href="{{ route('reports.sales') }}" class="nav-link active">Summary</a>
                    </li>
        
                    <li class="nav-item">
                        <a href="{{ route('reports.sales.details') }}" class="nav-link">Detail</a>
                    </li>
                </ul>
            </div>
        </div>
    </x-page-header>

    {{-- form filter --}}
    <div class="row mt-6">
        <div class="col-12">
            <form action="{{ route('reports.sales') }}" method="get" autocomplete="off" id="formFilter">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h2 class="fs-5 mb-0">Filter</h2>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-4 mb-6 mb-lg-0">
                                <x-form-floating-input
                                    type="text"
                                    name="from_date"
                                    id="fromDate"
                                    label="Dari tanggal"
                                    value="{{ old('from_date', request('from_date')) }}"
                                    error="{{ $errors->has('from_date') }}"
                                    data-flatpickr=""
                                />
                            </div>

                            <div class="col-12 col-lg-4 mb-6 mb-lg-0">
                                <x-form-floating-input
                                    type="text"
                                    name="to_date"
                                    id="toDate"
                                    label="Sampai tanggal"
                                    value="{{ old('to_date', request('to_date')) }}"
                                    error="{{ $errors->has('to_date') }}"
                                    data-flatpickr=""
                                />
                            </div>

                            <div class="col-12 col-lg-4">
                                <x-form-floating-input
                                    type="text"
                                    name="invoice_number"
                                    id="invoiceNumber"
                                    label="Nomor Faktur"
                                    value="{{ old('invoice_number', request('invoice_number')) }}"
                                    error="{{ $errors->has('invoice_number') }}"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row justify-content-end">
                            <div class="col-12 col-lg-4 col-xl-2">
                                <x-button type="submit" startIcon="filter_list" fullWidth>Submit</x-button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Datatable --}}
    <div class="row mt-6">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="tableSale" class="table table-hover table-sm align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- Javascript --}}
    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.formFilter = $('#formFilter');
                    this.tableSale = this.saleDataTable();
                };

                /**
                 * Sale datatable
                 */
                saleDataTable() {
                    return app.buttonDataTable('#tableSale', {
                        fixedColumns: {
                            start: 0,
                            end: 1,
                        },
                        columns: [
                            {
                                title: 'Tanggal',
                                name: 'transaction_date',
                                data: 'transaction_date',
                                class: 'text-nowrap',
                                footer: 'Total',
                                type: 'date',
                            },
                            {
                                title: 'Nomor faktur',
                                name: 'invoice_number',
                                data: 'invoice_number',
                                class: 'text-nowrap',
                                footer: '',
                                type: 'string',
                            },
                            {
                                title: 'Kasir',
                                name: 'user.full_name',
                                data: 'user.full_name',
                                class: 'text-nowrap',
                                footer: '',
                                type: 'string',
                            },
                            {
                                title: 'Pelanggan',
                                name: 'customer.name',
                                data: 'customer.name',
                                class: 'text-nowrap',
                                footer: '',
                                type: 'string',
                            },
                            {
                                title: 'Total Quantity',
                                name: 'total_quantity',
                                data: 'total_quantity',
                                class: 'text-nowrap',
                                footer: 0,
                                type: 'num',
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Total Harga',
                                name: 'total_amount',
                                data: 'total_amount',
                                class: 'text-nowrap',
                                footer: 0,
                                type: 'num',
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Dibayar',
                                name: 'paid_amount',
                                data: 'paid_amount',
                                class: 'text-nowrap',
                                footer: 0,
                                type: 'num',
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Kembalian',
                                name: 'change_amount',
                                data: 'change_amount',
                                class: 'text-nowrap',
                                footer: 0,
                                type: 'num',
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Metode Pembayaran',
                                name: 'payment_method',
                                data: 'payment_method',
                                class: 'text-nowrap',
                                footer: '',
                                type: 'string',
                                render: (data) => {
                                    if (data === 'cash') return 'TUNAI';
                                    return data.toUpperCase();
                                },
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
                                    return `
                                        <a href="${app.baseUrl}/transactions/sales/${data}/receipt" class="btn btn-light" title="Cetak struk" target="_blank" role="button">
                                            <span class="material-symbols-outlined fs-6">receipt</span>
                                        </a>
                                    `;
                                },
                            },
                        ],
                        footerCallback: function() {
                            const api = this.api();

                            const totalQuantity = api.column(4).data().reduce((a, b) => a + b, 0);
                            const totalPrice = api.column(5).data().reduce((a, b) => a + b, 0);
                            const totalPaid = api.column(6).data().reduce((a, b) => a + b, 0);
                            const totalChange = api.column(7).data().reduce((a, b) => a + b, 0);

                            $(api.column(4).footer()).text(app.numberFormat(totalQuantity));
                            $(api.column(5).footer()).text(app.numberFormat(totalPrice));
                            $(api.column(6).footer()).text(app.numberFormat(totalPaid));
                            $(api.column(7).footer()).text(app.numberFormat(totalChange));
                        }
                    });
                }

                /**
                 * Mengambil data sales
                 */
                getSales() {
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
             * Request sales saat document selesai dimuat
             */
            $(document).ready(async function () {
                const params = new URLSearchParams(window.location.search);
                const fromDate = params.get('from_date');
                const toDate = params.get('to_date');
                const invoiceNumber = params.get('invoice_number');

                if ((fromDate === null && toDate === null) && invoiceNumber === null) {
                    return;
                }

                app.showPreloader();
                app.clearFormErrors(service.formFilter);
                service.tableSale.clear().draw();

                try {
                    const response = await service.getSales();
                    const { data } = response;

                    service.tableSale.rows.add(data).draw();
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

                const fromDate = service.formFilter.find('[name="from_date"]').val();
                const toDate = service.formFilter.find('[name="to_date"]').val();
                const invoiceNumber = service.formFilter.find('[name="invoice_number"]').val();

                const params = {
                    from_date: fromDate,
                    to_date: toDate,
                    invoice_number: invoiceNumber,
                };

                app.updateQueryParams(params);
                app.showPreloader();
                app.clearFormErrors(service.formFilter);
                app.clearAlert();
                service.tableSale.clear().draw();

                try {
                    const response = await service.getSales();
                    const { data } = response;

                    service.tableSale.rows.add(data).draw();
                } catch (error) {
                    app.setFormErrors(service.formFilter, error);
                } finally {
                    app.hidePreloader();
                }
            });
        </script>
    </x-slot:script> 
</x-layouts.auth>