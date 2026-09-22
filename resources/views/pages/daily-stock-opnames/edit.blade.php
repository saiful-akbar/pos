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
                            <span class="text-body-secondary">Total produk</span>
                            <span class="fw-bold" id="totalProduct">{{ number_format($dailyStockOpname->totalProduct) }}</span>
                        </li>

                        <li class="list-group-item d-flex align-items-center justify-content-between bg-body px-0">
                            <span class="text-body-secondary">Total stock system</span>
                            <span class="fw-bold" id="totalSystemStock">{{ number_format($dailyStockOpname->totalSystemStock) }}</span>
                        </li>

                        <li class="list-group-item d-flex align-items-center justify-content-between bg-body px-0">
                            <span class="text-body-secondary">Total stock fisik</span>
                            <span class="fw-bold" id="totalPhysicalStock">{{ number_format($dailyStockOpname->totalPhysicalStock) }}</span>
                        </li>

                        <li class="list-group-item d-flex align-items-center justify-content-between bg-body px-0">
                            <span class="text-body-secondary">Total Selisih</span>
                            <span class="fw-bold" id="totalDifference">{{ number_format($dailyStockOpname->totalDifference) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
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
                        <input type="number" name="quantity" id="editQuantity"
                            class="form-control" min="0" data-invalid="quantity"
                            placeholder="Masukan quantity..." required>

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
                    this.id = "{{ $dailyStockOpname->id }}";
                    this.closeButton = $('#closeButton');
                    this.detailTable = this.detailTable();
                    this.productButton = $('#productButton');
                    this.productModal = new bootstrap.Modal('#productModal');
                    this.productTable = this.productTable();
                    this.csrfToken = $('meta[name="csrf-token"]').attr('content');
                    this.formEditDetail = $('#formEditDetail');
                    this.modalEditDetail = new bootstrap.Modal('#modalEditDetail');
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
                        fixedColumns: {
                            start: 1,
                            end: 1,
                        },
                        columns: [
                            {
                                title: 'Kode Produk',
                                name: 'product.code',
                                data: 'product.code',
                                className: 'text-nowrap px-3',
                                type: 'string',
                            },
                            {
                                title: 'Stock System',
                                name: 'system_stock',
                                data: 'system_stock',
                                className: 'text-nowrap px-3',
                                type: 'num',
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Stock Fisik',
                                name: 'physical_stock',
                                data: 'physical_stock',
                                className: 'text-nowrap px-3',
                                type: 'num',
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Selisih',
                                name: 'difference',
                                data: 'difference',
                                className: 'text-nowrap px-3',
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
                                            <button type="button" class="btn btn-light w-100" title="Edit" data-action="edit">
                                                <span class="material-symbols-outlined fs-6">edit</span>
                                            </button>
                                            <button type="button" class="btn btn-light w-100" title="Hapus" data-action="delete">
                                                <span class="material-symbols-outlined fs-6">delete</span>
                                            </button>
                                        </div>
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
                                service.formEditDetail.find('input[name="quantity"]').val(app.numberFormat(data.physical_stock));
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
                                const stock = response.data;

                                $('#date').text(app.dateFormat(stock.date));
                                $('#totalProduct').text(app.numberFormat(stock.total_product));
                                $('#totalSystemStock').text(app.numberFormat(stock.total_system_stock));
                                $('#totalPhysicalStock').text(app.numberFormat(stock.total_physical_stock));
                                $('#totalDifference').text(app.numberFormat(stock.total_difference));

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
                            data: {
                                _token: this.csrfToken,
                                _method: 'DELETE',
                            },
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
            service.productButton.click(function (e) { 
                e.preventDefault();
                service.productModal.show();
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
        </script>
    </x-slot:script>
</x-layouts.auth>