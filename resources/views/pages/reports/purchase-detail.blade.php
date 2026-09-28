<x-layouts.auth title="Laporan Detail Pembelian">
    <x-page-header title="Laporan Detail Pembelian" icon="monitoring">

        <hr class="my-6">

        <div class="row">
            <div class="col-12">
                <ul class="nav nav-pills nav-fill">
                    <li class="nav-item me-2">
                        <a href="{{ route('reports.purchases') }}" class="nav-link">
                            Summary
                        </a>
                    </li>
        
                    <li class="nav-item">
                        <a href="{{ route('reports.purchases.details') }}" class="nav-link active">
                            Detail
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </x-page-header>

    {{-- form filter --}}
    <div class="row mt-6">
        <div class="col-12">
            <form action="{{ route('reports.purchases.details') }}" method="get" autocomplete="off" id="formFilter">
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
                                    data-flatpickr=""
                                    required
                                />
                            </div>

                            <div class="col-12 col-lg-4 mb-6 mb-lg-0">
                                <x-form-floating-input
                                    type="text"
                                    name="to_date"
                                    id="toDate"
                                    label="Sampai tanggal"
                                    value="{{ old('to_date', request('to_date')) }}"
                                    data-flatpickr=""
                                    required
                                />
                            </div>

                            <div class="col-12 col-lg-4">
                                <x-form-floating-select name="payment_status" id="paymentStatus" label="Status pembayaran" required>
                                    <option value="all" @selected(old('payment_status', request('payment_status', 'all')) == 'all')>Semua</option>
                                    <option value="paid" @selected(old('payment_status', request('payment_status', 'all')) == 'paid')>Lunas</option>
                                    <option value="partial" @selected(old('payment_status', request('payment_status', 'all')) == 'partial')>Dibayar sebagian</option>
                                    <option value="unpaid" @selected(old('payment_status', request('payment_status', 'all')) == 'unpaid')>Belum bayar</option>
                                </x-form-floating-select>
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
                    <table id="purchaseTable" class="table table-hover table-sm align-middle w-100"></table>
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
                    this.purchaseTable = this.purchaseDataTable();
                }

                /**
                 * Purchase datatable
                 */
                purchaseDataTable() {
                    return app.buttonDataTable('#purchaseTable', {
                        columns: [
                            {
                                title: 'Tanggal',
                                name: 'created_at',
                                data: 'created_at',
                                className: 'text-nowrap',
                                footer: 'Total',
                            },
                            {
                                title: 'Kode Kategori',
                                name: 'product.category.code',
                                data: 'product.category.code',
                                className: 'text-nowrap',
                                footer: '',
                                render: (data, type, row) => `${data} (${row.product.category.name})`,
                            },
                            {
                                title: 'Kode Produk',
                                name: 'product.code',
                                data: 'product.code',
                                className: 'text-nowrap',
                                footer: '',
                            },
                            {
                                title: 'Nama Produk',
                                name: 'product.name',
                                data: 'product.name',
                                width: '250px',
                                footer: '',
                            },
                            {
                                title: 'Satuan',
                                name: 'product.unit.code',
                                data: 'product.unit.code',
                                className: 'text-nowrap',
                                footer: '',
                                render: (data, type, row) => `${data} (${row.product.unit.name})`,
                            },
                            {
                                title: 'Harga',
                                name: 'purchase_price',
                                data: 'purchase_price',
                                className: 'text-nowrap',
                                footer: 0,
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Quantity',
                                name: 'quantity',
                                data: 'quantity',
                                className: 'text-nowrap',
                                footer: 0,
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Subtotal',
                                name: 'subtotal_price',
                                data: 'subtotal_price',
                                className: 'text-nowrap',
                                footer: 0,
                                render: (data) => app.numberFormat(data),
                            },
                        ],
                        footerCallback: function() {
                            const api = this.api();

                            const totalPrice = api.column(5).data().reduce((a, b) => a + b, 0);
                            const totalQuantity = api.column(6).data().reduce((a, b) => a + b, 0);
                            const subtotal = api.column(7).data().reduce((a, b) => a + b, 0);

                            $(api.column(5).footer()).text(app.numberFormat(totalPrice));
                            $(api.column(6).footer()).text(app.numberFormat(totalQuantity));
                            $(api.column(7).footer()).text(app.numberFormat(subtotal));
                        }
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
             * Request data pembelian saat dokumen selesai dimuat
             */
            $(document).ready(async function () {
                const params = new URLSearchParams(window.location.search);
                const fromDate = params.get('from_date');
                const toDate = params.get('to_date');
                const paymentStatus = params.get('payment_status');

                if (fromDate === null || toDate === null || paymentStatus === null) {
                    return;
                }

                app.showPreloader();
                service.purchaseTable.clear().draw();

                try {
                    const response = await service.getPurchases();

                    service.purchaseTable.rows.add(response.data).draw();
                } catch (error) {
                    console.log(error);
                    
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
                const paymentStatus = service.formFilter.find('[name="payment_status"]').val();

                const params = {
                    from_date: fromDate,
                    to_date: toDate,
                    payment_status: paymentStatus,
                };

                app.updateQueryParams(params);
                app.clearFormErrors(service.formFilter);
                app.showPreloader();
                service.purchaseTable.clear().draw();

                try {
                    const response = await service.getPurchases();

                    service.purchaseTable.rows.add(response.data).draw();
                } catch (error) {
                    app.setFormErrors(service.formFilter, error);
                } finally {
                    app.hidePreloader();
                }
            });
        </script>
    </x-slot:script> 
</x-layouts.auth>