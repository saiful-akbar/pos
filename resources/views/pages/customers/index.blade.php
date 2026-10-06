<x-layouts.auth title="Pelanggan (Member)" :access="$access">
    <x-page-header title="Pelanggan (Member)" icon="data_table">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-4">
                    <x-button type="link" href="{{ route('masters.customers.export') }}" startIcon="download" fullWidth>
                        Export Excel
                    </x-button>
                </div>
                
                @if ($access?->create)
                    <div class="col-12 col-lg-4 mt-5 mt-lg-0">
                        <x-button type="button" id="buttonCreate" startIcon="add" fullWidth>
                            Tambah
                        </x-button>
                    </div>
                @endif
            </div>
        </x-slot:action>
    </x-page-header>

    {{-- Table --}}
    <div class="row mt-7">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="tableCustomer" class="table table-hover table-sm align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- Javascript --}}
    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.access = this.#getAccess();
                    this.tableCustomer = this.#dataTableCustomerInit();
                }

                /**
                 * Mengambil hak akses user.
                 */
                #getAccess() {
                    return {
                        read: $('html').data('read') === 1,
                        create: $('html').data('create') === 1,
                        update: $('html').data('update') === 1,
                        remove: $('html').data('remove') === 1,
                        delete: $('html').data('delete') === 1,
                    }
                }

                /**
                 * Inisialisasi datatable
                 */
                #dataTableCustomerInit() {
                    const columns = [
                        {
                            title: 'Nama',
                            name: 'name',
                            data: 'name',
                            className: 'text-nowrap',
                            type: 'string',
                        },
                        {
                            title: 'Telepon',
                            name: 'phone_number',
                            data: 'phone_number',
                            className: 'text-nowrap',
                            type: 'string',
                        },
                        {
                            title: 'Alamat',
                            name: 'address',
                            data: 'address',
                            type: 'string',
                            width: '250px',
                        },
                        {
                            title: 'Status',
                            name: 'is_active',
                            data: 'is_active',
                            className: 'text-nowrap',
                            type: 'string',
                            render: (data) => data ? 'Aktif' : 'Tidak aktif'
                        },
                        {
                            title: 'Diskon (%)',
                            name: 'discount',
                            data: 'discount',
                            className: 'text-nowrap',
                            type: 'num',
                        },
                        {
                            title: 'Dibuat',
                            name: 'created_at',
                            data: 'created_at',
                            className: 'text-nowrap',
                            type: 'date',
                            render: (data) => app.dateTimeFormat(data),
                        },
                        {
                            title: 'Diedit',
                            name: 'updated_at',
                            data: 'updated_at',
                            className: 'text-nowrap',
                            type: 'date',
                            render: (data) => app.dateTimeFormat(data),
                        },
                    ];

                    console.log(this.access.update || this.access.remove);
                    

                    if (this.access.update || this.access.remove) {
                        columns.push({
                            title: app.icon('settings', 'fs-6'),
                            name: 'id',
                            data: 'id',
                            className: 'text-nowrap text-center',
                            type: 'html',
                            orderable: false,
                            searchable: false,
                            render: (data) => {
                                let html = '';

                                if (this.access.update) {
                                    html  += `
                                        <button
                                            type="button"
                                            class="btn btn-light"
                                            title="Edit"
                                            data-action="edit"
                                        >
                                            ${app.icon('edit', 'fs-6')}
                                        </button>
                                    `;
                                }

                                if (this.access.remove) {
                                    html  += `
                                        <button
                                            type="button"
                                            class="btn btn-light"
                                            title="Hapus"
                                            data-action="delete"
                                            onclick="removeCustomer(event, '${data}')"
                                        >
                                            ${app.icon('delete', 'fs-6')}
                                        </button>
                                    `;
                                }

                                return html;
                            },
                        });
                    }

                    return app.dataTable('#tableCustomer', {
                        columns,
                        serverSide: true,
                        ajax: "{{ route('masters.customers') }}",
                        fixedColumns: {
                            start: 0,
                            end: this.access.update || this.access.remove ? 1 : 0,
                        }
                    });
                }
            }

            window.service = new Service();
        </script>
    </x-slot:script>
</x-layouts.auth>