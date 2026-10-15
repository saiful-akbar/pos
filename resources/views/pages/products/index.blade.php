<x-layouts.auth title="Produk" :access="$access">
    <x-page-header title="Produk" icon="data_table">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-4 order-2 order-lg-1">
                    <x-button type="link" hred="{{ route('masters.products.export') }}" startIcon="download" fullWidth>
                        Export Excel
                    </x-button>
                </div>
    
                @if ($access?->create ?? false)
                    <div class="col-12 col-lg-4 order-1 order-lg-2 mb-5 mb-lg-0">
                        <x-button type="link" href="{{ route('masters.products.create') }}" startIcon="add" fullWidth>
                            Tambah
                        </x-button>
                    </div>
                @endif
            </div>
        </x-slot:action>
    </x-page-header>

    <div class="row mt-7">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="productTable" class="table table-hover table-sm table-img align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.access = this.#getAccess();
                    this.productTable = this.#productDataTableInit();
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
                 * Inisialisasi product datatables.
                 */
                #productDataTableInit() {
                    const columns = [
                        {
                            title: 'Gambar',
                            name: 'image',
                            data: 'image',
                            className: 'text-nowrap',
                            type: 'html',
                            searchable: false,
                            orderable: false,
                            render: (data) => `<img src="${app.storageImage(data)}" alt="Product" class="rounded shadow-sm" />`,
                        },
                        {
                            title: 'Kode',
                            name: 'code',
                            data: 'code',
                            className: 'text-nowrap',
                            type: 'string',
                        },
                        {
                            title: 'Nama (Deskripsi)',
                            name: 'name',
                            data: 'name',
                            type: 'string',
                            width: '250px',
                        },
                        {
                            title: 'Kategori',
                            name: 'category.name',
                            data: 'category.name',
                            className: 'text-nowrap',
                            type: 'string',
                            render: (data, type, row) => `${data} (${row.category.code})`
                        },
                        {
                            title: 'Status',
                            name: 'is_active',
                            data: 'is_active',
                            className: 'text-nowrap',
                            type: 'string',
                            render: (data) => data ? 'Aktif' : 'Tidak Aktif',
                        },
                        {
                            title: 'Diskon (%)',
                            name: 'discount',
                            data: 'discount',
                            className: 'text-nowrap',
                            type: 'num',
                            render: (data) => app.numberFormat(data),
                        },
                        {
                            title: 'Minimum Stok',
                            name: 'minimum_stock',
                            data: 'minimum_stock',
                            className: 'text-nowrap',
                            type: 'num',
                            render: (data) => app.numberFormat(data),
                        },
                        {
                            title: 'Stok Tersedia',
                            name: 'current_stock',
                            data: 'current_stock',
                            className: 'text-nowrap',
                            type: 'num',
                            render: (data) => app.numberFormat(data),
                        },
                    ];

                    if (this.access.update || this.access.remove) {
                        columns.push({
                            title: app.icon('settings', 'fs-6'),
                            name: 'id',
                            data: 'id',
                            className: 'text-nowrap text-center no-export',
                            type: 'html',
                            searchable: false,
                            orderable: false,
                            render: (data) => {
                                let html = '';
                                    
                                if (this.access.update) {
                                    html += `
                                        <a
                                            href="${app.baseUrl}/masters/products/${data}/prices"
                                            class="btn btn-sm btn-light"
                                            title="Lihat atau edit harga"
                                            role="button"
                                        >
                                            ${app.icon('price_change', 'fs-6')}
                                        </a>

                                        <a
                                            href="${app.baseUrl}/masters/products/${data}/edit"
                                            class="btn btn-sm btn-light"
                                            title="Edit"
                                            role="button"
                                        >
                                            ${app.icon('edit', 'fs-6')}
                                        </a>
                                    `;
                                }
                                    
                                if (this.access.remove) {
                                    html += `
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-light"
                                            data-action="remove"
                                            title="Hapus"
                                        >
                                            ${app.icon('delete', 'fs-6')}
                                        </button>
                                    `;
                                }

                                return html;
                            },
                        });
                    }

                    return app.dataTable('#productTable', {
                        columns,
                        serverSide: true,
                        ajax: "{{ route('masters.products') }}",
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