<x-layouts.auth title="Produk Kategori" :access="$access">
    <x-page-header title="Produk Kategori" icon="data_table">
        @if ($access?->create)
            <x-slot:action>
                <a href="{{ route('master.categories.create') }}" role="button" class="btn btn-secondary w-100">
                    <x-icon class="me-1">add</x-icon>
                    <span>Tambah</span>
                </a>
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

            const table = app.dataTable('#categoryTable', {
                serverSide: true,
                ajax: "{{ route('master.categories') }}",
                columns: tableColumns,
            });
        </script>
    </x-slot:script>
</x-layouts.auth>