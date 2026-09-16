<x-layouts.auth title="Pemasok" :access="$access">
    <x-page-header title="Pemasok" icon="data_table">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-6 col-lg-5 col-xl-3">
                    <a href="{{ route('master.suppliers.export') }}" role="button" class="btn btn-light w-100">
                        <x-icon class="me-1">download</x-icon>
                        <span>Expor Excel</span>
                    </a>
                </div>
                    
                @if ($access?->create)
                    <div class="col-6 col-lg-5 col-xl-3">
                        <a href="{{ route('master.suppliers.create') }}" role="button" class="btn btn-secondary w-100">
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
                    <table id="supplierTable" class="table table-hover align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        <script>
            const updateAccess = $('html').data('update');
            const removeAccess = $('html').data('remove');
            
            const supplierTable = app.dataTable('#supplierTable', {
                serverSide: true,
                ajax: "{{ route('master.suppliers') }}",
                fixedColumns: {
                    start: 0,
                    end: 1
                },
                columnDefs: [
                    { width: '30%', targets: 3 },
                    { width: '30%', targets: 4 },
                ],
                columns: [
                    {
                        title: 'Kode',
                        name: 'code',
                        data: 'code',
                        type: 'string',
                        className: 'text-nowrap',
                    },
                    {
                        title: 'Nama',
                        name: 'name',
                        data: 'name',
                        type: 'string',
                        className: 'text-nowrap',
                    },
                    {
                        title: 'Nomor Telapon',
                        name: 'phone_number',
                        data: 'phone_number',
                        type: 'string',
                        className: 'text-nowrap',
                    },
                    {
                        title: 'Alamat',
                        name: 'address',
                        data: 'address',
                        type: 'string',
                    },
                    {
                        title: 'Catatan',
                        name: 'notes',
                        data: 'notes',
                        type: 'string',
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
                        type: 'string',
                        className: 'text-nowrap',
                        searchable: false,
                        render: (data) => app.dateTimeFormat(data),
                    },
                    {
                        title: 'Diedit',
                        name: 'updated_at',
                        data: 'updated_at',
                        type: 'string',
                        className: 'text-nowrap',
                        searchable: false,
                        render: (data) => app.dateTimeFormat(data),
                    },
                    {
                        title: 'Aksi',
                        name: 'id',
                        data: 'id',
                        className: 'text-nowrap text-center',
                        searchable: false,
                        orderable: false,
                        render: (data) => {
                            if (updateAccess === 1) {
                                return `
                                    <a href="${app.baseUrl}/master/suppliers/${data}/edit" class="btn btn-light" title="Edit">
                                        <span class="material-symbols-outlined">edit</span>
                                    </a>
                                `;
                            }

                            return '';
                        },
                    }
                ],
            });
        </script>
    </x-slot:script>
</x-layouts.auth>