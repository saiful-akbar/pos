<x-layouts.auth title="Edit stok opname">
    <x-page-header title="Edit stok opname" icon="inventory">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-4">
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
                            <span class="fw-bold" id="date">{{ $dailyStock->date->format('d M Y') }}</span>
                        </li>
                        
                        <li class="list-group-item d-flex align-items-center justify-content-between bg-body px-0">
                            <span class="text-body-secondary">Total Produk</span>
                            <span class="fw-bold" id="totalProduct">{{ number_format($dailyStock->totalProduct) }}</span>
                        </li>

                        <li class="list-group-item d-flex align-items-center justify-content-between bg-body px-0">
                            <span class="text-body-secondary">Total Quantity</span>
                            <span class="fw-bold" id="totalQuantity">{{ number_format($dailyStock->totalQuantity, 2) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="row">
                <div class="col-12">
                    <form action="{{ route('stockOpnames.daily.details.store', ['id' => $dailyStock->id]) }}" method="post" autocomplete="off" id="formCreate">
                        @csrf

                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h2 class="fs-5 mb-0">Tambah</h2>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-6 mb-5 mb-lg-0">
                                        <div class="input-group" data-invalid="product_code">
                                            <div class="form-floating" data-invalid="product_code">
                                                <input type="text" name="product_code" id="productCode" class="form-control" placeholder="Kode Produk" data-invalid="product_code" required>
                                                
                                                <label for="product_code">
                                                    Kode Produk <span class="text-danger">*</span>
                                                </label>
                                            </div>

                                            <x-button type="button" data-bs-toggle="tooltip" data-bs-title="Pilih produk" id="buttonProduct">
                                                <x-icon class="text-secondary">table</x-icon>
                                            </x-button>
                                        </div>

                                        <div class="invalid-feedback" data-invalid-feedback="product_code"></div>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <div class="form-floating" data-invalid="quantity">
                                            <input type="number" name="quantity" id="quantity" class="form-control" placeholder="Quantity" data-invalid="quantity" min="0" required>
                                            
                                            <label for="quantity">
                                                Quantity <span class="text-danger">*</span>
                                            </label>
                                        </div>

                                        <div class="invalid-feedback" data-invalid-feedback="quantity"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div class="row justify-content-end align-items-center">
                                    <div class="col-12 col-lg-4 col-xl-3">
                                        <x-button type="submit" startIcon="add" fullWidth>Tambah</x-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mt-6">
                <div class="col-12">
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
    <div class="modal" tabindex="-1" id="modalProduct">
        <div class="modal-dialog modal-xl modal-fullscreen-md-down">
            <div class="modal-content border">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <table id="tableProduct" class="table table-hover table-sm table-img align-middle w-100"></table>
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

    {{-- Form modal edit quantity --}}
    <form action="#" method="post" id="formEditDetail" autocomplete="off">
        @csrf @method('patch')

        <div class="modal" tabindex="-1" id="modalEditDetail">
            <div class="modal-dialog">
                <div class="modal-content border">
                    <div class="modal-header">
                        <h5 class="modal-title">Ubah Quantity</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
    
                    <div class="modal-body">
                        <input type="number" name="quantity" id="editQuantity" class="form-control" min="0" data-invalid="quantity" placeholder="Masukan quantity..." required>
                        <div class="invalid-feedback" data-invalid-feedback="quantity"></div>
                    </div>
    
                    <div class="modal-footer">
                        <div class="row w-100">
                            <div class="col-6">
                                <x-button type="button" color="light" startIcon="close" data-bs-dismiss="modal" fullWidth>
                                    Tutup
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

    {{-- Javascript --}}
    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.id = "{{ $dailyStock->id }}";
                    this.closeButton = $('#closeButton');
                    this.detailTable = this.detailTable();
                    this.buttonProduct = $('#buttonProduct');
                    this.modalProduct = new bootstrap.Modal('#modalProduct');
                    this.formCreate = $('#formCreate');
                    this.tableProduct = this.tableProduct();
                    this.formEditDetail = $('#formEditDetail');
                    this.modalEditDetail = new bootstrap.Modal('#modalEditDetail');
                }

                /**
                 * Product dataTable
                 */
                tableProduct() {
                    return app.modalDataTable('#tableProduct', {
                        serverSide: true,
                        ajax: "{{ route('master.products.active') }}",
                        columns: [
                            {
                                title: 'Kode',
                                name: 'code',
                                data: 'code',
                                className: 'text-nowrap',
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
                                className: 'text-nowrap',
                            },
                            {
                                title: 'Satuan',
                                name: 'unit.code',
                                data: 'unit.code',
                                type: 'string',
                                className: 'text-nowrap',
                            },
                            {
                                title: 'Stok Tersedia',
                                name: 'current_stock',
                                data: 'current_stock',
                                className: 'text-nowrap',
                                type: 'num',
                                searching: false,
                                render: (data) => app.numberFormat(data, 2),
                            },
                        ],
                        createdRow: (row, data, index) => {
                            $(row).addClass('cursor-pointer');

                            $(row).click((e) => {
                                e.preventDefault();
                                this.formCreate.find('input[name="product_code"]').val(data.code);
                                this.modalProduct.hide();
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
                        fixedColumns: {
                            start: 1,
                            end: 1,
                        },
                        columns: [
                            {
                                title: 'Kode Produk',
                                name: 'product.code',
                                data: 'product.code',
                                className: 'text-nowrap',
                                type: 'string',
                            },
                            {
                                title: 'Satuan',
                                name: 'product.unit.code',
                                data: 'product.unit.code',
                                className: 'text-nowrap',
                                type: 'string',
                            },
                            {
                                title: 'Quantity',
                                name: 'quantity',
                                data: 'quantity',
                                className: 'text-nowrap',
                                type: 'num',
                                render: (data) => app.numberFormat(data, 2),
                            },
                            {
                                title: `<span class="material-symbols-outlined">settings</span>`,
                                name: 'id',
                                data: 'id',
                                className: 'text-center text-nowrap no-export',
                                type: 'html',
                                searchable: false,
                                orderable: false,
                                render: (data, type, row) => {
                                    return `
                                    <button type="button" class="btn btn-light" title="Edit" data-action="edit">
                                        <span class="material-symbols-outlined fs-6">edit</span>
                                    </button>
                                    <button type="button" class="btn btn-light" title="Hapus" data-action="delete">
                                        <span class="material-symbols-outlined fs-6">delete</span>
                                    </button>
                                    `;
                                }
                            }
                        ],
                        createdRow: (row, data, index) => {
                            $(row).find('[data-action="edit"]').click((e) => {
                                e.preventDefault();
                                service.formEditDetail.attr('action', app.url(`/stock-opnames/daily/${this.id}/details/${data.id}`));
                                service.formEditDetail.find('[data-invalid]').removeClass('is-invalid');
                                service.formEditDetail.find('[data-invalid-feedback]').text('');
                                service.formEditDetail.find('input[name="quantity"]').val(app.numberFormat(data.quantity));
                                service.modalEditDetail.show();
                            });

                            $(row).find('[data-action="delete"]').click((e) => {
                                e.preventDefault();
                                deleteStockOpnameDetail(data.id);
                            });
                        }
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
                                $('#date').text(app.dateFormat(response.data.date));
                                $('#totalProduct').text(app.numberFormat(response.data.total_product));
                                $('#totalQuantity').text(app.numberFormat(response.data.total_quantity, 2));

                                resolve(response);
                            },
                            error: (error) => {
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
                 * Menghapus stock opname detail
                 */
                deleteStockOpnameDetail(id) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "DELETE",
                            url: app.url(`/stock-opnames/daily/${this.id}/details/${id}`),
                            data: { _token: app.csrfToken },
                            dataType: "json",
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    })
                }

                /**
                 * Update quantity daily stock opname detail.
                 */
                updateQuantityDailyStockOpnameDetail() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "PATCH",
                            url: this.formEditDetail.attr('action'),
                            data: this.formEditDetail.serialize(),
                            dataType: "json",
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    })
                }

                /**
                 * Create stock opname detail
                 */
                createStockOpnameDetail() {
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
            }

            window.service = new Service();
        </script>

        <script>
            $(document).ready(async function () {
                app.showPreloader();

                try {
                    await service.findStockOpname();
                    await service.getStockOpnameDetails();
                } catch (error) {
                    app.alert('danger', error.message);
                } finally {
                    app.hidePreloader();
                }
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
            service.buttonProduct.click(function (e) { 
                e.preventDefault();
                service.modalProduct.show();
            });

            /**
             * Fungsi untuk menghapus stock opname detail
             */
            const deleteStockOpnameDetail = (id) => {
                app.delete('Stok', async (deleted) => {
                    if (!deleted) {
                        return;
                    }

                    app.showPreloader();

                    try {
                        const response = await service.deleteStockOpnameDetail(id);

                        await service.findStockOpname();
                        await service.getStockOpnameDetails();

                        app.alert('success', response.message);
                    } catch (error) {
                        app.alert('danger', error.message);
                    } finally {
                        app.hidePreloader();
                    }
                });
            }

            /**
             * Submit form edit daily stock opname detail.
             */
            service.formEditDetail.submit(async function (e) { 
                e.preventDefault();
                app.showPreloader();

                service.formEditDetail.find(`[data-invalid]`).removeClass('is-invalid');
                service.formEditDetail.find(`[data-invalid-feedback]`).text('');

                try {
                    const response = await service.updateQuantityDailyStockOpnameDetail();

                    await service.findStockOpname();
                    await service.getStockOpnameDetails();

                    app.alert('success', response.message);
                    service.modalEditDetail.hide();
                } catch (error) {
                    app.alert('danger', error.message);

                    Object.entries(error.errors).forEach(([field, message]) => {
                        service.formEditDetail.find(`[data-invalid="${field}"]`).addClass('is-invalid');
                        service.formEditDetail.find(`[data-invalid-feedback="${field}"]`).text(message[0]);
                    });
                } finally {
                    app.hidePreloader();
                }
                
            });

            /**
             * Submit form create stock opname detail.
             */
            service.formCreate.submit(async function (e) { 
                e.preventDefault();
                
                app.showPreloader();
                service.formCreate.find('[data-invalid]').removeClass('is-invalid');
                service.formCreate.find('[data-invalid-feedback]').text('');

                try {
                    const response = await service.createStockOpnameDetail();

                    await service.findStockOpname();
                    await service.getStockOpnameDetails();

                    app.alert('success', response.message);
                    service.formCreate.trigger('reset');
                } catch (error) {
                    app.alert('danger', error.message);

                    Object.entries(error.errors).forEach(([field, message]) => {
                        service.formCreate.find(`[data-invalid="${field}"]`).addClass('is-invalid');
                        service.formCreate.find(`[data-invalid-feedback="${field}"]`).text(message[0]);
                    });
                } finally {
                    app.hidePreloader();
                }
            });
        </script>
    </x-slot:script>
</x-layouts.auth>