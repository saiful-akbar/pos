<x-layouts.auth title="Stock Opname Harian" active="summary" :access="$access">
    <x-page-header title="Stock Opname Harian" icon="inventory">
        <x-slot:action>
            <div class="row justify-content-end">
                @if ($access?->update)
                    <div class="col-6 col-lg-4 col-xl-3">
                        <x-button type="button" start-icon="edit" id="buttonEdit" full-width>
                            Edit
                        </x-button>
                    </div>
                @endif
        
                @if ($access?->create)
                    <div class="col-6 col-lg-4 col-xl-3">
                        <x-button type="button" start-icon="upload" id="buttonImport" full-width>
                            Import Excel
                        </x-button>
                    </div>
                @endif
            </div>
        </x-slot:action>
    </x-page-header>

    {{-- Form modal import excel --}}
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

    {{-- Form filter --}}
    <div class="row mt-6">
        <div class="col-12">
            <form action="{{ route('stockOpnames.daily') }}" method="GET" id="formFilter" autocomplete="off">
                @csrf @method('GET')

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-5 mb-lg-0">
                                <div @class(['form-floating', 'is-invalid' => $errors->has('from_date')])>
                                    <input type="text" name="from_date" id="fromDate"
                                        @class(['form-control', 'is-invalid' => $errors->has('from_date')])
                                        placeholder="Dari Tanggal" value="{{ request('from_date') }}"
                                        data-flatpickr="" required>
                                    
                                    <label for="fromDate">
                                        Dari Tanggal <span class="text-danger">*</span>
                                    </label>
                                </div>

                                <div class="invalid-feedback">
                                    {{ $errors->first('from_date') }}
                                </div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <div @class(['form-floating', 'is-invalid' => $errors->has('to_date')])>
                                    <input type="text" name="to_date" id="toDate"
                                        @class(['form-control', 'is-invalid' => $errors->has('to_date')])
                                        placeholder="Sampai Tanggal" value="{{ request('to_date') }}"
                                        data-flatpickr="" required>
                                    
                                    <label for="toDate">
                                        Sampai Tanggal <span class="text-danger">*</span>
                                    </label>
                                </div>

                                <div class="invalid-feedback">
                                    {{ $errors->first('to_date') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row justify-content-end w-100 gx-0">
                            <div class="col-12 col-lg-4 col-xl-2">
                                <x-button type="submit" start-icon="filter_list" full-width>
                                    Filter
                                </x-button>
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
            <div class="card shadow-sm" style="min-height: 600px;">
                <div class="card-header">
                    <h3 class="fs-5 m-0">Summary</h3>
                </div>

                <div class="card-body">
                    <table id="summaryTable" class="table table-hover table-sm align-middle w-100"></table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card shadow-sm" style="min-height: 600px;">
                <div class="card-header">
                    <div class="row justify-content-between align-items-center w-100 gx-0">
                        <div class="col">
                            <h3 class="fs-5 m-0">Detail</h3>
                        </div>

                        @if ($access?->update)
                            <div class="col-3">
                                <x-button type="button" start-icon="edit" id="editButton" full-width disabled>
                                    Edit
                                </x-button>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <table id="detailTable" class="table table-hover table-sm align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- Javascript --}}
    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.buttonImport = $('#buttonImport');
                    this.modalImport = new bootstrap.Modal('#modalImport');
                    this.formImport = $('#formImport');
                    this.formFilter = $('#formFilter');
                    this.summaryTable = this.summaryTable();
                    this.detailTable = this.detailTable();
                    this.editButton = $('#editButton')
                }

                /**
                 * Summary table
                 */
                summaryTable() {
                    return app.dataTable('#summaryTable', {
                        scrollY: '650px',
                        scrollX: true,
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
                                render: (data) => app.numberFormat(data),
                            },
                        ],
                        createdRow: async (row, data, index) => {
                            $(row).addClass('cursor-pointer');

                            $(row).click(async (e) => { 
                                e.preventDefault();
                                app.showPreloader();
                                
                                this.editButton.removeAttr('disabled');
                                this.editButton.data('id', data.id);

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
                        scrollY: '485px',
                        scrollX: false,
                        info: false,
                        paging: true,
                        columns: [
                            {
                                title: 'Kode Produk',
                                name: 'product.code',
                                data: 'product.code',
                                className: 'text-nowrap px-3',
                                type: 'string',
                                footer: 'Total'
                            },
                            {
                                title: 'Quantity',
                                name: 'quantity',
                                data: 'quantity',
                                className: 'text-nowrap px-3',
                                type: 'num',
                                render: (data) => app.numberFormat(data),
                            },
                        ],
                        footerCallback: function(row, data, start, end) {
                            const api = this.api();
                            const totalQuantity = api.column(1).data().reduce((a, b) => a + b, 0);

                            $(api.column(1).footer()).text(app.numberFormat(totalQuantity));
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
                service.modalImport.show();
                
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
                service.editButton.attr('disabled', 'disabled');
                service.editButton.data('id', '');

                service.formFilter.find('input[name="from_date"]').removeClass('is-invalid');
                service.formFilter.find('input[name="from_date"]').parent().removeClass('is-invalid');
                service.formFilter.find('input[name="from_date"]').parent().next().text('');
                service.formFilter.find('input[name="to_date"]').removeClass('is-invalid');
                service.formFilter.find('input[name="to_date"]').parent().removeClass('is-invalid');
                service.formFilter.find('input[name="to_date"]').parent().next().text('');

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
                        service.formFilter.find(`input[name="${field}"]`).addClass('is-invalid');
                        service.formFilter.find(`input[name="${field}"]`).parent().addClass('is-invalid');
                        service.formFilter.find(`input[name="${field}"]`).parent().next().text(message[0]);
                    });
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Edit stock
             */
            service.editButton.click(function (e) { 
                e.preventDefault();
                const id = service.editButton.data('id');
                window.open(`${app.baseUrl}/stock-opnames/daily/${id}/edit`);
            });
        </script>
    </x-slot:script>
</x-layouts.auth>