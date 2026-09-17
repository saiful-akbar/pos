<x-layouts.sales :active="$active" :access="$access">
    <div class="row mt-7">
        <div class="col-12">
            <form action="{{ route('transactions.sales.return') }}" method="get" id="formFilter">
                @csrf @method('GET')

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-6 mb-lg-0">
                                <div class="form-floating">
                                    <input type="text" name="from_date" id="fromDate" class="form-control"
                                        placeholder="Dari tanggal" data-flatpickr="" required />

                                    <label for="fromDate">
                                        Periode awal <span class="text-danger">*</span>
                                    </label>
                                </div>

                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <div class="form-floating">
                                    <input type="text" name="to_date" id="toDate" class="form-control"
                                        placeholder="Sampai tanggal" data-flatpickr="" required />

                                    <label for="toDate">
                                        Periode akhir <span class="text-danger">*</span>
                                    </label>
                                </div>

                                <div class="invalid-feedback"></div>
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
                    <table id="saleTable" class="table table-hover align-middle w-100"></table>
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
                        order: [
                            [1, 'asc']
                        ],
                        columns: [
                            {
                                title: 'No. Faktur Penjualan',
                                name: 'sale.invoice_number',
                                data: 'sale.invoice_number',
                                className: 'text-nowrap',
                                type: 'string',
                            },
                            {
                                title: 'Tanggal',
                                name: 'transaction_date',
                                data: 'transaction_date',
                                className: 'text-nowrap',
                                type: 'date',
                            },
                            {
                                title: 'Qauntity',
                                name: 'total_quantity',
                                data: 'total_quantity',
                                className: 'text-nowrap',
                                type: 'num-fmt',
                                render: (data, type, row) => app.numberFormat(data),
                            },
                            {
                                title: 'Total',
                                name: 'total_amount',
                                data: 'total_amount',
                                className: 'text-nowrap',
                                type: 'num-fmt',
                                render: (data) => `Rp ${app.numberFormat(data)}`,
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
            }

            window.service = new Service();
        </script>

        {{-- Global script --}}
        <script>
            service.formFilter.submit(async function (e) { 
                e.preventDefault();
                app.showPreloader();
                
                service.formFilter.find('input[name="from_date"]').removeClass('is-invalid');
                service.formFilter.find('input[name="from_date"]').parent().removeClass('is-invalid');
                service.formFilter.find('input[name="from_date"]').parent().next().text('');
                service.formFilter.find('input[name="to_date"]').removeClass('is-invalid');
                service.formFilter.find('input[name="to_date"]').parent().removeClass('is-invalid');
                service.formFilter.find('input[name="to_date"]').parent().next().text('');

                try {
                    const table = service.saleTable;
                    const response = await service.getSales();

                    table.clear().draw();
                    table.rows.add(response.data).draw();
                } catch (error) {
                    Object.entries(error.errors).forEach(([field, message]) => {
                        service.formFilter.find(`input[name="${field}"]`).addClass('is-invalid');
                        service.formFilter.find(`input[name="${field}"]`).parent().addClass('is-invalid');
                        service.formFilter.find(`input[name="${field}"]`).parent().next().text(message[0]);
                    });
                } finally {
                    app.hidePreloader();
                }
            });
        </script>
    </x-slot:script>
</x-layouts.sales>