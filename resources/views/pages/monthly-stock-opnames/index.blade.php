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
                                    name="daily_stock_opname_date"
                                    id="dailyStockOpnameDateCreate"
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
                    <div class="card-header">
                        <h2 class="fs-5 mb-0">Filter</h2>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-6 mb-lg-0">
                                <x-form-floating-input
                                    type="number"
                                    name="year"
                                    id="yearFilter"
                                    label="Tahun"
                                    value="{{ old('year', request('year', date('Y'))) }}"
                                    error="{{ $errors->has('year') }}"
                                    required
                                />
                            </div>

                            <div class="col-12 col-lg-6">
                                <x-form-floating-select name="month" id="monthFilter" label="Bulan" error="{{ $errors->has('month') }}" required>
                                    @foreach (months() as $month)
                                        <option value="{{ $month['key'] }}" @selected($month['key'] == old('month', request('month', date('m'))))>
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
        <div class="col-12 col-lg-4 mb-6 mb-lg-0">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h2 class="fs-5 mb-0">Summary</h2>
                </div>

                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Tanggal stok dipilih</span>
                            <span id="dailyStockOpnameDate" class="fw-bold">-</span>
                        </li>
                        
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Total produk</span>
                            <span id="totalProduct" class="fw-bold">-</span>
                        </li>
                        
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Total quantity</span>
                            <span id="totalQuantity" class="fw-bold">-</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-auto">
                            <h2 class="fs-5 m-0">Detail stok</h2>
                        </div>
                        
                        @if ($access?->update)
                            <div class="col-6 col-lg-4 col-xl-3">
                                <x-button type="button" startIcon="autorenew" id="buttonUpdate" fullWidth disabled>Perbarui Stok</x-button>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <table id="tableDetail" class="table table-hover table-sm align-middle w-100 m-0"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- Form modal update stock bulanan --}}
    @if ($access?->update)
        <form action="#" method="post" id="formUpdate" autocomplete="off">
            @csrf @method('patch')

            <div class="modal" tabindex="-1" id="modalUpdate">
                <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-md-down">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Perbarui Stok</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div>
                                <x-form-floating-input
                                    type="text"
                                    name="daily_stock_opname_date"
                                    id="dailyStockOpnameDateUpdate"
                                    label="Tanggal stok harian"
                                    data-flatpickr=""
                                    required
                                />
                            </div>

                            <div class="mt-6">
                                <x-form-floating-input
                                    type="textarea"
                                    name="notes"
                                    id="notesUpdate"
                                    label="Catatan"
                                />
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="row w-100">
                                <div class="col-6">
                                    <x-button type="reset" color="light" data-bs-dismiss="modal" startIcon="close" fullWidth>
                                        Batal
                                    </x-button>
                                </div>

                                <div class="col-6">
                                    <x-button type="submit" color="secondary" startIcon="save" fullWidth>
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

    {{-- Javascript --}}
    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.buttonFilter = $('#buttonFilter');
                    this.formFilter = $('#formFilter');
                    this.tableDetail = this.detailDataTable();
                    this.buttonCreate = $('#buttonCreate');
                    this.modalCreate = this.modalCreate();
                    this.formCreate = $('#formCreate');
                    this.buttonUpdate = $('#buttonUpdate');
                    this.formUpdate = $('#formUpdate');
                    this.modalUpdate = this.modalUpdate();
                }

                /**
                 * Inisialisasi modal create
                 */
                modalCreate() {
                    const modal = $('#modalCreate');

                    if (modal.length === 0) return null;
                    return new bootstrap.Modal(modal);
                }

                /**
                 * Inisialisasi modal update
                 */
                modalUpdate() {
                    const modal = $('#modalUpdate');

                    if (modal.length === 0) return null;
                    return new bootstrap.Modal(modal);
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
                                className: 'text-nowrap',
                                type: 'string',
                                footer: 'Total',
                            },
                            {
                                title: 'Kode Produk',
                                name: 'product.code',
                                data: 'product.code',
                                className: 'text-nowrap',
                                type: 'string',
                                footer: '',
                            },
                            {
                                title: 'Satuan',
                                name: 'product.unit.code',
                                data: 'product.unit.code',
                                className: 'text-nowrap',
                                type: 'string',
                                footer: '',
                            },
                            {
                                title: 'Quantity',
                                name: 'quantity',
                                data: 'quantity',
                                className: 'text-nowrap',
                                type: 'html-num',
                                footer: app.numberFormat(0, 2),
                                render: (data) => app.numberFormat(data, 2),
                            },
                        ],
                        footerCallback: function(row, data, start, end) {
                            const api = this.api();
                            const totalQuantity = api.column(3).data().reduce((a, b) => a + b, 0);

                            $(api.column(3).footer()).text(app.numberFormat(totalQuantity));
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

                /**
                 * Menyimpan (insert) data stok bulanan.
                 */
                createMonthlyStock() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "post",
                            url: this.formCreate.attr('action'),
                            data: this.formCreate.serialize(),
                            dataType: "json",
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }

                /**
                 * Update data stok bulanan.
                 */
                updateMonthlyStock() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "patch",
                            url: this.formUpdate.attr('action'),
                            data: this.formUpdate.serialize(),
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
             * Request data stock awal saat dokumen dimuat.
             */
            $(document).ready(async function () {
                const params = new URLSearchParams(window.location.search);
                const year = params.get('year');
                const month = params.get('month');

                if (year === null || month === null) {
                    return;
                }

                app.showPreloader();
                app.clearFormErrors(service.formFilter);
                service.tableDetail.clear().draw();

                try {
                    const response = await service.findMonthlyStock();
                    
                    $('#dailyStockOpnameDate').text(app.dateFormat(response.data.stock.daily_stock_opname.date));
                    $('#totalProduct').text(app.numberFormat(response.data.stock.total_product));
                    $('#totalQuantity').text(app.numberFormat(response.data.stock.total_quantity));

                    service.tableDetail.rows.add(response.data.details).draw();
                    service.buttonUpdate.removeAttr('disabled');
                    service.formUpdate.attr('action', app.url(`/stock-opnames/monthly/${response.data.stock.id}`));
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
                service.tableDetail.clear().draw();
                service.buttonUpdate.attr('disabled', 'disabled');
                service.formUpdate.attr('action', '#');

                try {
                    const response = await service.findMonthlyStock();
                    
                    $('#dailyStockOpnameDate').text(app.dateFormat(response.data.stock.daily_stock_opname.date));
                    $('#totalProduct').text(app.numberFormat(response.data.stock.total_product));
                    $('#totalQuantity').text(app.numberFormat(response.data.stock.total_quantity));
                    
                    service.tableDetail.rows.add(response.data.details).draw();
                    service.buttonUpdate.removeAttr('disabled');
                    service.formUpdate.attr('action', app.url(`/stock-opnames/monthly/${response.data.stock.id}`));
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
                service.modalCreate?.show();
            });

            /**
             * Submit form create
             */
            service.formCreate.submit(async function (e) { 
                e.preventDefault();
                app.showPreloader();
                app.clearFormErrors(service.formCreate);

                try {
                    const response = await service.createMonthlyStock();

                    app.alert('success', response.message)
                    service.modalCreate?.hide();
                } catch (error) {
                    app.setFormErrors(service.formCreate, error);
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Membuka modal update stock bulanan
             */
            service.buttonUpdate.click(function (e) { 
                e.preventDefault();
                app.clearFormErrors(service.formUpdate);
                service.formUpdate.trigger('reset');
                service.modalUpdate?.show();
            });

            /**
             * Submit form update stock bulanan
             */
            service.formUpdate.submit(async function (e) { 
                e.preventDefault();
                app.showPreloader();
                app.clearFormErrors(service.formUpdate);

                console.log(service.formFilter.attr('action'));
                

                try {
                    const response = await service.updateMonthlyStock();
                    
                    if (response.status === 200) {
                        await service.findMonthlyStock()
    
                        app.alert('success', response.message);
                        service.modalUpdate?.hide();
                    }
                } catch (error) {
                    app.setFormErrors(service.formUpdate, error);                    
                } finally {
                    app.hidePreloader();
                }
            });
        </script>
    </x-slot:script>
</x-layouts.auth>