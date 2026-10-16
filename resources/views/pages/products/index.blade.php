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
                    id="file"
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

    {{-- Import reset discount --}}
    @if ($access?->update ?? false)
        <form action="{{ route('masters.products.discount.reset') }}" method="post" id="formResetDiscounts" autocomplete="off">
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
    @endif

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
                    ];

                    if (this.access.update || this.access.remove) {
                        columns.push({
                            title: app.icon('settings', 'fs-6'),
                            name: 'id',
                            data: 'id',
                            className: 'text-nowrap text-center no-export',
                            type: 'html',
                            searchable: false,
                            orderable: false,
                            render: (data) => {
                                let html = '';
                                    
                                if (this.access.update) {
                                    html += `
                                        <a
                                            href="${app.baseUrl}/masters/products/${data}/prices"
                                            class="btn btn-sm btn-light"
                                            title="Lihat atau edit harga"
                                            role="button"
                                        >
                                            ${app.icon('price_change', 'fs-6')}
                                        </a>

                                        <a
                                            href="${app.baseUrl}/masters/products/${data}/edit"
                                            class="btn btn-sm btn-light"
                                            title="Edit"
                                            role="button"
                                        >
                                            ${app.icon('edit', 'fs-6')}
                                        </a>
                                    `;
                                }
                                    
                                if (this.access.remove) {
                                    html += `
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-light"
                                            data-action="remove"
                                            title="Hapus"
                                        >
                                            ${app.icon('delete', 'fs-6')}
                                        </button>
                                    `;
                                }

                                return html;
                            },
                        });
                    }

                    return app.dataTable('#productTable', {
                        columns,
                        serverSide: true,
                        ajax: "{{ route('masters.products') }}",
                        order: [[1, 'asc']],
                        fixedColumns: {
                            start: 0,
                            end: this.access.update || this.access.remove ? 1 : 0,
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
                    })
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
                    console.log(error);
                    
                    app.setFormErrors(service.formResetDiscounts, error);
                } finally {
                    app.hidePreloader();
                }
            });
        </script>
    </x-slot:script>
</x-layouts.auth>