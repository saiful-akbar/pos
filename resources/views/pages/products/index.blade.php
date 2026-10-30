<x-layouts.auth title="Produk" :access="$access">
    <x-page-header title="Produk" icon="data_table">
        <x-slot:action>
            <div class="row justify-content-end">
                <div @class(["col-12", "col-lg-4" => !($access?->create ?? false), "col-lg-6" => ($access?->create ?? false)])>
                    <div class="btn-group w-100">
                        <x-button type="link" href="{{ route('masters.products.export') }}" startIcon="download" fullWidth>
                            Export
                        </x-button>
                        
                        @if ($access?->create ?? false)
                            <x-button id="buttonImport" startIcon="upload" fullWidth>
                                Import
                            </x-button>
                        @endif
                    </div>
                </div>
    
                @if ($access?->update ?? false)
                    <div class="col-12 col-lg-3 mt-5 mt-lg-0">
                        <div class="dropdown">
                            <x-button class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" startIcon="edit_square" fullWidth>
                                Update
                            </x-button>

                            <ul class="dropdown-menu dropdown-menu-end border">
                                <li><a class="dropdown-item" href="#" id="buttonResetDiscounts">Reset discount</a></li>
                                <li><a class="dropdown-item" href="#" id="buttonImportUpdateDiscounts">Import update diskon</a></li>
                                <li><a class="dropdown-item" href="#" id="buttonImportUpdatePrices">Import update harga</a></li>
                            </ul>
                        </div>
                    </div>
                @endif
    
                @if ($access?->create ?? false)
                    <div class="col-12 col-lg-3 mt-5 mt-lg-0">
                        <x-button type="link" href="{{ route('masters.products.create') }}" startIcon="add" fullWidth>
                            Tambah
                        </x-button>
                    </div>
                @endif
            </div>
        </x-slot:action>
    </x-page-header>

    <div class="row mt-7">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="productTable" class="table table-hover table-sm table-img align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- Form modal import excel --}}
    @if ($access?->create ?? false)
        <form action="{{ route('masters.products.import') }}" method="post" id="formImport" autocomplete="off" enctype="multipart/form-data">
            @csrf @method('post')

            <x-modal id="modalImport" title="Import Excel" fullScreen="md">
                <x-button
                    type="link"
                    href="{{ route('masters.products.import.template') }}"
                    color="secondary"
                    startIcon="download"
                    fullWidth
                >
                    Unduh Template
                </x-button>

                <hr class="my-6">

                <x-form-input
                    required
                    type="file"
                    name="file"
                    id="fileImport"
                    accept=".xlsx,.xls.csv"
                    error="{{ $errors->has('file') }}"
                />
            
                <x-slot:footer>
                    <div class="w-100">
                        <div class="row">
                            <div class="col-6">
                                <x-button type="button" color="light" startIcon="close" data-bs-dismiss="modal" fullWidth>
                                    Batal
                                </x-button>
                            </div>

                            <div class="col-6">
                                <x-button type="submit" startIcon="save" fullWidth>
                                    Simpan
                                </x-button>
                            </div>
                        </div>
                    </div>
                </x-slot:footer>
            </x-modal>
        </form>
    @endif

    @if ($access?->update ?? false)
        {{-- Import reset discount --}}
        <form action="{{ route('masters.products.discounts.reset') }}" method="post" id="formResetDiscounts" autocomplete="off">
            @csrf @method('patch')

            <x-modal id="modalResetDiscounts" title="Reset Diskon" fullScreen="md">
                <x-form-input
                    required
                    type="number"
                    name="discount"
                    id="discount"
                    placeholder="Masukan diskon..."
                    error="{{ $errors->has('discount') }}"
                    value="{{ old('discount') }}"
                />
            
                <x-slot:footer>
                    <div class="w-100">
                        <div class="row">
                            <div class="col-6">
                                <x-button type="button" color="light" startIcon="close" data-bs-dismiss="modal" fullWidth>
                                    Batal
                                </x-button>
                            </div>

                            <div class="col-6">
                                <x-button type="submit" startIcon="save" fullWidth>
                                    Simpan
                                </x-button>
                            </div>
                        </div>
                    </div>
                </x-slot:footer>
            </x-modal>
        </form>

        {{-- Import update discount --}}
        <form action="{{ route('masters.products.discounts.import') }}" method="post" id="formImportUpdateDiscounts" autocomplete="off" enctype="multipart/form-data">
            @csrf @method('patch')

            <x-modal id="modalImportUpdateDiscounts" title="Import Update Diskon" fullScreen="md">
                <x-button
                    type="link"
                    href="{{ route('masters.products.discounts.import.template') }}"
                    color="secondary"
                    startIcon="download"
                    fullWidth
                >
                    Unduh Template
                </x-button>

                <hr class="my-6">

                <x-form-input
                    required
                    type="file"
                    name="file"
                    id="fileImportUpdateDiscounts"
                    accept=".xlsx,.xls.csv"
                    error="{{ $errors->has('file') }}"
                />
            
                <x-slot:footer>
                    <div class="w-100">
                        <div class="row">
                            <div class="col-6">
                                <x-button type="button" color="light" startIcon="close" data-bs-dismiss="modal" fullWidth>
                                    Batal
                                </x-button>
                            </div>

                            <div class="col-6">
                                <x-button type="submit" startIcon="save" fullWidth>
                                    Simpan
                                </x-button>
                            </div>
                        </div>
                    </div>
                </x-slot:footer>
            </x-modal>
        </form>

        {{-- Import update harga --}}
        <form action="{{ route('masters.products.prices.import') }}" method="post" id="formImportUpdatePrices" autocomplete="off" enctype="multipart/form-data">
            @csrf @method('patch')

            <x-modal id="modalImportUpdatePrices" title="Import Update Harga" fullScreen="md">
                <x-button
                    type="link"
                    href="{{ route('masters.products.prices.import.template') }}"
                    color="secondary"
                    startIcon="download"
                    fullWidth
                >
                    Unduh Template
                </x-button>

                <hr class="my-6">

                <x-form-input
                    required
                    type="file"
                    name="file"
                    id="fileImportUpdatePrices"
                    accept=".xlsx,.xls.csv"
                    error="{{ $errors->has('file') }}"
                />
            
                <x-slot:footer>
                    <div class="w-100">
                        <div class="row">
                            <div class="col-6">
                                <x-button type="button" color="light" startIcon="close" data-bs-dismiss="modal" fullWidth>
                                    Batal
                                </x-button>
                            </div>

                            <div class="col-6">
                                <x-button type="submit" startIcon="save" fullWidth>
                                    Simpan
                                </x-button>
                            </div>
                        </div>
                    </div>
                </x-slot:footer>
            </x-modal>
        </form>
    @endif

    {{-- Modal daftar harga per-produk --}}
    <x-modal id="modalPrices" title="Harga" fullScreen="md" size="xl">
        <table id="pricesTable" class="table table-hover table-sm align-middle w-100"></table>
    </x-modal>

    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.access = this.#getAccess();
                    this.productTable = this.#productDataTableInit();
                    this.buttonImport = $('#buttonImport');
                    this.formImport = $('#formImport');
                    this.modalImport = this.#modalImportInit();
                    this.buttonResetDiscounts = $('#buttonResetDiscounts');
                    this.formResetDiscounts = $('#formResetDiscounts');
                    this.modalResetDiscounts = this.#modalResetDiscountsInit();
                    this.buttonImportUpdateDiscounts = $('#buttonImportUpdateDiscounts');
                    this.formImportUpdateDiscounts = $('#formImportUpdateDiscounts');
                    this.modalImportUpdateDiscounts = this.#modalImportUpdateDiscountsInit();
                    this.buttonImportUpdatePrices = $('#buttonImportUpdatePrices');
                    this.formImportUpdatePrices = $('#formImportUpdatePrices');
                    this.modalImportUpdatePrices = this.#modalImportUpdatePricesInit();
                    this.modalPrices = new bootstrap.Modal('#modalPrices');
                    this.pricesTable = null
                }

                /**
                 * Mengambil hak akses user.
                 */
                #getAccess() {
                    return {
                        read: $('html').data('read') === 1,
                        create: $('html').data('create') === 1,
                        update: $('html').data('update') === 1,
                        remove: $('html').data('remove') === 1,
                        delete: $('html').data('delete') === 1,
                    }
                }

                /**
                 * Inisialisasi product datatables.
                 */
                #productDataTableInit() {
                    const columns = [
                        {
                            title: 'Gambar',
                            name: 'image',
                            data: 'image',
                            className: 'text-nowrap',
                            type: 'html',
                            searchable: false,
                            orderable: false,
                            render: (data) => `<img src="${app.storageImage(data)}" alt="Product" class="rounded shadow-sm" />`,
                        },
                        {
                            title: 'Kode',
                            name: 'code',
                            data: 'code',
                            className: 'text-nowrap',
                            type: 'string',
                        },
                        {
                            title: 'Nama (Deskripsi)',
                            name: 'name',
                            data: 'name',
                            type: 'string',
                            width: '200px',
                        },
                        {
                            title: 'Kategori',
                            name: 'category.name',
                            data: 'category.name',
                            className: 'text-nowrap',
                            type: 'string',
                            render: (data, type, row) => `${data} (${row.category.code})`
                        },
                        {
                            title: 'Status',
                            name: 'is_active',
                            data: 'is_active',
                            className: 'text-nowrap',
                            type: 'string',
                            render: (data) => data ? 'Aktif' : 'Tidak Aktif',
                        },
                        {
                            title: 'Diskon (%)',
                            name: 'discount',
                            data: 'discount',
                            className: 'text-nowrap',
                            type: 'num',
                            render: (data) => app.numberFormat(data),
                        },
                        {
                            title: 'Minimum Stok',
                            name: 'minimum_stock',
                            data: 'minimum_stock',
                            className: 'text-nowrap',
                            type: 'num',
                            render: (data) => app.numberFormat(data),
                        },
                        {
                            title: 'Stok Tersedia',
                            name: 'current_stock',
                            data: 'current_stock',
                            className: 'text-nowrap',
                            type: 'num',
                            render: (data) => app.numberFormat(data),
                        },
                        {
                            title: 'Dibuat',
                            name: 'created_at',
                            data: 'created_at',
                            className: 'text-nowrap',
                            type: 'date',
                            render: (data) => app.dateTimeFormat(data),
                        },
                        {
                            title: 'Diedit',
                            name: 'updated_at',
                            data: 'updated_at',
                            className: 'text-nowrap',
                            type: 'date',
                            render: (data) => app.dateTimeFormat(data),
                        },
                        {
                            title: app.icon('settings', 'fs-6'),
                            name: 'id',
                            data: 'id',
                            className: 'text-nowrap text-center no-export',
                            type: 'html',
                            searchable: false,
                            orderable: false,
                            render: (data) => {
                                // Menu detail harga tersedia untuk semua user yang dapat membuka halaman ini.
                                let items = `
                                    <li>
                                        <a class="dropdown-item" href="#" data-action="detail" onclick="showPrices(event, '${data}')">
                                            Detail harga
                                        </a>
                                    </li>
                                `;

                                // Menu yang mengubah data hanya untuk user dengan akses update.
                                if (this.access.update) {
                                    items += `
                                        <li>
                                            <a class="dropdown-item" href="${app.baseUrl}/masters/products/${data}/prices/create">
                                                Edit harga
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="${app.baseUrl}/masters/products/${data}/edit">
                                                Edit produk
                                            </a>
                                        </li>
                                    `;
                                }

                                // Menu hapus hanya untuk user dengan akses remove.
                                if (this.access.remove) {
                                    items += `
                                        <li>
                                            <a class="dropdown-item text-danger" href="#" data-action="remove" onclick="removeProduct(event, '${data}')">
                                                Hapus produk
                                            </a>
                                        </li>
                                    `;
                                }

                                // Posisi menu "fixed" agar tidak terpotong oleh area tabel (kolom aksi tetap).
                                return `
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-light rounded" data-bs-toggle="dropdown" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" title="Aksi" aria-label="Aksi">
                                            ${app.icon('more_vert', 'fs-6')}
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border">${items}</ul>
                                    </div>
                                `;
                            },
                        }
                    ];

                    return app.dataTable('#productTable', {
                        columns,
                        serverSide: true,
                        ajax: "{{ route('masters.products') }}",
                        order: [[1, 'asc']],
                        fixedColumns: {
                            start: 2,
                            end: 0,
                        }
                    });
                }

                /**
                 * Inisialisasi modal import
                 */
                #modalImportInit() {
                    if ($('#modalImport').length === 0) {
                        return null;
                    }

                    return new bootstrap.Modal('#modalImport', {
                        backdrop: 'static',
                    });
                }

                /**
                 * Inisialisasi modal import
                 */
                #modalResetDiscountsInit() {
                    if ($('#modalResetDiscounts').length === 0) {
                        return null;
                    }

                    return new bootstrap.Modal('#modalResetDiscounts', {
                        backdrop: 'static',
                    });
                }

                /**
                 * Inisialisasi modal import update discounts
                 */
                #modalImportUpdateDiscountsInit() {
                    if ($('#modalImportUpdateDiscounts').length === 0) {
                        return null;
                    }

                    return new bootstrap.Modal('#modalImportUpdateDiscounts', {
                        backdrop: 'static',
                    });
                }

                /**
                 * Inisialisasi modal import update prices
                 */
                #modalImportUpdatePricesInit() {
                    if ($('#modalImportUpdatePrices').length === 0) {
                        return null;
                    }

                    return new bootstrap.Modal('#modalImportUpdatePrices', {
                        backdrop: 'static',
                    });
                }

                /**
                 * Import insert product
                 */
                importInsert() {
                    return new Promise((resolve, reject) => {
                        const formData = new FormData(document.querySelector('#formImport'));

                        $.ajax({
                            type: "post",
                            url: this.formImport.attr('action'),
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }

                /**
                 * Reset discounts
                 */
                resetDiscounts() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "patch",
                            url: this.formResetDiscounts.attr('action'),
                            data: this.formResetDiscounts.serialize(),
                            dataType: 'json',
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }

                /**
                 * Remove (soft delete)
                 */
                remove(id) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "delete",
                            url: app.url(`/masters/products/${id}`),
                            data: {
                                _token: app.csrfToken,
                                _method: 'delete',
                            },
                            dataType: 'json',
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }

                /**
                 * Import update discounts
                 */
                importUpdateDiscounts() {
                    const formData = new FormData(document.querySelector('#formImportUpdateDiscounts'));
                    
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "post",
                            url: this.formImportUpdateDiscounts.attr('action'),
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }

                /**
                 * Import update price
                 */
                importUpdatePrices() {
                    const formData = new FormData(document.querySelector('#formImportUpdatePrices'));
                    
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "post",
                            url: this.formImportUpdatePrices.attr('action'),
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }

                /**
                 * Inisialisasi dataTable harga.
                 */
                pricesDataTableInit(productId) {
                    return app.modalDataTable('#pricesTable', {
                        serverSide: true,
                        ajax: app.url(`/masters/products/${productId}/prices/table`),
                        scrollX: false,
                        columns: [
                            {
                                title: 'Unit (Satuan)',
                                name: 'units.name',
                                data: 'unit_name',
                                className: 'text-nowrap',
                                type: 'string',
                                render: (data, type, row) => `${row.unit_name} (${row.unit_code})`,
                            },
                            {
                                title: 'Harga Default',
                                name: 'prices.is_default',
                                data: 'is_default',
                                className: 'text-nowrap',
                                type: 'string',
                                render: (data, type, row) => data === 1 ? 'Ya' : 'Tidak',
                            },
                            {
                                title: 'Kelipatan Stok',
                                name: 'prices.multiples_quantity',
                                data: 'multiples_quantity',
                                className: 'text-nowrap',
                                type: 'num',
                                render: (data, type, row) => app.numberFormat(data, 2),
                            },
                            {
                                title: 'Harga Beli',
                                name: 'prices.purchase_price',
                                data: 'purchase_price',
                                className: 'text-nowrap',
                                type: 'num',
                                render: (data, type, row) => app.numberFormat(data, 2),
                            },
                            {
                                title: 'Harga Jual',
                                name: 'prices.selling_price',
                                data: 'selling_price',
                                className: 'text-nowrap',
                                type: 'num',
                                render: (data, type, row) => app.numberFormat(data, 2),
                            },
                        ],
                    })
                }

                /**
                 * Mengambil daftar harga berdasarkan produk
                 */
                getPrices(productId) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "get",
                            url: app.url(`/masters/products/${productId}/prices`),
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
             * Buka modal import
             */
            service.buttonImport.click(function (e) { 
                e.preventDefault();
                app.clearFormErrors(service.formImport);
                service.formImport.trigger('reset');
                service.modalImport?.show();
            });

            /**
             * Submit form import 
             */
            service.formImport.submit(async function (e) { 
                e.preventDefault();
                app.showPreloader();

                try {
                    const response = await service.importInsert();
                    const { message, status } = response;

                    if (status === 201) {
                        service.modalImport?.hide();
                        service.productTable.ajax.reload();
                        app.alert('success', message);
                    }
                } catch (error) {
                    app.setFormErrors(service.formImport, error);
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Buka modal reset discounts
             */
            service.buttonResetDiscounts.click(function (e) { 
                e.preventDefault();
                app.clearFormErrors(service.formResetDiscounts);
                service.formResetDiscounts.trigger('reset');
                service.modalResetDiscounts?.show();
            });

            /**
             * Submit form reset discounts 
             */
            service.formResetDiscounts.submit(async function (e) { 
                e.preventDefault();
                app.showPreloader();

                try {
                    const response = await service.resetDiscounts();
                    const { message, status } = response;

                    if (status === 200) {
                        service.modalResetDiscounts?.hide();
                        service.productTable.ajax.reload();
                        app.alert('success', message);
                    }
                } catch (error) {
                    app.setFormErrors(service.formResetDiscounts, error);
                } finally {
                    app.hidePreloader();
                }
            });
            
            /**
             * Remove (soft delete)
             */
            const removeProduct = (e, id) => {
                e.preventDefault();

                app.delete('Produk', async (removed) => {
                    if (!removed) return;

                    app.showPreloader();
                    
                    try {
                        const response = await service.remove(id);
                        const { status, message } = response;

                        if (status === 200) {
                            app.alert('success', message);
                            service.productTable.ajax.reload();
                        }
                    } catch (error) {
                        app.alert('danger', error.message);
                    } finally {
                        app.hidePreloader();
                    }
                });
            }

            /**
             * Buka modal import update discounts
             */
            service.buttonImportUpdateDiscounts.click(function (e) { 
                e.preventDefault();
                app.clearFormErrors(service.formImportUpdateDiscounts);
                service.formImportUpdateDiscounts.trigger('reset');
                service.modalImportUpdateDiscounts?.show();
            });

            /**
             * Submit form import update discount.
             */
            service.formImportUpdateDiscounts.submit(async function (e) { 
                e.preventDefault();
                app.showPreloader();
                app.clearFormErrors(service.formImportUpdateDiscounts);

                try {
                    const response = await service.importUpdateDiscounts();
                    const { status, message } = response;

                    if (status === 200) {
                        app.alert('success', message);
                        service.modalImportUpdateDiscounts?.hide();
                        service.productTable.ajax.reload();
                    }
                } catch (error) {
                    service.formImportUpdateDiscounts.find('input[name="file"]').removeClass('border');
                    service.formImportUpdateDiscounts.find('input[name="file"]').addClass('is-invalid');
                    service.formImportUpdateDiscounts.find('input[name="file"]').next().text(error.message);
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Buka modal import update Prices
             */
            service.buttonImportUpdatePrices.click(function (e) { 
                e.preventDefault();
                app.clearFormErrors(service.formImportUpdatePrices);
                service.formImportUpdatePrices.trigger('reset');
                service.modalImportUpdatePrices?.show();
            });

            /**
             * Submit form import update prices.
             */
            service.formImportUpdatePrices.submit(async function (e) { 
                e.preventDefault();
                app.showPreloader();
                app.clearFormErrors(service.formImportUpdatePrices);

                try {
                    const response = await service.importUpdatePrices();
                    const { status, message } = response;

                    if (status === 200) {
                        app.alert('success', message);
                        service.modalImportUpdatePrices?.hide();
                        service.productTable.ajax.reload();
                    }
                } catch (error) {
                    service.formImportUpdatePrices.find('input[name="file"]').removeClass('border');
                    service.formImportUpdatePrices.find('input[name="file"]').addClass('is-invalid');
                    service.formImportUpdatePrices.find('input[name="file"]').next().text(error.message);
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Tampilkan modal harga
             */
            const showPrices = async (e, productId) => {
                e.preventDefault();

                service.modalPrices.show();

                try {
                    if (service.pricesTable === null) {
                        service.pricesTable = service.pricesDataTableInit(productId);
                        return;
                    }

                    service.pricesTable.ajax.url(app.url(`/masters/products/${productId}/prices/table`)).load();
                } catch (error) {
                    app.alert('danger', error.message);
                }
            }
        </script>
    </x-slot:script>
</x-layouts.auth>