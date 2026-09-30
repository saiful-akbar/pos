<x-layouts.auth title="Pelanggan" :access="$access">
    <x-page-header title="Pelanggan" icon="data_table">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-6 col-lg-5 col-xl-3">
                    <a href="{{ route('master.customers.export') }}" role="button" class="btn btn-light w-100">
                        <x-icon class="me-1">download</x-icon>
                        <span>Expor Excel</span>
                    </a>
                </div>
                    
                @if ($access?->create)
                    <div class="col-6 col-lg-5 col-xl-3">
                        <a href="{{ route('master.customers.create') }}" role="button" class="btn btn-secondary w-100">
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
                    <table id="customerTable" class="table table-sm table-hover align-middle w-100"></table>
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
             * Table columns
             */
            const columns = [
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
                    width: '300px',
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
             * /Tambahkan kolom action jika user memiliki akses update.
             */
            if (access) {
                columns.push({
                    title: '<span class="material-symbols-outlined">settings</span>',
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
                                <a href="${app.baseUrl}/master/customers/${data}/edit" class="btn btn-light">
                                    <span class="material-symbols-outlined fs-6">edit</span>
                                </a>
                            `;
                        }

                        if (remove) {
                            html += `
                                <button type="button" class="btn btn-light" title="Hapus" onclick="removeCustomer(event, '${data}')">
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
            const customerTable = app.dataTable('#customerTable', {
                columns,
                serverSide: true,
                ajax: "{{ route('master.customers') }}",
                fixedColumns: {
                    start: 0,
                    end: access ? 1 : 0,
                },
            });

            /**
             * Fungsi soft delete customer.
             */
            const removeCustomer = (e, id) => {
                e.preventDefault();

                app.delete('Pelanggan', (removed) => {
                    if (!removed) return;

                    app.showPreloader();

                   $.ajax({
                    type: "delete",
                    url: app.url(`/master/customers/${id}`),
                    data: {
                        _method: 'delete',
                        _token: app.csrfToken,
                    },
                    dataType: "json",
                    success: (response) => {
                        customerTable.ajax.reload();
                        app.alert('success', response.message);
                    },
                    error: (error) => {
                        app.alert('danger', error.responseJSON.message);
                    },
                    complete: () => {
                        app.hidePreloader();
                    }
                   }); 
                });
            }
        </script>
    </x-slot:script>
</x-layouts.auth>