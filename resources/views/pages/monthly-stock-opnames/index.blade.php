<x-layouts.auth title="Stok awal bulana" :access="$access">
    <x-page-header title="Stok awal bulan" icon="inventory">
        <x-slot:action>
            <div class="row justify-content-lg-end">
                @if ($access?->create)
                    <div class="col-12 col-lg-6 col-xl-4 mb-5 mb-lg-0">
                        <x-button type="button" startIcon="add" id="buttonCreate" fullWidth>
                            Tambah
                        </x-button>
                    </div>
                @endif
            </div>
        </x-slot:action>
    </x-page-header>

    {{-- Modal create monthly stock --}}
    @if ($access?->create)
        <form action="#" method="post" id="formCreate" autocomplete="off">
            @csrf

            <div class="modal" tabindex="-1" id="modalCreate">
                <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-md-down">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Stok Awal</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
    
                        <div class="modal-body">
                            <div class="mb-6">
                                <x-form-floating-input
                                    required
                                    type="text"
                                    name="daily_date"
                                    id="dailyDateCreate"
                                    label="Tanggal stok harian"
                                    data-flatpickr=""
                                />
                            </div>
                            
                            <div class="mb-6">
                                <x-form-floating-input
                                    required
                                    type="number"
                                    name="year"
                                    id="yearCreate"
                                    label="Tahun"
                                    max="{{ date('Y') }}"
                                    value="{{ date('Y') }}"
                                />
                            </div>
                            
                            <div class="mb-6">
                                <x-form-floating-select required name="month" id="monthCreate" label="Bulan">
                                    @foreach (months() as $month)
                                        <option value="{{ $month['key'] }}" @selected($month['key'] == (int) date('m'))>
                                            {{ $month['name'] }}
                                        </option>
                                    @endforeach
                                </x-form-floating-select>
                            </div>

                            <div>
                                <x-form-floating-input
                                    type="textaread"
                                    name="notes"
                                    id="notesCreate"
                                    label="Catatan"
                                />
                            </div>
                        </div>

                        <div class="modal-footer d-block">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-6">
                                    <x-button type="reset" color="light" startIcon="close" data-bs-dismiss="modal" fullWidth>
                                        Batal
                                    </x-button>
                                </div>

                                <div class="col-6">
                                    <x-button type="submit" color="secondary" startIcon="Save" fullWidth>
                                        Simpan
                                    </x-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif

    {{-- Form filter --}}
    <div class="row mt-6">
        <div class="col-12">
            <form action="{{ route('stockOpnames.monthly') }}" method="get" autocomplete="off" id="formFilter">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-5 mb-lg-0">
                                <x-form-floating-input
                                    type="number"
                                    name="year"
                                    id="yearFilter"
                                    label="Tahun"
                                    value="{{ request('year', date('Y')) }}"
                                    required
                                />
                            </div>

                            <div class="col-12 col-lg-6 mb-5 mb-lg-0">
                                <x-form-floating-select name="month" id="monthFilter" label="Bulan" required>
                                    @foreach (months() as $month)
                                        <option value="{{ $month['key'] }}" @selected($month['key'] == request('month', date('m')))>
                                            {{ $month['name'] }}
                                        </option>
                                    @endforeach
                                </x-form-floating-select>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row justify-content-end">
                            <div class="col-12 col-lg-3 col-xl-2">
                                <x-button type="submit" startIcon="filter_list" fullWidth>Filter</x-button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table stock opname awal bulan --}}
    <div class="row mt-6">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="tableDetail" class="table table-hover table-sm align-middle w-100 m-0"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- Javascript --}}
    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.buttonFilter = $('#buttonFilter');
                    this.formFilter = $('#formFilter');
                    this.tableDetail = this.detailDataTable();
                    this.buttonCreate = $('#buttonCreate');
                    this.modalCreate = new bootstrap.Modal('#modalCreate');
                    this.formCreate = $('#formCreate');
                }

                /**
                 * Monthly stock data table.
                 */
                detailDataTable() {
                    return app.buttonDataTable('#tableDetail', {
                        columns: [
                            {
                                title: 'Kategori',
                                name: 'product.category.name',
                                data: 'product.category.name',
                                className: 'text-nowrap px-3',
                                type: 'string',
                                footer: 'Total',
                            },
                            {
                                title: 'Kode Produk',
                                name: 'product.code',
                                data: 'product.code',
                                className: 'text-nowrap px-3',
                                type: 'string',
                                footer: '',
                            },
                            {
                                title: 'Nama Produk',
                                name: 'product.name',
                                data: 'product.name',
                                className: 'px-3',
                                type: 'string',
                                width: '300px',
                                footer: '',
                            },
                            {
                                title: 'Satuan',
                                name: 'product.unit.code',
                                data: 'product.unit.code',
                                className: 'text-nowrap px-3',
                                type: 'string',
                                footer: '',
                            },
                            {
                                title: 'Quantity',
                                name: 'quantity',
                                data: 'quantity',
                                className: 'text-nowrap px-3',
                                type: 'html-num',
                                footer: app.numberFormat(0, 2),
                                render: (data) => app.numberFormat(data, 2),
                            },
                        ],
                        footerCallback: function(row, data, start, end) {
                            const api = this.api();
                            const totalQuantity = api.column(4).data().reduce((a, b) => a + b, 0);

                            $(api.column(4).footer()).text(app.numberFormat(totalQuantity, 2));
                        }
                    })
                }

                /**
                 * Mengambil data stok bulanan.
                 */
                findMonthlyStock() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "get",
                            url: this.formFilter.attr('action'),
                            data: this.formFilter.serialize(),
                            dataType: "json",
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }
            }

            window.service = new Service();
        </script>

        <script>
            $(document).ready(async function () {
                const params = new URLSearchParams(window.location.search);
                const year = params.get('year');
                const month = params.get('month');

                if (year === null || month === null) {
                    return;
                }

                app.showPreloader();
                app.clearFormErrors(service.formFilter);

                try {
                    const response = await service.findMonthlyStock();
                    
                    service.tableDetail.clear().draw();
                    service.tableDetail.rows.add(response.data.details).draw();
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
                const year = service.formFilter.find('input[name="year"]').val();
                const month = service.formFilter.find('select[name="month"]').val();
                const params = { year, month };

                app.updateQueryParams(params);
                app.showPreloader();
                app.clearFormErrors(service.formFilter);

                try {
                    const response = await service.findMonthlyStock();
                    
                    service.tableDetail.clear().draw();
                    service.tableDetail.rows.add(response.data.details).draw();
                } catch (error) {
                    app.setFormErrors(service.formFilter, error);
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Buka modal create
             */
            service.buttonCreate.click(function (e) { 
                e.preventDefault();
                service.modalCreate.show();
            });
        </script>
    </x-slot:script>
</x-layouts.auth>