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
    </x-page-header>

    <div class="row mt-7">
        <div class="col-12">
            <form action="{{ route('transactions.purchases') }}" method="get" autocomplete="off" id="formFilter">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-12 col-lg-6 mb-6 mb-lg-0">
                                <label for="fromDate" class="form-label">
                                    <span>Tanggal</span>
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <input
                                        type="text"
                                        name="from_date"
                                        id="fromDate"
                                        class="form-control"
                                        placeholder="Pilih tanggal awal"
                                        data-flatpickr=""
                                    />

                                    <input
                                        type="text"
                                        name="to_date"
                                        id="toDate"
                                        class="form-control"
                                        placeholder="Pilih tanggal akhir"
                                        data-flatpickr=""
                                    />
                                </div>

                                <div class="invalid-feedback" data-error="from_date"></div>
                            </div>

                            <div class="col-12 col-lg-3 mb-6 mb-lg-0">
                                <x-form-select
                                    required
                                    id="paymentStatus"
                                    name="payment_status"
                                    label="Status Pembayaran"
                                    error="{{ $errors->has('payment_status') }}"
                                    data-choices=""
                                >
                                    <option value="all" selected>Semua</option>
                                    <option value="unpaid" @selected(request('payment_status') == 'unpaid')>Belum Bayar</option>
                                    <option value="partial" @selected(request('payment_status') == 'partial')>Dibayar Sebagian</option>
                                    <option value="paid" @selected(request('payment_status') == 'paid')>Lunas</option>
                                </x-form-select>
                            </div>

                            <div class="col-12 col-lg-3">
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
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="purchaseTable" class="table table-hover align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        <script>
            const updateAccess = $('html').data('update');

            /**
             * DataTable penjualan.
             */
            const purchaseTable = app.dataTable('#purchaseTable', {
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

            /**
             * Submit form filter
             */
            $('#formFilter').submit(function (e) {
                e.preventDefault();
                
                const fromDate = $(this).find('input[name=from_date]').val();
                const toDate = $(this).find('input[name=to_date]').val();
                const paymentStatus = $(this).find('select[name=payment_status]').val();

                

                console.log(fromDate);
                console.log(toDate);
                console.log(paymentStatus);
                

                $.ajax({
                    type: "get",
                    url: "{{ route('transactions.purchases') }}",
                    dataType: "json",
                    data: {
                        from_date: fromDate,
                        to_date: toDate,
                        payment_status: paymentStatus,
                    },
                    success: function (response) {
                        console.log(response);
                    },
                    error: function (response) {
                        for (const name of response.errors.entries) {
                        }
                    },
                });
            });
        </script>
    </x-slot:script>
</x-layouts.auth>