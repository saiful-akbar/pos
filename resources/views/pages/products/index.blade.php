<x-layouts.auth title="Produk" :access="$access">
    @if ($errors->any())
        <x-alert class="alert-danger border-danger mb-7">
            <ul class="m-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <x-page-header title="Produk" icon="data_table">
        <x-slot:action>
            <div class="row justify-content-lg-end">
                <div class="col-12 col-lg-8 mb-4 mb-lg-0">
                    <div class="btn-group w-100">
                        <a href="{{ route('master.products.export') }}" class="btn btn-light w-100" role="button">
                            <x-icon class="me-1">Download</x-icon>
                            <span>Expor Excel</span>
                        </a>
                        
                        @if ($access?->create)
                            <button id="modalImportButton" class="btn btn-light w-100">
                                <x-icon class="me-1">upload</x-icon>
                                <span>Impor Excel</span>
                            </button>
                        @endif
                    </div>
                </div>
                        
                @if ($access?->create)
                    <div class="col-12 col-lg-4">
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
                    <table id="unitTable" class="table table-hover align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal import --}}
    <form action="{{ route('master.products.import') }}" method="post" id="formImport" enctype="multipart/form-data">
        @csrf @method('post')

        <div id="modalImport" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
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
                            <input type="file" name="file" accept=".xlsx" class="form-control form-control-lg" required>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">
                        <x-icon class="me-1">close</x-icon>
                        <span>Tutup</span>
                    </button>
    
                    <button type="submit" class="btn btn-secondary">
                        <x-icon class="me-1">upload</x-icon>
                        <span>Impor</span>
                    </button>
                </div>
                </div>
            </div>
        </div>
    </form>

    <x-slot:script>
        <script>
            const updateAccess = $('html').data('update');

            const table = app.dataTable('#unitTable', {
                serverSide: true,
                ajax: "{{ route('master.products') }}",
                fixedColumns: {
                    start: 1,
                    end: 1
                },
                columns: [
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
                        className: 'text-nowrap',
                        type: 'string',
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
                        title: 'Harga Beli',
                        name: 'purchase_price',
                        data: 'purchase_price',
                        className: 'text-nowrap',
                        render: (data) => app.numberFormat(data),
                    },
                    {
                        title: 'Harga Jual',
                        name: 'selling_price',
                        data: 'selling_price',
                        className: 'text-nowrap',
                        render: (data) => app.numberFormat(data),
                    },
                    {
                        title: 'Diskon',
                        name: 'discount',
                        data: 'discount',
                        className: 'text-nowrap',
                        render: (data) => `${data}%`,
                    },
                    {
                        title: 'Stok Tersedia',
                        name: 'current_stock',
                        data: 'current_stock',
                        className: 'text-nowrap',
                        render: (data, type, row) => {
                            const { current_stock: currentStock, minimum_stock: minimumStock } = row;
                            const color = currentStock < minimumStock ? 'danger' : 'success';

                            return `<span class="text-${color} fw-bold">${data}</span>`
                        }
                    },
                    {
                        title: 'Minimal Stok',
                        name: 'minimum_stock',
                        data: 'minimum_stock',
                        className: 'text-nowrap',
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
                        title: 'Aksi',
                        name: 'products.id',
                        data: 'id',
                        className: 'text-nowrap text-center',
                        searchable: false,
                        orderable: false,
                        render: (data) => {
                            if (updateAccess === 1) {
                                return `
                                    <a href="${app.baseUrl}/master/products/${data}/edit" class="btn btn-sm btn-light">
                                        <span class="material-symbols-outlined me-1">edit</span>
                                        <span>Edit</span>    
                                    </a>
                                `;
                            }
                        },
                    }
                ],
            });
        </script>

        {{-- Modal import --}}
        <script>
            const modalImport = new bootstrap.Modal('#modalImport', {
                backdrop: 'static',
                keyboard: false,
            });

            $('#modalImportButton').click(function (e) { 
                e.preventDefault();
                modalImport.show();
            });
        </script>
    </x-slot:script>
</x-layouts.auth>