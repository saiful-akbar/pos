<x-layouts.auth title="Pembelian" :access="$access">
    <x-page-header title="Pembelian" icon="add_shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end align-items-center">
                @if ($access?->create)
                    <div class="col-12 col-lg-6 col-xl-3">
                        <a href="{{ route('transactions.purchases.cart') }}" class="btn btn-secondary w-100" role="button">
                            <x-icon class="me-1">add</x-icon>
                            <span>Tambah Pembelian</span>
                        </a>
                    </div>
                @endif
            </div>
        </x-slot:action>

        <x-slot>
            <hr class="my-7">
            
            <div class="row align-items-center">
                <div class="col-12">
                    <ul class="nav nav-pills nav-fill">
                        <li class="nav-item me-4">
                            <a class="nav-link active" aria-current="page" href="{{ route('transactions.purchases') }}">Pembelian</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Hutang</a>
                        </li>
                    </ul>
                </div>
            </div>
        </x-slot>
    </x-page-header>

    <div class="row mt-7">
        <div class="col-12">
            <form action="{{ route('transactions.purchases') }}" method="get" autocomplete="off" id="formFilter">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-12 col-lg-4 col-xl-5 mb-6 mb-lg-0">
                                <x-form-input
                                    required
                                    type="text"
                                    id="fromDate"
                                    name="from_date"
                                    label="Periode Awal"
                                    placeholder="Periode Awal"
                                    value="{{ request('from_date', date('Y-m-d')) }}"
                                    data-flatpickr=""
                                />
                            </div>

                            <div class="col-12 col-lg-4 col-xl-5 mb-6 mb-lg-0">
                                <x-form-input
                                    required
                                    type="text"
                                    id="toDate"
                                    name="to_date"
                                    label="Periode Akhir"
                                    placeholder="Periode Akhir"
                                    value="{{ request('to_date', date('Y-m-d')) }}"
                                    data-flatpickr=""
                                />
                            </div>

                            <div class="col-12 col-lg-4 col-xl-2">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <x-icon class="me-1">filter_list</x-icon>
                                    <span>Filter</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row mt-7">
        <div class="col-12">
            <div class="card sahdow-sm">
                <div class="card-body">
                    <table id="purchaseTable" class="table table-hover align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        <script>
            const updateAccess = $('html').data('update');

            const purchaseTable = app.dataTable('#purchaseTable', {
                serverSide: true,
                ajax: {
                    url: `${app.baseUrl}/transactions/purchases`,
                    type: 'get',
                    data: function (data) {
                        data.from_date = $('#fromDate').val();
                        data.to_date = $('#toDate').val();
                    },
                },
                fixedColumns: {
                    start: 0,
                    end: 1,
                },
                columns: [
                    {
                        title: 'Pemasok',
                        name: 'suppliers.name',
                        data: 'supplier_name',
                        className: 'text-nowrap',
                        type: 'string',
                    },
                    {
                        title: 'Nomor Invoice',
                        name: 'purchases.invoice_number',
                        data: 'invoice_number',
                        className: 'text-nowrap',
                    },
                    {
                        title: 'Tanggal',
                        name: 'purchases.purchase_date',
                        data: 'purchase_date',
                        className: 'text-nowrap',
                    },
                    {
                        title: 'Total Qty',
                        name: 'purchases.total_quantity',
                        data: 'total_quantity',
                        className: 'text-nowrap',
                        render: (data) => app.numberFormat(data),
                    },
                    {
                        title: 'Total Harga',
                        name: 'purchases.total_price',
                        data: 'total_price',
                        className: 'text-nowrap',
                        render: (data) => app.numberFormat(data),
                    },
                    {
                        title: 'Sudah Dibayar',
                        name: 'payments.total_amount',
                        data: 'payment_total_amount',
                        className: 'text-nowrap',
                        render: (data) => app.numberFormat(data),
                    },
                    {
                        title: 'Kurang Bayar',
                        className: 'text-nowrap',
                        render: (data, type, row) => app.numberFormat(row.total_price - row.payment_total_amount),
                    },
                    {
                        title: 'Status Pembayaran',
                        name: 'purchases.payment_status',
                        data: 'payment_status',
                        className: 'text-nowrap',
                    },
                ],
                createdRow: (row, data, index) => {
                    $(row).addClass('cursor-pointer');
                    
                    $(row).click(function (e) { 
                        e.preventDefault();
                        window.location = `${app.baseUrl}/transactions/purchases/${data.id}`;
                    });
                }
            });

            $('#formFilter').submit(function (e) {
                e.preventDefault();
                purchaseTable.ajax.reload(null, true);
            });
        </script>
    </x-slot:script>
</x-layouts.auth>