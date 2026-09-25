<x-layouts.auth title="Stock Opname Harian" active="summary" :access="$access">
    <x-page-header title="Stock Opname Harian" icon="inventory">
        @if ($access?->create)
            <x-slot:action>
                <div class="row justify-content-end gx-0">
                    <div class="col-12 col-lg-6 col-xl-4">
                        <x-button type="button" start-icon="upload" id="buttonImport" full-width>
                            Import Excel
                        </x-button>
                    </div>
                </div>
            </x-slot:action>
        @endif
    </x-page-header>

    {{-- Form modal import excel --}}
    @if ($access?->create)
        <form id="formImport" action="{{ route('stockOpnames.daily.import') }}" method="post" autocomplete="off" enctype="multipart/form-data">
            @csrf @method('POST')
            
            <div class="modal" tabindex="-1" id="modalImport">
                <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-md-down">
                    <div class="modal-content border">
                        <div class="modal-header">
                            <h5 class="modal-title">Impor Excel</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
        
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <x-button type="link" href="{{ route('stockOpnames.daily.export') }}" start-icon="download" full-width>
                                        Unduh Template Excel
                                    </x-button>
                                </div>
        
                                <div class="col-12 my-5">
                                    <hr>
                                </div>
        
                                <div class="col-12 mb-5">
                                    <x-form-input
                                        required
                                        type="text"
                                        name="date"
                                        id="date"
                                        label="Tanggal"
                                        placeholder="Tanggal"
                                        value="{{ old('date', date('Y-m-d')) }}"
                                        data-flatpickr=""
                                    />
                                </div>
        
                                <div class="col-12 mb-5">
                                    <x-form-input
                                        required
                                        type="file"
                                        name="file"
                                        id="file"
                                        label="File"
                                        placeholder="File"
                                        accept=".xlsx"
                                    />
                                </div>
        
                                <div class="col-12">
                                    <x-form-input
                                        type="textarea"
                                        name="notes"
                                        id="notes"
                                        label="Catatan"
                                        placeholder="Masukan catatan..."
                                        value="{{ old('notes') }}"
                                    />
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <div class="row w-100">
                                <div class="col-6">
                                    <x-button type="button" color="light" data-bs-dismiss="modal" start-icon="close" full-width>
                                        Close
                                    </x-button>
                                </div>
        
                                <div class="col-6">
                                    <x-button type="submit" color="secondary" start-icon="save" full-width>
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
            <form action="{{ route('stockOpnames.daily') }}" method="GET" id="formFilter" autocomplete="off">
                @csrf @method('GET')

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-5 mb-lg-0">
                                <div @class(['form-floating', 'is-invalid' => $errors->has('from_date')]) data-invalid="from_date">
                                    <input type="text" name="from_date" id="fromDate"
                                        @class(['form-control', 'is-invalid' => $errors->has('from_date')])
                                        placeholder="Dari Tanggal" value="{{ request('from_date') }}"
                                        data-invalid="from_date" data-flatpickr="" required>
                                    
                                    <label for="fromDate">
                                        Dari Tanggal <span class="text-danger">*</span>
                                    </label>
                                </div>

                                <div class="invalid-feedback" data-invalid-feedback="from_date">
                                    {{ $errors->first('from_date') }}
                                </div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <div @class(['form-floating', 'is-invalid' => $errors->has('to_date')]) data-invalid="to_date">
                                    <input type="text" name="to_date" id="toDate"
                                        @class(['form-control', 'is-invalid' => $errors->has('to_date')])
                                        placeholder="Sampai Tanggal" value="{{ request('to_date') }}"
                                        data-invalid="to_date" data-flatpickr="" required>
                                    
                                    <label for="toDate">
                                        Sampai Tanggal <span class="text-danger">*</span>
                                    </label>
                                </div>

                                <div class="invalid-feedback" data-invalid-feedback="to_date">
                                    {{ $errors->first('to_date') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row justify-content-end w-100 gx-0">
                            <div class="col-12 col-lg-2">
                                <x-button type="submit" start-icon="filter_list" full-width>Filter</x-button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="row mt-6">
        <div class="col-12 col-lg-4 mb-6 mb-lg-0">
            <div class="card shadow-sm" style="min-height: 500px;">
                <div class="card-header">
                    <h3 class="fs-5 m-0">Summary</h3>
                </div>

                <div class="card-body">
                    <table id="summaryTable" class="table table-hover table-sm align-middle w-100"></table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card shadow-sm" style="min-height: 500px;">
                @if ($access?->update)
                    <div class="card-header">
                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-secondary" id="buttonActiveStock" disabled>
                                <x-icon class="me-1">check_circle</x-icon>
                                <span>Jadikan Stok Aktif</span>
                            </button>

                            <button type="button" class="btn btn-secondary" id="buttonMonthlyStock" disabled>
                                <x-icon class="me-1">inventory_2</x-icon>
                                <span>Jadikan Stok Awal</span>
                            </button>

                            <button type="button" class="btn btn-secondary" id="buttonEditStock" disabled>
                                <x-icon class="me-1">edit</x-icon>
                                <span>Edit Stock</span>
                            </button>
                        </div>
                    </div>
                @endif

                <div class="card-body">
                    <table id="detailTable" class="table table-hover table-sm align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- Form modal pilih stok awal bulan --}}
    @if ($access?->update)
        <form action="#" method="post" id="formMonthlyStock" autocomplete="off">
            @csrf @method('post')

            <div class="modal" tabindex="-1" id="modalMonthlyStock">
                <div class="modal-dialog modal-fullscreen-md-down">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Stok Awal</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
        
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12 mb-6">
                                    <div class="form-floating" data-invalid="year">
                                        <input type="number" name="year" id="year" class="form-control"
                                            placeholder="Tahun" value="{{ date('Y') }}"
                                            data-invalid="year" required>
                                        
                                        <label for="year">
                                            Tahun <span class="text-danger">*</span>
                                        </label>
                                    </div>
        
                                    <div class="ivalid-feedback" data-invalid-feedback="year"></div>
                                </div>
        
                                <div class="col-12 mb-6">
                                    <div class="form-floating" data-invalid="month">
                                        <select name="month" id="month" class="form-select" data-invalid="month" required>
                                            <option value="01" @selected(date('m') == '01')>Januari</option>
                                            <option value="02" @selected(date('m') == '02')>Februari</option>
                                            <option value="03" @selected(date('m') == '03')>Maret</option>
                                            <option value="04" @selected(date('m') == '04')>April</option>
                                            <option value="05" @selected(date('m') == '05')>Mei</option>
                                            <option value="06" @selected(date('m') == '06')>Juni</option>
                                            <option value="07" @selected(date('m') == '07')>Juli</option>
                                            <option value="08" @selected(date('m') == '08')>Agustus</option>
                                            <option value="09" @selected(date('m') == '09')>September</option>
                                            <option value="10" @selected(date('m') == '10')>Oktober</option>
                                            <option value="11" @selected(date('m') == '11')>November</option>
                                            <option value="12" @selected(date('m') == '12')>Desember</option>
                                        </select>
                                        
                                        <label for="month">
                                            Bulan <span class="text-danger">*</span>
                                        </label>
                                    </div>
        
                                    <div class="invalid-feedback" data-invalid-feedback="month"></div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating" data-invalid="notes">
                                        <textarea name="notes" id="monthlyNotes" class="form-control" placeholder="Catatan" data-invalid="notes"></textarea>
                                        <label for="monthlyNotes">Catatan</label>
                                    </div>

                                    <div class="invalid-feedback" data-invalid-feedback="notes"></div>
                                </div>
                            </div>
                        </div>
        
                        <div class="modal-footer">
                            <div class="row w-100">
                                <div class="col-6">
                                    <x-button type="reset" color="light" startIcon="close" data-bs-dismiss="modal" fullWidth>Tutup</x-button>
                                </div>
                                <div class="col-6">
                                    <x-button type="submit" color="secondary" startIcon="save" fullWidth>Simpan</x-button>
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
                    this.buttonImport = $('#buttonImport');
                    this.modalImport = this.modalImport();
                    this.formImport = $('#formImport');
                    this.formFilter = $('#formFilter');
                    this.summaryTable = this.summaryTable();
                    this.detailTable = this.detailTable();
                    this.buttonEditStock = $('#buttonEditStock');
                    this.buttonMonthlyStock = $('#buttonMonthlyStock');
                    this.formMonthlyStock = $('#formMonthlyStock');
                    this.modalMonthlyStock = this.modalMonthlyStock();
                    this.buttonActiveStock = $('#buttonActiveStock');
                    this.csrfToken = $('meta[name="csrf-token"]').attr('content');
                }

                /**
                 * Inisialisasi modal import
                 */
                modalImport() {
                    const modal = $('#modalImport');

                    if (modal.length === 0) return null;
                    return new bootstrap.Modal(modal);
                }

                /**
                 * Inisialisasi modal monthly stock
                 */
                modalMonthlyStock() {
                    const modal = $('#modalMonthlyStock');

                    if (modal.length === 0) return null;
                    return new bootstrap.Modal(modal);
                }

                /**
                 * Summary table
                 */
                summaryTable() {
                    return app.dataTable('#summaryTable', {
                        scrollY: '625px',
                        lengthChange: false,
                        searching: false,
                        info: false,
                        paging: false,
                        columns: [
                            {
                                title: 'Tanggal',
                                name: 'date',
                                data: 'date',
                                className: 'text-nowrap px-3',
                                type: 'date',
                                render: (data) => app.dateFormat(data),
                            },
                            {
                                title: 'Total Quantity',
                                name: 'total_quantity',
                                data: 'total_quantity',
                                className: 'text-nowrap px-3',
                                type: 'num',
                                render: (data) => app.numberFormat(data, 2),
                            },
                        ],
                        createdRow: async (row, data, index) => {
                            $(row).addClass('cursor-pointer');

                            $(row).click(async (e) => { 
                                e.preventDefault();
                                app.showPreloader();
                                
                                this.buttonEditStock.removeAttr('disabled');
                                this.buttonEditStock.data('id', data.id);
                                this.buttonMonthlyStock.removeAttr('disabled');
                                this.buttonMonthlyStock.data('id', data.id);
                                this.buttonActiveStock.removeAttr('disabled');
                                this.buttonActiveStock.data('id', data.id);

                                try {
                                    const response = await this.getDetails(data.id);

                                    this.detailTable.clear().draw();
                                    this.detailTable.rows.add(response.data).draw();
                                } catch (error) {
                                    app.alert('danger', error.message);
                                } finally {
                                    app.hidePreloader();
                                }
                            });
                        }
                    })
                }

                /**
                 * Detail table
                 */
                detailTable() {
                    return app.buttonDataTable('#detailTable', {
                        info: false,
                        scrollX: false,
                        scrollY: '460px',
                        columns: [
                            {
                                title: 'Kode Produk',
                                name: 'product.code',
                                data: 'product.code',
                                className: 'text-nowrap px-3',
                                type: 'string',
                                footer: 'Total',
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
                                type: 'num',
                                footer: '0',
                                render: (data) => app.numberFormat(data, 2),
                            },
                        ],
                        footerCallback: function(row, data, start, end) {
                            const api = this.api();
                            const totalQuantity = api.column(2).data().reduce((a, b) => a + b, 0);

                            $(api.column(2).footer()).text(app.numberFormat(totalQuantity, 2));
                        }
                    });
                }

                /**
                 * Mengambil summary stock opname
                 */
                getSummaries() {
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

                /**
                 * Mengambil detail stock opname
                 */
                getDetails(headerId) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "GET",
                            url: `${app.baseUrl}/stock-opnames/daily/${headerId}`,
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }

                /**
                 * Update URL query params
                 */
                updateQueryParams(paramsObj) {
                    const url = new URL(window.location);
                    
                    Object.keys(paramsObj).forEach(key => {
                        url.searchParams.set(key, paramsObj[key]);
                    });
                    
                    window.history.pushState({}, '', url);
                }

                /**
                 * Menjadikan daily stock sebagai stok aktif produk.
                 */
                setActiveStock(id) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "patch",
                            url: app.url(`/stock-opnames/daily/${id}/active`),
                            data: {
                                _token: this.csrfToken,
                            },
                            dataType: "json",
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }

                /**
                 * Menjadikan stok harian sebagai stok bulanan.
                 */
                createMonthlyStock(dailyStockId) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "POST",
                            url: app.url(`/stock-opnames/daily/${dailyStockId}/monthly`),
                            data: this.formMonthlyStock.serialize(),
                            dataType: "json",
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }
            }

            window.service = new Service();
        </script>

        {{-- Global script --}}
        <script>
            $(document).ready(async function () {
                const params = new URLSearchParams(window.location.search);
                const fromDate = params.get('from_date');
                const toDate = params.get('to_date');

                if (fromDate === null || toDate === null) {
                    return;
                }

                app.showPreloader();

                try {
                    const response = await service.getSummaries();

                    service.summaryTable.clear().draw();
                    service.summaryTable.rows.add(response.data).draw();
                } catch (error) {
                    app.alert('danger', error.message);
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Membuka modal import excel
             */
            service.buttonImport.click(function(e) {
                e.preventDefault();
                service.modalImport?.show();
                
            });
            
            /**
             * Submit form import excel.
             */
            service.formImport.submit(function (e) { 
                app.showPreloader();
            });
            
            /**
             * Submit form filter
             */
            service.formFilter.submit(async function (e) { 
                e.preventDefault();

                app.showPreloader();

                service.detailTable.clear().draw();
                service.buttonEditStock.attr('disabled', 'disabled');
                service.buttonEditStock.data('id', '');
                service.buttonMonthlyStock.attr('disabled', 'disabled');
                service.buttonMonthlyStock.data('id', '');
                service.buttonActiveStock.attr('disabled', 'disabled');
                service.buttonActiveStock.data('id', '');
                service.formFilter.find('[data-invalid]').removeClass('is-invalid');
                service.formFilter.find('[data-invalid-feedback]').text('');

                try {
                    const fromDate = service.formFilter.find('input[name="from_date"]').val();
                    const toDate = service.formFilter.find('input[name="to_date"]').val();
                    const response = await service.getSummaries();

                    service.summaryTable.clear().draw();
                    service.summaryTable.rows.add(response.data).draw();

                    service.updateQueryParams({
                        from_date: fromDate,
                        to_date: toDate,
                    });
                } catch (error) {
                    app.alert('danger', error.message);

                    Object.entries(error.errors).forEach(([field, message]) => {
                        service.formFilter.find(`[data-invalid="${field}"]`).addClass('is-invalid');
                        service.formFilter.find(`[data-invalid-feedback="${field}"]`).text(message[0]);
                    });
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Edit stock
             */
            service.buttonEditStock.click(function (e) { 
                e.preventDefault();
                const id = service.buttonEditStock.data('id');
                window.open(`${app.baseUrl}/stock-opnames/daily/${id}/edit`);
            });

            /**
             * Menjadikan daily stock sebagai current_stock product
             */
            service.buttonActiveStock.click(function (e) { 
                e.preventDefault();

                const title = 'Jadikan Stok Aktif';
                const message = 'Stok hasil stock opname ini akan ditetapkan sebagai stok aktif produk. Nilai stok aktif saat ini akan digantikan dengan stok hasil opname. <br>Anda yakin ingin menlanjutkan?';
                
                app.confirm(title, message, async (result) => {
                    if (!result) {
                        return;
                    }

                    app.showPreloader();

                    try {
                        const response = await service.setActiveStock(service.buttonActiveStock.data('id'));

                        app.alert('success', response.message);
                    } catch (error) {
                        app.alert('danger', error.message);
                    } finally {
                        app.hidePreloader();
                    }
                })
            });
            
            /**
             * Membuka modal untuk menjadikan daily stock sebagai monthly stock.
             */
            service.buttonMonthlyStock.click(function (e) { 
                e.preventDefault();
                service.formMonthlyStock.find('[data-invalid]').removeClass('is-invalid');
                service.formMonthlyStock.find('[data-invalid-feedback]').text('');
                service.modalMonthlyStock?.show();
            });

            /**
             * Submit form create monthly stock
             */
            service.formMonthlyStock.submit(async function (e) { 
                e.preventDefault();
                
                app.showPreloader();
                service.formMonthlyStock.find('[data-invalid]').removeClass('is-invalid');
                service.formMonthlyStock.find('[data-invalid-feedback]').text('');

                try {
                    const response = await service.createMonthlyStock(service.buttonMonthlyStock.data('id'));

                    service.formMonthlyStock.trigger('reset');
                    service.modalMonthlyStock?.hide();
                    app.alert('success', response.message);
                } catch (error) {
                    Object.entries(error.errors).forEach(([field, message]) => {
                        service.formMonthlyStock.find(`[data-invalid="${field}"]`).addClass('is-invalid');
                        service.formMonthlyStock.find(`[data-invalid-feedback="${field}"]`).text(message[0]);
                    });
                } finally {
                    app.hidePreloader();
                }
            });
        </script>
    </x-slot:script>
</x-layouts.auth>