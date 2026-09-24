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

    {{-- Form filter --}}
    <div class="row mt-7">
        <div class="col-12">
            <form action="{{ route('stockOpnames.monthly') }}" method="get" autocomplete="off" id="formFilter">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-5 mb-lg-0">
                                <x-form-floating-input
                                    type="number"
                                    name="year"
                                    id="year"
                                    label="Tahun"
                                    value="{{ request('year', date('Y')) }}"
                                    required
                                />
                            </div>

                            <div class="col-12 col-lg-6 mb-5 mb-lg-0">
                                <x-form-floating-select name="month" id="month" label="Bulan" required>
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
    <div class="row mt-7">
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
                }

                /**
                 * Monthly stock data table.
                 */
                detailDataTable() {
                    return app.buttonDataTable('#tableDetail', {
                        columns: [
                            {
                                title: 'Kategori Produk',
                                name: 'products.category.name',
                                data: 'products.category.name',
                                className: 'text-nowrap px-3',
                                type: 'string',
                            },
                            {
                                title: 'Kode Produk',
                                name: 'products.code',
                                data: 'products.code',
                                className: 'text-nowrap px-3',
                                type: 'string',
                            },
                            {
                                title: 'Satuan',
                                name: 'products.unit.code',
                                data: 'products.unit.code',
                                className: 'text-nowrap px-3',
                                type: 'string',
                            },
                            {
                                title: 'Stok System',
                                name: 'system_stock',
                                data: 'system_stock',
                                className: 'text-nowrap px-3',
                                type: 'num',
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Stok Fisik',
                                name: 'physical_stock',
                                data: 'physical_stock',
                                className: 'text-nowrap px-3',
                                type: 'num',
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Selisih',
                                name: 'difference_stock',
                                data: 'difference_stock',
                                className: 'text-nowrap px-3',
                                type: 'num',
                                render: (data) => app.numberFormat(data),
                            },
                        ],
                    })
                }
            }

            window.service = new Service();
        </script>

        <script>
            service.formFilter.submit(function (e) { 
                e.preventDefault();
                const year = service.formFilter.find('[name="year"]').val();
                const month = service.formFilter.find('[name="month"]').val();
                const params = { year, month };

                app.updateQueryParams(params);
            });
        </script>
    </x-slot:script>
</x-layouts.auth>