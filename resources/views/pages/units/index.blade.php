<x-layouts.auth title="Unit Produk" :access="$access">
    <x-page-header title="Unit Produk" icon="data_table">
        @if ($access?->create)
            <x-slot:action>
                <div class="row justify-content-end">
                    <div class="col-6 col-lg-6">
                        <div class="btn-group w-100">
                            <a href="{{ route('master.units.export') }}" class="btn btn-secondary w-100" role="button">
                                <x-icon class="me-1">Download</x-icon>
                                <span>Expor Excel</span>
                            </a>

                            <button id="modalImportButton" class="btn btn-secondary w-100">
                                <x-icon class="me-1">upload</x-icon>
                                <span>Impor Excel</span>
                            </button>
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <a href="{{ route('master.units.create') }}" role="button" class="btn btn-secondary w-100">
                            <x-icon class="me-1">add</x-icon>
                            <span>Tambah</span>
                        </a>
                    </div>
                </div>
            </x-slot:action>
        @endif
    </x-page-header>

    <div class="row mt-6">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="unitTable" class="table table-sm table-hover align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal import --}}
    <form action="{{ route('master.units.import') }}" method="post" id="formImport" enctype="multipart/form-data">
        @csrf @method('post')

        <div id="modalImport" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content border">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Impor Excel</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
        
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-7">
                                <a href="{{ route('master.units.export.template') }}" class="btn btn-secondary w-100" role="button">
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
            const update = $('html').data('update');
            const remove = $('html').data('remove');
            const access = update === 1 || remove === 1;

            /**
             * Datatable columns
             */
            const columns = [
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
                        if (data) return `<span class="badge bg-success-subtle text-success">Aktif</span>`;
                        return `<span class="badge bg-danger-subtle text-danger">Tidak Aktif</span>`;
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

            /**
             * Tambahkan kolom action jika user memiliki akses update
             */
            if (access) {
                columns.push({
                    title: `<span class="material-symbols-outlined">settings</span>`,
                    name: 'id',
                    data: 'id',
                    className: 'text-nowrap text-center',
                    searchable: false,
                    orderable: false,
                    type: 'html',
                    render: (data) => {
                        let html = '';

                        if (update) {
                            html += `
                                <a href="${app.baseUrl}/master/units/${data}/edit" class="btn btn-light" title="edit">
                                    <span class="material-symbols-outlined fs-6">edit</span>
                                </a>
                            `;
                        }

                        if (remove) {
                            html += `
                                <button type="button" class="btn btn-light" title="Hapus" onclick="removeUnit(event, '${data}')">
                                    <span class="material-symbols-outlined fs-6">delete</span>
                                </button>
                            `;
                        }

                        return html;
                    },
                });
            }

            /**
             * Inisialisasi datatable.
             */
            const unitTable = app.dataTable('#unitTable', {
                columns,
                serverSide: true,
                ajax: "{{ route('master.units') }}",
                fixedColumns: {
                    start: 0,
                    end: access ? 1 : 0,
                },
            });

            /**
             * Fungsi soft delete unit.
             */
            const removeUnit = (e, id) => {
                e.preventDefault();

                app.delete('Unit Produk', (removed) => {
                    if (!removed) return;

                    $.ajax({
                        type: "delete",
                        url: app.url(`/master/units/${id}`),
                        data: {
                            _method: 'delete',
                            _token: app.csrfToken,
                        },
                        dataType: "json",
                        success: (response) => {
                            unitTable.ajax.reload();
                            app.alert('success', response.message);
                        },
                        error: (error) => {
                            app.alert('danger', error.responseJSON.message);
                        }
                    }); 
                });
            }
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