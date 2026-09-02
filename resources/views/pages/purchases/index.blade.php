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

    {{-- Modal purchase detail --}}
    <div class="modal" tabindex="-1" id="modalPurchaseDetail">
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-md-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pembelian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <table id="purchaseDetailTable" class="table table-hover align-middle w-100"></table>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <x-icon class="me-1">close</x-icon>
                        <span>Tutup</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        <script>
            let purchaseDetailtable = null;
            const modalPurchaseDetail = new bootstrap.Modal('#modalPurchaseDetail');
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
                    {
                        title: 'Opsi',
                        data: 'id',
                        className: 'text-center',
                        searchable: false,
                        orderable: false,
                        render: (data) => {
                            let html = `<div class="btn-group w-100">`;

                            html += `
                                <button type="button" class="btn btn-light" title="Lihat detail" data-action="detail">
                                    <span class="material-symbols-outlined fs-6">visibility</span>
                                </button>
                            `;

                            if (updateAccess) {
                                html += `
                                    <a href="${app.baseUrl}/transactions/purchases/${data}/edit" class="btn btn-light" role="button" title="Edit">
                                        <span class="material-symbols-outlined fs-6">edit</span>
                                    </a>
                                `;
                            }

                            html += `</div>`;

                            return html;
                        }
                    }
                ],
                createdRow: (row, data, index) => {
                    $(row).find("button[data-action='detail']").click(function (e) { 
                        e.preventDefault();
                        modalPurchaseDetail.show();

                        if (purchaseDetailtable === null) {
                            purchaseDetailtable = app.dataTable('#purchaseDetailTable', {
                                serverSide: true,
                                ajax: `${app.baseUrl}/transactions/purchases/details/${data.id}`,
                                scrollY: "400px",
                                scrollX: false,
                                layout: {
                                    topStart: 'pageLength',
                                    topEnd: "search",
                                    bottomStart: null,
                                    bottomEnd: "paging",
                                },
                                order: {
                                    name: 'code',
                                    dir: 'asc',
                                },
                                columns: [
                                    {
                                        title: 'Produk',
                                        name: 'product.code',
                                        data: 'product.code',
                                        className: 'text-nowrap',
                                        render: (data, type, row) => {
                                            if (row.product.image === null) {
                                                return `
                                                    <div class="d-flex align-items-center justify-content-start">
                                                        <div class="me-3">
                                                            <img src="${app.baseUrl}/assets/images/empty.jpg" alt="Product" class="rounded" width="50" height="50" />
                                                        </div>
                                                        <div>
                                                            <h6 class="fs-6 m-0">${row.product.code}</h6>
                                                            <p class="text-body-secondary m-0 text-truncate" style="max-width: 200px;">lakslas hsjagsha sfagfsgasa kasjka sahsfagfs sfagsfa sfagfsgasf a${row.product.name}</p>
                                                        </div>
                                                    </div>
                                                `;
                                            }

                                            return `
                                                <div class="d-flex align-items-center justify-content-start">
                                                    <div class="me-3">
                                                        <img src="${app.baseUrl}/storage/${row.product.image}" alt="Product" class="rounded" width="50" height="50" />
                                                    </div>
                                                    <div>
                                                        <h6 class="fs-6 m-0">${row.product.code}</h6>
                                                        <p class="text-body-secondary m-0 text-truncate" style="max-width: 200px;">${row.product.name}</p>
                                                    </div>
                                                </div>
                                            `;
                                        }
                                    },
                                    {
                                        title: 'Qty',
                                        name: 'quantity',
                                        data: 'quantity',
                                        className: 'text-nowrap',
                                        render: (data) => app.numberFormat(data),
                                    },
                                    {
                                        title: 'Harga Beli',
                                        name: 'purchase_price',
                                        data: 'purchase_price',
                                        className: 'text-nowrap',
                                        render: (data) => app.numberFormat(data),
                                    },
                                    {
                                        title: 'Subtotal',
                                        name: 'subtotal_price',
                                        data: 'subtotal_price',
                                        className: 'text-nowrap',
                                        render: (data) => app.numberFormat(data),
                                    },
                                ]
                            })
                        } else {
                            purchaseDetailtable.ajax.url(`${app.baseUrl}/transactions/purchases/details/${data.id}`).load();
                        }
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