<x-layouts.auth title="Role" :access="$access">
    <div class="row mb-7">
        <div class="col-12">
            <x-page-header title="Role" icon="data_table">
                @if ($access?->create)
                    <x-slot:action>
                        <div class="row justify-content-end">
                            <div class="col-12 col-lg-5 col-xl-3">
                                <a role="button" href="{{ route('master.roles.create') }}" class="btn btn-secondary w-100">
                                    <x-icon class="me-1">add</x-icon>
                                    <span>Tambah</span>
                                </a>
                            </div>
                        </div>
                    </x-slot:action>
                @endif
            </x-page-header>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="roleTable" class="table table-sm table-hover align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        <script>
            const updateAccess = $('html').data('update');

            /**
             * Datatable columns.
             */
            const columns = [
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

            /**
             * Tambahkan kolom action jika user memiliki akses update.
             */
            if (updateAccess) {
                columns.push({
                    title: '<span class="material-symbols-outlined">settings</span>',
                    data: 'id',
                    className: 'text-center text-nowrap',
                    searchable: false,
                    orderable: false,
                    render: (data) => {
                        return `
                            <a role="button" class="btn btn-light" href="${app.baseUrl}/master/roles/${data}/edit" title="Edit">
                                <span class="material-symbols-outlined fs-6">edit</span>
                            </a>
                        `;
                    }
                });
            }

            const table = app.dataTable('#roleTable', {
                columns,
                serverSide: true,
                ajax: "{{ route('master.roles') }}",
                fixedColumns: {
                    start: 0,
                    end: updateAccess ? 1 : 0,
                }
            });
        </script>
    </x-slot:script>
</x-layouts.auth>