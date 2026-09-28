<x-layouts.auth title="Laporan Detail Penjualan">
    <x-page-header title="Laporan Penjualan" icon="monitoring">

        <hr class="my-6">

        <div class="row">
            <div class="col-12">
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item me-2">
                        <a href="{{ route('reports.sales') }}" class="nav-link">Summary</a>
                    </li>
        
                    <li class="nav-item">
                        <a href="{{ route('reports.sales.details') }}" class="nav-link active">Detail</a>
                    </li>
                </ul>
            </div>
        </div>
    </x-page-header>

    {{-- form filter --}}
    <div class="row mt-6">
        <div class="col-12">
            <form action="{{ route('reports.sales.details') }}" method="get" autocomplete="off" id="formFilter">
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
                        columns: [
                            {
                                title: 'Tanggal',
                                name: 'sale.transaction_date',
                                data: 'sale.transaction_date',
                                class: 'text-nowrap',
                                type: 'date',
                                footer: 'Total',
                            },
                            {
                                title: 'Nomor faktur',
                                name: 'sale.invoice_number',
                                data: 'sale.invoice_number',
                                class: 'text-nowrap',
                                type: 'string',
                                footer: '',
                            },
                            {
                                title: 'Kasir',
                                name: 'sale.user.full_name',
                                data: 'sale.user.full_name',
                                class: 'text-nowrap',
                                type: 'string',
                                footer: '',
                            },
                            {
                                title: 'Pelanggan',
                                name: 'sale.customer.name',
                                data: 'sale.customer.name',
                                class: 'text-nowrap',
                                type: 'string',
                                footer: '',
                            },
                            {
                                title: 'Kode Kategori',
                                name: 'product.category.code',
                                data: 'product.category.code',
                                class: 'text-nowrap',
                                type: 'string',
                                footer: '',
                            },
                            {
                                title: 'Nama (Deskripsi) Kategori',
                                name: 'product.category.name',
                                data: 'product.category.name',
                                type: 'string',
                                width: '250px',
                                footer: '',
                            },
                            {
                                title: 'Kode Produk',
                                name: 'product.code',
                                data: 'product.code',
                                class: 'text-nowrap',
                                type: 'string',
                                footer: '',
                            },
                            {
                                title: 'Nama (Deskripsi) Produk',
                                name: 'product.name',
                                data: 'product.name',
                                type: 'string',
                                width: '250px',
                                footer: '',
                            },
                            {
                                title: 'Satuan',
                                name: 'product.unit.code',
                                data: 'product.unit.code',
                                class: 'text-nowrap',
                                type: 'string',
                                footer: '',
                            },
                            {
                                title: 'Quantity',
                                name: 'quantity',
                                data: 'quantity',
                                class: 'text-nowrap',
                                type: 'num',
                                footer: 0,
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Harga Beli',
                                name: 'purchase_price',
                                data: 'purchase_price',
                                class: 'text-nowrap',
                                type: 'num',
                                footer: 0,
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Harga Jual',
                                name: 'selling_price',
                                data: 'selling_price',
                                class: 'text-nowrap',
                                type: 'num',
                                footer: 0,
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Diskon (%)',
                                name: 'discount',
                                data: 'discount',
                                class: 'text-nowrap',
                                type: 'num',
                                footer: 0,
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Subtotal',
                                name: 'subtotal',
                                data: 'subtotal',
                                class: 'text-nowrap',
                                type: 'num',
                                footer: 0,
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Metode Pembayaran',
                                name: 'sale.payment_method',
                                data: 'sale.payment_method',
                                class: 'text-nowrap',
                                type: 'string',
                                footer: '',
                                render: (data) => {
                                    if (data === 'cash') return 'TUNAI';
                                    return data.toUpperCase();
                                },
                            },
                        ],
                        footerCallback: function() {
                            const api = this.api();

                            const totalQuantity = api.column(9).data().reduce((a, b) => a + b, 0);
                            const totalPurchasePrice = api.column(10).data().reduce((a, b) => a + b, 0);
                            const totalSellingPrice = api.column(11).data().reduce((a, b) => a + b, 0);
                            const subtotal = api.column(13).data().reduce((a, b) => a + b, 0);

                            $(api.column(9).footer()).text(app.numberFormat(totalQuantity));
                            $(api.column(10).footer()).text(app.numberFormat(totalPurchasePrice));
                            $(api.column(11).footer()).text(app.numberFormat(totalSellingPrice));
                            $(api.column(13).footer()).text(app.numberFormat(subtotal));
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