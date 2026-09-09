<x-layouts.auth title="Diskon" :access="$access">
    <x-page-header title="Diskon" icon="data_table">
        <x-slot:action>
            <div class="row justify-content-end align-items-center">
                @if ($access?->create)
                    <div class="col-12 col-lg-6 col-xl-3">
                        <a href="{{ route('master.discounts.create') }}" class="btn btn-secondary w-100" role="button">
                            <x-icon class="me-1">add</x-icon>
                            <span>Tambah Diskon</span>
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
                    <table id="discountTable" class="table table-hover align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- Form remove --}}
    <form action="#" method="post" class="d-none" id="formRemove">
        @csrf @method('delete');
    </form>

    <x-slot:script>
        <script>
            class Discount {
                constructor() {
                    this.permission = {
                        read: $('html').data('read') !== '' ? true : false,
                        create: $('html').data('create') !== '' ? true : false,
                        update: $('html').data('update') !== '' ? true : false,
                        remove: $('html').data('remove') !== '' ? true : false,
                        delete: $('html').data('delete') !== '' ? true : false,
                    }
                }

                /**
                 * Table options
                 */
                #tableOptions() {
                    return {
                        serverSide: true,
                        ajax: "{{ route('master.discounts') }}",
                        fixedColumns: {
                            start: 0,
                            end: 1,
                        },
                        columns: [
                            {
                                title: 'Diskon',
                                name: 'discount',
                                data: 'discount',
                                className: 'text-nowrap',
                                type: 'string',
                                render: (data) => `${data}%`
                            },
                            {
                                title: 'Nama (Deskripsi)',
                                name: 'name',
                                data: 'name',
                                className: 'text-nowrap',
                                type: 'string',
                            },
                            {
                                title: 'Tanggal Efektif',
                                name: 'effective_date',
                                data: 'effective_date',
                                className: 'text-nowrap',
                                render: (data) => app.dateFormat(data),
                            },
                            {
                                title: 'Tanggal Kadaluarsa',
                                name: 'expiration_date',
                                data: 'expiration_date',
                                className: 'text-nowrap',
                                render: (data) => data === null ? '' : app.dateFormat(data),
                            },
                            {
                                title: 'Waktu Dibuat',
                                name: 'created_at',
                                data: 'created_at',
                                className: 'text-nowrap',
                                render: (data) => app.dateTimeFormat(data),
                            },
                            {
                                title: 'Waktu Diperbarui',
                                name: 'updated_at',
                                data: 'updated_at',
                                className: 'text-nowrap',
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
                                    let html = `<div class="btn-group w-100">`;

                                    if (this.permission.update) {
                                        html += `
                                            <a href="${app.baseUrl}/master/discounts/${data}/edit" class="btn btn-light" title="Edit">
                                                <span class="material-symbols-outlined">edit</span>
                                            </a>
                                        `;
                                    }

                                    if (this.permission.remove) {
                                        html += `
                                            <button type="button" class="btn btn-light" onclick="remove(event, '${data}')">
                                                <span class="material-symbols-outlined">delete</span>
                                            </button>
                                        `;
                                    }

                                    html += `</div>`

                                    return html;
                                }
                            }
                        ],
                    }
                }

                /**
                 * Datatable
                 */
                table() {
                    return app.dataTable('#discountTable', this.#tableOptions());
                }

                /**
                 * Remove discount.
                 */
                remove(id) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "delete",
                            url: `${app.baseUrl}/master/discounts/${id}`,
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                            success: function (response) {
                                resolve(response);
                            },
                            error: function (error) {
                                resolve(error.responseJSON);
                            },
                        });
                    })
                }
            }

            window.discount = new Discount();
        </script>

        <script>
            const discountTable = discount.table();

            /**
             * Hapus discount.
             */
            function remove(e, id) {
                e.preventDefault();

                app.delete('Diskon', async (result) => {
                    if (!result) {
                        return;
                    }

                    try {
                        const response = await discount.remove(id);
                        
                        discountTable.ajax.reload();
                        app.alert('success', response.message);
                    } catch (error) {
                        app.alert('danger', error.message);
                    }
                })
            }
        </script>
    </x-slot:script>
</x-layouts.auth>