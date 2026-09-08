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
                        <div class="row align-items-top">
                            <div class="col-12 col-lg-4 mb-6 mb-lg-0">
                                <div class="form-floating">
                                    <input type="text" name="from_date" id="from_date" placeholder="Priode awal" class="form-control" data-flatpickr="">
                                    <label for="fromDate">Periode awal <span class="text-danger">*</span></label>
                                </div>

                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-12 col-lg-4 mb-6 mb-lg-0">
                                <div class="form-floating">
                                    <input type="text" name="to_date" id="to_date" placeholder="Priode awal" class="form-control" data-flatpickr="">
                                    <label for="toDate">Periode akhir <span class="text-danger">*</span></label>
                                </div>

                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-12 col-lg-4">
                                <div class="form-floating">
                                    <select name="payment_status" id="payment_status" class="form-select">
                                        <option value="all" selected>Semua</option>
                                        <option value="paid">Lunas</option>
                                        <option value="partial">Dibayar sebagian</option>
                                        <option value="unpaid">Belum dibayar</option>
                                    </select>

                                    <label for="toDate">Status pembayaran <span class="text-danger">*</span></label>
                                </div>

                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row justify-content-end">
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
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="purchaseTable" class="table table-hover align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>

        {{-- CLass form filter --}}
        <script>
            class FormFilter {
                constructor() {
                    this.form = $('#formFilter');
                }

                /**
                 * Membersihkan pesan error pada form filter
                 */
                clearErrors() {
                    this.form.find('.is-invalid').removeClass('is-invalid');
                    this.form.find('.is-invalid').parent().removeClass('is-invalid');
                    this.form.find('.is-invalid').parent().next().text('');
                }
                
                /**
                 * Menampilkan pesan error.
                 */
                showErrors(errors) {
                    Object.entries(errors).forEach(([field, messages]) => {
                        this.form.find(`#${field}`).addClass('is-invalid');
                        this.form.find(`#${field}`).parent().addClass('is-invalid');
                        this.form.find(`#${field}`).parent().next().text(messages[0]);
                    });
                }

                /**
                 * Submit form filter
                 */
                submit(values) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "get",
                            url: "{{ route('transactions.purchases') }}",
                            data: values,
                            dataType: "json",
                            success: function (response) {
                                resolve(response);
                            },
                            error: function (response) {
                                reject(response.responseJSON);
                            },
                        });
                    });
                }
            }

            window.formFilter = new FormFilter();
        </script>

        {{-- Global script --}}
        <script>
            "use strict"

            /**
             * Purchase datatable.
             */
            const purchaseTable = app.buttonDataTable('#purchaseTable', {
                columns: [
                    {
                        title: 'Pemasok',
                        name: 'suppliers.name',
                        data: 'supplier.name',
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
                        name: 'amount_paid',
                        data: 'amount_paid',
                        className: 'text-nowrap',
                        render: (data) => app.numberFormat(data),
                    },
                    {
                        title: 'Kurang Bayar',
                        className: 'text-nowrap',
                        render: (data, type, row) => app.numberFormat(row.total_price - row.amount_paid),
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
                        window.open(`${app.baseUrl}/transactions/purchases/${data.id}`);
                    });
                }
            });

            /**
             * Submit form filter
             */
            formFilter.form.submit(async (e) => {
                e.preventDefault();

                formFilter.clearErrors();
                app.showPreloader();

                try {
                    const values = formFilter.form.serialize();
                    const response = await formFilter.submit(values);

                    purchaseTable.clear().draw();
                    purchaseTable.rows.add(response.data).draw();
                } catch (error) {
                    formFilter.showErrors(error.errors);
                } finally {
                    app.hidePreloader();
                }
            })
        </script>
    
    </x-slot:script>
</x-layouts.auth>