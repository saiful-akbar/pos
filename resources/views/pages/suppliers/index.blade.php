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

    <div class="row mt-6">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="supplierTable" class="table table-sm table-hover align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

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
                    width: '250px',
                },
                {
                    title: 'Catatan',
                    name: 'notes',
                    data: 'notes',
                    type: 'string',
                    width: '250px',
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
            ];

            /**
             * Tambahkan kolom action jika user mmeiliki akses update
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
                                <a href="${app.baseUrl}/master/suppliers/${data}/edit" class="btn btn-light" title="Edit">
                                    <span class="material-symbols-outlined fs-6">edit</span>
                                </a>
                            `;
                        }

                        if (remove) {
                            html += `
                                <button type="button" class="btn btn-light" title="Hapus" onclick="removeSupplier(event, '${data}')">
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
            const supplierTable = app.dataTable('#supplierTable', {
                columns,
                serverSide: true,
                ajax: "{{ route('master.suppliers') }}",
                fixedColumns: {
                    start: 0,
                    end: access ? 1 : 0,
                },
            });

            /**
             * Fungsi untuk soft delete supplier
             */
            const removeSupplier = (e, id) => {
                e.preventDefault();

                app.delete('Pemasok', (removed) => {
                    if (!removed) return;

                    $.ajax({
                        type: "DELETE",
                        url: app.url(`/master/suppliers/${id}`),
                        data: {
                            _method: 'delete',
                            _token: app.csrfToken,
                        },
                        dataType: "json",
                        success: (response) => {
                            supplierTable.ajax.reload();
                            app.alert('success', response.message);
                        },
                        error: (error) => {
                            app.alert('danger', error.responseJSON.message);
                        }
                    });
                })
            }
        </script>
    </x-slot:script>
</x-layouts.auth>