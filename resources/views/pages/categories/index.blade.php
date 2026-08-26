<x-layouts.auth title="Produk Kategori" :access="$access">
    @if ($errors->any())
        <x-alert class="alert-danger border-danger mb-7">
            <ul class="m-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <x-page-header title="Produk Kategori" icon="data_table">
        @if ($access?->create)
            <x-slot:action>
                <div class="row">
                    <div class="col-6">
                        <button id="modalImportButton" class="btn btn-light w-100">
                            <x-icon class="me-1">upload</x-icon>
                            <span>Impor</span>
                        </button>
                    </div>

                    <div class="col-6">
                        <a href="{{ route('master.categories.create') }}" role="button" class="btn btn-secondary w-100">
                            <x-icon class="me-1">add</x-icon>
                            <span>Tambah</span>
                        </a>
                    </div>
                </div>
            </x-slot:action>
        @endif
    </x-page-header>

    <div class="row mt-7">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="categoryTable" class="table table-hover w-100"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal import --}}
    <form action="{{ route('master.categories.import') }}" method="post" id="formImport" enctype="multipart/form-data">
        @csrf @method('post')

        <div id="modalImport" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Impor Excel</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
    
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-7">
                            <a href="{{ route('master.categories.export.template') }}" class="btn btn-secondary w-100" role="button">
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
            const tableColumns = [
                {
                    title: 'Kode',
                    name: 'code',
                    data: 'code',
                    className: 'text-nowrap',
                },
                {
                    title: 'Nama (Deskripsi)',
                    name: 'name',
                    data: 'name',
                    className: 'text-nowrap',
                },
                {
                    title: 'Status',
                    name: 'is_active',
                    data: 'is_active',
                    className: 'text-nowrap',
                    searchable: false,
                    render: (data) => {
                        if (data) {
                            return `
                                <span class="badge bg-success-subtle text-success">
                                    Aktif
                                </span>
                            `;
                        }

                        return `
                            <span class="badge bg-danger-subtle text-danger">
                                Tidak Aktif
                            </span>
                        `;
                    }
                },
                {
                    title: 'Dibuat',
                    name: 'created_at',
                    data: 'created_at',
                    className: 'text-nowrap',
                    searchable: false,
                    render: (data) => app.dateTimeFormat(data),
                },
                {
                    title: 'Diedit',
                    name: 'updated_at',
                    data: 'updated_at',
                    className: 'text-nowrap',
                    searchable: false,
                    render: (data) => app.dateTimeFormat(data),
                },
            ];
            
            const updateAccess = $('html').data('update');
            
            if (updateAccess === 1) {
                tableColumns.push({
                    title: 'Aksi',
                    name: 'id',
                    data: 'id',
                    className: 'text-nowrap text-center',
                    searchable: false,
                    orderable: false,
                    render: (data) => {
                        return `
                            <a href="${app.baseUrl}/master/categories/${data}/edit" class="btn btn-sm btn-light">
                                <span class="material-symbols-outlined me-1">edit</span>
                                <span>Edit</span>    
                            </a>
                        `;
                    },
                });
            }

            const table = app.dataTable('#categoryTable', {
                serverSide: true,
                ajax: "{{ route('master.categories') }}",
                columns: tableColumns,
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