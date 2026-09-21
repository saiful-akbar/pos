<x-layouts.auth title="Edit daily stock opname">
    <x-page-header title="Edit daily stock opname" icon="inventory">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-4 col-xl-3">
                    <x-button type="button" color="danger" start-icon="close" id="closeButton" full-width>Tutup</x-button>
                </div>
            </div>
        </x-slot:action>
    </x-page-header>

    <div class="row mt-6">
        <div class="col-12 col-lg-4 mb-6 mb-lg-0">
            <div class="card shadow-sm position-sticky" style="top: 20px;">
                <div class="card-header">
                    <h3 class="fs-5 m-0">Info</h3>
                </div>

                <div class="card-body">
                    <ul class="list-group list-group-flush m-0">
                        <li class="list-group-item d-flex align-items-center justify-content-between bg-body px-0">
                            <span class="text-body-secondary">Tanggal</span>
                            <span class="fw-bold" id="date">{{ $dailyStockOpname->date->format('d M Y') }}</span>
                        </li>
                        
                        <li class="list-group-item d-flex align-items-center justify-content-between bg-body px-0">
                            <span class="text-body-secondary">Total Produk</span>
                            <span class="fw-bold" id="totalProduct">{{ number_format($dailyStockOpname->totalProduct) }}</span>
                        </li>

                        <li class="list-group-item d-flex align-items-center justify-content-between bg-body px-0">
                            <span class="text-body-secondary">Total Stok</span>
                            <span class="fw-bold" id="totalPhysicalStock">{{ number_format($dailyStockOpname->totalPhysicalStock) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="row">
                <div class="col-12">
                    <form action="{{ route('stockOpnames.daily.details.store', ['id' => $dailyStockOpname->id]) }}" method="POST" name="create_detail" autocomplete="off">
                        @csrf @method('POST')

                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h3 class="fs-5 m-0">Tambah Stok</h3>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-6 mb-6 mb-lg-0">
                                        <div class="input-group" data-invalid="product_code">
                                            <div class="form-floating">
                                                <input type="text" name="product_code" data-invalid="product_code" id="productCode"
                                                    class="form-control" placeholder="Kode Produk" required>
                                                
                                                <label for="productCode">
                                                    Kode Produk <span class="text-danger">*</span>
                                                </label>
                                            </div>
            
                                            <x-button type="button" data-bs-toggle="tooltip" data-bs-title="Pilih produk" id="productButton">
                                                <x-icon class="fs-6 text-secondary">table</x-icon>
                                            </x-button>
                                        </div>
            
                                        <div class="invalid-feedback" data-invalid-feedback="product_code"></div>
                                    </div>
            
                                    <div class="col-12 col-lg-6">
                                        <div class="form-floating" data-invalid="quantity">
                                            <input type="number" name="quantity" id="quantity" data-invalid="quantity"
                                                class="form-control" placeholder="Quantity" required>
                                            
                                            <label for="quantity">
                                                Quantity <span class="text-danger">*</span>
                                            </label>
                                        </div>
            
                                        <div class="invalid-feedback" data-invalid-feedback="quantity"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div class="row w-100 justify-content-end gx-0">
                                    <div class="col-12 col-lg-4 col-xl-3">
                                        <x-button type="submit" startIcon="add" fullWidth>Tambah</x-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mt-6">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h3 class="fs-5 m-0">Detail</h3>
                        </div>
        
                        <div class="card-body">
                            <table id="detailTable" class="table table-hover table-sm align-middle w-100"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Product modal --}}
    <div class="modal" tabindex="-1" id="productModal">
        <div class="modal-dialog modal-xl modal-fullscreen-md-down">
            <div class="modal-content border">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <table id="productTable" class="table table-hover table-sm table-img align-middle w-100"></table>
                </div>

                <div class="modal-footer">
                    <div class="row justify-content-end gx-0 w-100">
                        <div class="col-12 col-lg-4 col-xl-2">
                            <x-button type="button" color="light" startIcon="close" data-bs-dismiss="modal" fullWidth>
                                Tutup
                            </x-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Javascript --}}
    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.id = "{{ $dailyStockOpname->id }}";
                    this.closeButton = $('#closeButton');
                    this.detailTable = this.detailTable();
                    this.productButton = $('#productButton');
                    this.productModal = new bootstrap.Modal('#productModal');
                    this.productTable = this.productTable();
                    this.formCreateDetail = $('form[name="create_detail"]')
                }

                /**
                 * Product dataTable
                 */
                productTable() {
                    return app.modalDataTable('#productTable', {
                        serverSide: true,
                        ajax: "{{ route('master.products.active') }}",
                        columns: [
                            {
                                title: 'Kode',
                                name: 'code',
                                data: 'code',
                                className: 'text-nowrap px-3',
                                type: 'string',
                                render: (data, type, row) => {
                                    return `
                                        <div class="d-flex align-items-center">
                                            <img src="${app.storageImage(row.image)}" class="rounded" alt="Produk" />
                                            <div class="ms-4">
                                                <div>${row.code}</div>
                                                <div class="fs-sm text-body-secondary text-truncate" style="max-width: 150px;">${row.name}</div>
                                            </div>
                                        </div>
                                    `;
                                }
                            },
                            {
                                title: 'Barcode',
                                name: 'barcode',
                                data: 'barcode',
                                type: 'string',
                                className: 'text-nowrap px-3',
                            },
                            {
                                title: 'Stok',
                                name: 'current_stock',
                                data: 'current_stock',
                                className: 'text-nowrap px-3',
                                type: 'num',
                                searching: false,
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Minimum Stok',
                                name: 'minimum_stock',
                                data: 'minimum_stock',
                                className: 'text-nowrap px-3',
                                type: 'num',
                                searching: false,
                                render: (data) => app.numberFormat(data),
                            },
                        ],
                        createdRow: (row, data, index) => {
                            $(row).addClass('cursor-pointer');

                            $(row).click((e) => {
                                e.preventDefault();

                                this.formCreateDetail.find('input[name="product_code"]').val(data.code);
                                this.productModal.hide();
                            });
                        }
                    });
                }

                /**
                 * Detail data table
                 */
                detailTable() {
                    return app.buttonDataTable('#detailTable', {
                        info: false,
                        ordering: false,
                        fixedColumns: {
                            start: 1,
                            end: 1,
                        },
                        columns: [
                            {
                                title: 'Kode Produk',
                                name: 'product.code',
                                data: 'product.code',
                                className: 'text-nowrap text-start px-3',
                                type: 'string',
                            },
                            {
                                title: 'Stok',
                                name: 'physical_stock',
                                data: 'physical_stock',
                                className: 'text-nowrap text-start px-3',
                                type: 'num',
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: `<span class="material-symbols-outlined">settings</span>`,
                                name: 'id',
                                data: 'id',
                                className: 'text-center text-nowrap px-3 no-export',
                                type: 'html',
                                searchable: false,
                                orderable: false,
                                render: (data, type, row) => {
                                    return `
                                        <div class="btn-group w-100">
                                            <button type="button" class="btn btn-light w-100" title="Edit">
                                                <span class="material-symbols-outlined fs-6">edit</span>
                                            </button>
                                            <button type="button" class="btn btn-light w-100" title="Hapus">
                                                <span class="material-symbols-outlined fs-6">delete</span>
                                            </button>
                                        </div>
                                    `;
                                }
                            }
                        ],
                    });
                }

                /**
                 * Mengambil data stock opname
                 */
                findStockOpname() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "GET",
                            url: `${app.baseUrl}/stock-opnames/daily/${this.id}/edit`,
                            success: (response) => {
                                const stock = response.data;

                                $('#date').text(app.dateFormat(stock.date));
                                $('#totalProduct').text(app.numberFormat(stock.total_product));
                                $('#totalPhysicalStock').text(app.numberFormat(stock.total_physical_stock));

                                resolve(response);
                            },
                            error: (error) => {
                                const { message } = error.responseJSON;

                                app.alert('danger', message);
                                reject(error.responseJSON);
                            }
                        });
                    })
                }

                /**
                 * Mengambil daftar detail stok
                 */
                getStockOpnameDetails() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "GET",
                            url: `${app.baseUrl}/stock-opnames/daily/${this.id}`,
                            success: (response) => {
                                resolve(response);
                                this.detailTable.clear().draw();
                                this.detailTable.rows.add(response.data).draw();
                            },
                            error: (error) => {
                                reject(error.responseJSON);
                                app.alert('danger', error.responseJSON.message);
                            },
                        });
                    });
                }

                /**
                 * Create detail
                 */
                createDetail() {
                    this.formCreateDetail.find('[data-invalid]').removeClass('is-invalid');
                    this.formCreateDetail.find('[data-invalid-feelback]').text('');

                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "POST",
                            url: this.formCreateDetail.attr('action'),
                            data: this.formCreateDetail.serialize(),
                            dataType: "json",
                            success: (response) => {
                                this.formCreateDetail.trigger('reset');
                                app.alert('success', response.message);
                                resolve(response);
                            },
                            error: (error) => {
                                const { errors, message } = error.responseJSON;
                                
                                Object.entries(errors).forEach(([field, message]) => {
                                    this.formCreateDetail.find(`[data-invalid="${field}"]`).addClass('is-invalid');
                                    this.formCreateDetail.find(`[data-invalid-feedback="${field}"]`).text(message[0]);
                                })
                                
                                app.alert('danger', message);
                                reject(error.responseJSON);
                            },
                        });
                    })
                }
            }

            window.service = new Service();
        </script>



        <script>
            $(document).ready(function () {
                service.findStockOpname();
                service.getStockOpnameDetails();
            });

            /**
             * Close window
             */
            service.closeButton.click((e) => {
                e.preventDefault();
                window.close();
            });

            /**
             * Menampilkan modal product
             */
            service.productButton.click(function (e) { 
                e.preventDefault();
                service.productModal.show();
            });

            /**
             * Submit form create detail.
             */
            service.formCreateDetail.submit(async function (e) { 
                e.preventDefault();
                app.showPreloader();

                try {
                    await service.createDetail();
                    await service.findStockOpname();
                    await service.getStockOpnameDetails();
                } finally {
                    app.hidePreloader();
                }
            });
        </script>
    </x-slot:script>
</x-layouts.auth>