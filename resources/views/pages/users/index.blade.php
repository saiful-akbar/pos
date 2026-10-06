<x-layouts.auth title="Users" :access="$access">
    <x-page-header title="Users" icon="people">
        @if($access?->create)
            <x-slot:action>
                <div class="row justify-content-end">
                    <div class="col-12 col-lg-5 col-xl-3">
                        <a role="button" href="{{ route('masters.users.create') }}" class="btn btn-secondary w-100">
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
                    <table id="userTable" class="table table-sm table-hover align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        <script>
            const updateAccess = $('html').data('update');
            const removeAccess = $('html').data('remove');
            const access = (updateAccess || removeAccess) ? true : false;
            

            /**
             * Datatable columns
             */
            const columns = [
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
                                <div class="ms-4">${row.full_name}</div>
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
                    name: 'users.is_active',
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

            /**
             * Tambahkan kolom action jika user memiliki akses update
             */
            if (access) {
                columns.push({
                    title: '<span class="material-symbols-outlined">settings</span>',
                    data: 'id',
                    className: 'text-nowrap text-center',
                    orderable: false,
                    searchable: false,
                    type: 'html',
                    render: (data) => {
                        let html = '';

                        if (updateAccess) {
                            html += `
                                <a href="${app.baseUrl}/masters/users/${data}/edit" class="btn btn-light" role="button" title="Edit">
                                    <span class="material-symbols-outlined fs-6">edit</span>
                                </a>
                            `;
                        }

                        if (removeAccess) {
                            html += `
                                <button type="button" class="btn btn-light" title="Hapus" onclick="removeUser(event, '${data}')">
                                    <span class="material-symbols-outlined fs-6">delete</span>
                                </button>
                            `;
                        }

                        return html;
                    }
                });
            }

            /**
             * Inisialisasi datatable.
             */
            const userTable = window.app.dataTable('#userTable', {
                columns,
                serverSide: true,
                ajax: "{{ route('masters.users') }}",
                fixedColumns: {
                    start: 0,
                    end: access ? 1 : 0,
                },
            });

            /**
             * Fungsi untuk menghapus user
             */
            const removeUser = (e, id) => {
                e.preventDefault();

                app.delete('User', (removed) => {
                    if (!removed) return;

                    $.ajax({
                        type: "delete",
                        url: app.url(`/masters/users/${id}`),
                        data: {
                            _method: 'delete',
                            _token: app.csrfToken,
                        },
                        dataType: "json",
                        success: (response) => {
                            userTable.ajax.reload();
                            app.alert('success', response.message);
                        },
                        error: (error) => {
                            app.alert('danger', error.responseJSON.message);
                        },
                    }); 
                });
            }
        </script>
    </x-slot:script>
</x-layouts.auth>