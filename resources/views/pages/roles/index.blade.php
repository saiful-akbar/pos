<x-layouts.auth title="Role" :access="$access">
    <div class="row mb-7">
        <div class="col-12">
            <x-page-header title="Role" icon="data_table">
                @if ($access?->create)
                    <x-slot:action>
                        <a role="button" href="{{ route('master.roles.create') }}" class="btn btn-secondary w-100">
                            <x-icon class="me-1">add</x-icon>
                            <span>Tambah</span>
                        </a>
                    </x-slot:action>
                @endif
            </x-page-header>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="roleTable" class="table table-hover align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        <script>
            const baseUrl = $('meta[name=base-url]').attr('content');

            const tableColumns = [
                {
                    title: 'Nama',
                    name: 'roles.name',
                    data: 'name',
                    className: 'text-nowrap',
                },
                {
                    title: 'Deskripsi',
                    name: 'roles.description',
                    data: 'description',
                    className: 'text-nowrap',
                },
                {
                    title: 'Dibuat',
                    name: 'roles.created_at',
                    data: 'created_at',
                    className: 'text-nowrap',
                    searchable: false,
                    render: (data) => app.dateTimeFormat(data)
                },
                {
                    title: 'Diedit',
                    name: 'roles.updated_at',
                    data: 'updated_at',
                    className: 'text-nowrap',
                    searchable: false,
                    render: (data) => app.dateTimeFormat(data)
                },
            ];

            const updateAccess = $('html').data('update');

            if (updateAccess === 1) {
                tableColumns.push({
                    title: 'Aksi',
                    data: 'id',
                    className: 'text-center text-nowrap',
                    searchable: false,
                    orderlable: false,
                    render: (data) => {
                        return `
                            <a role="button" class="btn btn-sm btn-light" href="${baseUrl}/master/roles/${data}/edit">
                                <span class="material-symbols-outlined me-1">edit</span>
                                <span>Edit</span>
                            </a>
                        `;
                    }
                });
            }

            const table = app.dataTable('#roleTable', {
                serverSide: true,
                ajax: "{{ route('master.roles') }}",
                columns: tableColumns,
            });
        </script>
    </x-slot:script>
</x-layouts.auth>