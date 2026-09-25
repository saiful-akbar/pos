<x-layouts.auth title="Produk" :access="$access">
    <x-page-header title="Produk" icon="data_table">
        <x-slot:action>
            <div class="row justify-content-lg-end">
                <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                    <div class="btn-group w-100">
                        <a href="{{ route('master.products.export') }}" class="btn btn-light w-100" role="button">
                            <x-icon class="me-1">Download</x-icon>
                            <span>Expor Excel</span>
                        </a>
                        
                        @if ($access?->create)
                            <button id="buttonImport" class="btn btn-light w-100">
                                <x-icon class="me-1">upload</x-icon>
                                <span>Impor Excel</span>
                            </button>
                        @endif
                    </div>
                </div>
                
                @if ($access?->update)
                    <div class="col-12 col-lg-3 mb-4 mb-lg-0">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <x-icon>upload</x-icon>
                                <span>Update</span>
                            </button>

                            <ul class="dropdown-menu border">
                                <li><a class="dropdown-item" href="#" id="buttonResetDiscount">Reset diskon</a></li>
                                <li><a class="dropdown-item" href="#" id="buttonImportUpdateDiscount">Impor update diskon</a></li>
                                <li><a class="dropdown-item" href="#" id="buttonImportUpdateSellingPrice">Import update harga jual</a></li>
                            </ul>
                        </div>
                    </div>
                @endif
                        
                @if ($access?->create)
                    <div class="col-12 col-lg-3">
                        <a href="{{ route('master.products.create') }}" role="button" class="btn btn-secondary w-100">
                            <x-icon class="me-1">add</x-icon>
                            <span>Tambah</span>
                        </a>
                    </div>
                @endif
            </div>
        </x-slot:action>
    </x-page-header>

    <div class="row mt-7">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="productTable" class="table table-sm table-hover table-img align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal import --}}
    <form action="{{ route('master.products.import') }}" method="post" id="formImport" enctype="multipart/form-data">
        @csrf @method('post')

        <div id="modalImport" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content border">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalImportLabel">Impor Excel</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
        
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-7">
                                <a href="{{ route('master.products.export.template') }}" class="btn btn-secondary w-100" role="button">
                                    <x-icon class="me-1">download</x-icon>
                                    <span>Unduh Template</span>
                                </a>
                            </div>

                            <div class="col-12">
                                <input type="file" name="file" accept=".xlsx" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <div class="row w-100">
                            <div class="col-6">
                                <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">
                                    <x-icon class="me-1">close</x-icon>
                                    <span>Batal</span>
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <x-icon class="me-1">upload</x-icon>
                                    <span>Impor</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Modal reset discount --}}
    <form action="{{ route('master.products.resetDiscount') }}" method="post" id="formResetDiscount" enctype="multipart/form-data">
        @csrf @method('patch')

        <div id="modalResetDiscount" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content border">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalResetDiscountLabel">Reset Diskon</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
        
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <x-form-input
                                    required
                                    name="discount"
                                    id="discount"
                                    placeholder="Masukan diskon"
                                    type="number"
                                    min="0"
                                    max="100"
                                    class="no-spinners"
                                    size="lg"
                                />
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <div class="row w-100">
                            <div class="col-6">
                                <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">
                                    <x-icon class="me-1">close</x-icon>
                                    <span>Batal</span>
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <x-icon class="me-1">save</x-icon>
                                    <span>Simpan</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Modal import update discount --}}
    <form action="{{ route('master.products.import.discount') }}" method="post" id="formImportUpdateDiscount" enctype="multipart/form-data">
        @csrf @method('patch')

        <div id="modalImportUpdateDiscount" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content border">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalImportUpdateDiscountLabel">Update Diskon</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
        
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-7">
                                <a href="{{ route('master.products.export.template.discount') }}" class="btn btn-secondary w-100" role="button">
                                    <x-icon class="me-1">download</x-icon>
                                    <span>Unduh Template</span>
                                </a>
                            </div>

                            <div class="col-12">
                                <input type="file" name="file" accept=".xlsx" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <div class="row w-100">
                            <div class="col-6">
                                <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">
                                    <x-icon class="me-1">close</x-icon>
                                    <span>Batal</span>
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <x-icon class="me-1">upload</x-icon>
                                    <span>Impor</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Modal import update selling price --}}
    <form action="{{ route('master.products.import.sellingPrice') }}" method="post" id="formImportUpdateSellingPrice" enctype="multipart/form-data">
        @csrf @method('patch')

        <div id="modalImportUpdateSellingPrice" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content border">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="modalImportUpdateSellingPriceLabel">Update Harga Jual</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
        
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-7">
                                <a href="{{ route('master.products.export.template.sellingPrice') }}" class="btn btn-secondary w-100" role="button">
                                    <x-icon class="me-1">download</x-icon>
                                    <span>Unduh Template</span>
                                </a>
                            </div>

                            <div class="col-12">
                                <input type="file" name="file" accept=".xlsx" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <div class="row w-100">
                            <div class="col-6">
                                <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">
                                    <x-icon class="me-1">close</x-icon>
                                    <span>Batal</span>
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <x-icon class="me-1">upload</x-icon>
                                    <span>Impor</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <x-slot:script>
        <script>
            const updateAccess = $('html').data('update');
            const baseUrl = app.baseUrl;

            const productTable = app.dataTable('#productTable', {
                serverSide: true,
                ajax: "{{ route('master.products') }}",
                fixedColumns: {
                    start: 2,
                    end: 1
                },
                columns: [
                    {
                        title: 'Gambar',
                        name: 'products.image',
                        data: 'image',
                        type: 'string',
                        searchable: false,
                        orderable: false,
                        render: (data, type, row) => {
                            return `<img src="${app.storageImage(data)}" alt="${row.code}" class="rounded" />`;
                        }
                    },
                    {
                        title: 'Kode Produk',
                        name: 'products.code',
                        data: 'code',
                        className: 'text-nowrap',
                        type: 'string',
                    },
                    {
                        title: 'Barcode',
                        name: 'products.barcode',
                        data: 'barcode',
                        className: 'text-nowrap',
                        type: 'string',
                    },
                    {
                        title: 'Nama (Deskripsi)',
                        name: 'products.name',
                        data: 'name',
                        type: 'string',
                        width: '300px',
                    },
                    {
                        title: 'Kode Kategori',
                        name: 'category.code',
                        data: 'category.code',
                        className: 'text-nowrap',
                        type: 'string',
                    },
                    {
                        title: 'Nama Kategori',
                        name: 'category.name',
                        data: 'category.name',
                        className: 'text-nowrap',
                        type: 'string',
                    },
                    {
                        title: 'Kode Unit',
                        name: 'unit.code',
                        data: 'unit.code',
                        className: 'text-nowrap',
                        type: 'string',
                    },
                    {
                        title: 'Nama Unit',
                        name: 'unit.name',
                        data: 'unit.name',
                        className: 'text-nowrap',
                        type: 'string',
                    },
                    {
                        title: 'Stok Tersedia',
                        name: 'current_stock',
                        data: 'current_stock',
                        className: 'text-nowrap',
                        type: 'html-num',
                        render: (data, type, row) => {
                            return app.numberFormat(data)
                        }
                    },
                    {
                        title: 'Minimum Stok',
                        name: 'minimum_stock',
                        data: 'minimum_stock',
                        className: 'text-nowrap',
                        render: (data, type, row) => {
                            return app.numberFormat(data)
                        }
                    },
                    {
                        title: 'Harga Beli',
                        name: 'purchase_price',
                        data: 'purchase_price',
                        className: 'text-nowrap',
                        type: 'num',
                        render: (data) => `Rp ${app.numberFormat(data)}`,
                    },
                    {
                        title: 'Harga Jual',
                        name: 'selling_price',
                        data: 'selling_price',
                        className: 'text-nowrap',
                        type: 'num',
                        render: (data) => `Rp ${app.numberFormat(data)}`,
                    },
                    {
                        title: 'Diskon',
                        name: 'discount',
                        data: 'discount',
                        className: 'text-nowrap',
                        type: 'num',
                        render: (data) => `${data}%`,
                    },
                    {
                        title: 'Status',
                        name: 'products.is_active',
                        data: 'is_active',
                        className: 'text-nowrap',
                        searchable: false,
                        render: (data) => {
                            if (data) return `<span class="badge bg-success-subtle text-success">Aktif</span>`;
                            return `<span class="badge bg-danger-subtle text-danger">Tidak Aktif</span>`;
                        }
                    },
                    {
                        title: 'Dibuat',
                        name: 'products.created_at',
                        data: 'created_at',
                        className: 'text-nowrap',
                        searchable: false,
                        render: (data) => app.dateTimeFormat(data),
                    },
                    {
                        title: 'Diedit',
                        name: 'products.updated_at',
                        data: 'updated_at',
                        className: 'text-nowrap',
                        searchable: false,
                        render: (data) => app.dateTimeFormat(data),
                    },
                    {
                        title: `<span class="material-symbols-outlined">settings</span>`,
                        name: 'products.id',
                        data: 'id',
                        className: 'text-nowrap text-center',
                        type: 'html',
                        searchable: false,
                        orderable: false,
                        render: (data) => {
                            if (updateAccess === 1) {
                                return `
                                    <a href="${app.baseUrl}/master/products/${data}/edit" class="btn btn-light" title="Edit">
                                        <span class="material-symbols-outlined fs-6">edit</span>
                                    </a>
                                `;
                            }

                            return null;
                        },
                    }
                ],
            });
        </script>

        {{-- Modal import --}}
        <script>
            const modalImport = new bootstrap.Modal('#modalImport');
            const modalResetDiscount = new bootstrap.Modal('#modalResetDiscount');
            const modalImportUpdateDiscount = new bootstrap.Modal('#modalImportUpdateDiscount');
            const modalImportUpdateSellingPrice = new bootstrap.Modal('#modalImportUpdateSellingPrice');
            
            /**
             * Buka modal import excel.
             */
            $('#buttonImport').click(function (e) { 
                e.preventDefault();
                modalImport.show();
            });
            
            /**
             * Buka modal reset diskon
             */
            $('#buttonResetDiscount').click(function (e) { 
                e.preventDefault();
                modalResetDiscount.show();
            });
            
            /**
             * Buka modal update discount
             */
            $('#buttonImportUpdateDiscount').click(function (e) { 
                e.preventDefault();
                modalImportUpdateDiscount.show();
            });
            
            /**
             * Buka modal harga jual
             */
            $('#buttonImportUpdateSellingPrice').click(function (e) { 
                e.preventDefault();
                modalImportUpdateSellingPrice.show();
            });
        </script>

        {{-- GLobal script --}}
        <script>
            $('#formImport').submit(function (e) { 
                app.showPreloader();
            });

            $('#formResetDiscount').submit(function (e) { 
                app.showPreloader();
            });

            $('#formImportUpdateDiscount').submit(function (e) { 
                app.showPreloader();
            });

            $('#formImportUpdateSellingPrice').submit(function (e) { 
                app.showPreloader();
            });
        </script>
    </x-slot:script>
</x-layouts.auth>