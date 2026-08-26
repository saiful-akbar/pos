<x-layouts.auth title="Users" :access="$access">
    <x-page-header title="Users" icon="people">
        @if($access?->create)
            <x-slot:action>
                <a role="button" href="{{ route('master.users.create') }}" class="btn btn-secondary w-100">
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
                    <table id="userTable" class="table table-hover align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        <script>
            const baseUrl = $('meta[name=base-url]').attr('content');
            const updateAccess = $('html').data('update');

            const tableColumns = [
                {
                    title: 'Nama',
                    name: 'users.full_name',
                    data: 'full_name',
                    className: 'text-nowrap',
                    render: (data, type, row) => {
                        return `
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <img class="avatar-img" src="${app.userPhoto(row.photo)}" alt="${row.full_name}" />
                                </div>
                                <div class="ms-4">
                                    <div>${row.full_name}</div>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    title: 'Role',
                    name: 'roles.name',
                    data: 'role_name',
                    className: 'text-nowrap',
                },
                {
                    title: 'Username',
                    name: 'users.username',
                    data: 'username',
                    className: 'text-nowrap',
                },
                {
                    title: 'Status',
                    name: 'users.deleted_at',
                    data: 'deleted_at',
                    className: 'text-nowrap',
                    searchable: false,
                    render: (data) => {
                        if (data === null) {
                            return `
                                <span class="badge bg-success-subtle text-success">Aktif</span>
                            `;
                        }

                        return `
                            <span class="badge bg-danger-subtle text-danger">Tidak Aktif</span>
                        `;
                    }
                },
                {
                    title: 'Dibuat',
                    name: 'users.created_at',
                    data: 'created_at',
                    className: 'text-nowrap',
                    searchable: false,
                    render: (data) => window.app.dateTimeFormat(data),
                },
                {
                    title: 'Diedit',
                    name: 'users.updated_at',
                    data: 'updated_at',
                    className: 'text-nowrap',
                    searchable: false,
                    render: (data) => window.app.dateTimeFormat(data),
                },
            ];

            if (updateAccess === 1) {
                tableColumns.push({
                    title: 'Aksi',
                    data: 'id',
                    className: 'text-nowrap text-center',
                    orderable: false,
                    searchable: false,
                    render: (data) => {
                        return `
                            <a role="button" href="${baseUrl}/master/users/${data}/edit" class="btn btn-sm btn-light">
                                <span class="material-symbols-outlined me-1">edit</span>
                                <span>Edit</span>
                            </a>
                        `;
                    }
                });
            }

            const userTable = window.app.dataTable('#userTable', {
                serverSide: true,
                ajax: "{{ route('master.users') }}",
                columns: tableColumns,
            });
        </script>
    </x-slot:script>
</x-layouts.auth>