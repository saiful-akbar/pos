<x-layouts.auth title="Pemasok" :access="$access">
    <x-page-header title="Pemasok" icon="data_table">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-4 order-2 order-lg-1">
                    <x-button type="link" href="{{ route('masters.suppliers.export') }}" startIcon="download" fullWidth>
                        Export Excel
                    </x-button>
                </div>

                @if ($access?->create)
                    <div class="col-12 col-lg-4 order-1 order-lg-2 mb-5 mb-lg-0">
                        <x-button startIcon="add" id="buttonCreate" fullWidth>Tambah</x-button>
                    </div>
                @endif
            </div>
        </x-slot:action>
    </x-page-header>

    <div class="row mt-7">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="tableSupplier" class="table table-hover table-sm align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    @if ($access?->create || $access?->update)
        <form action="#" method="post" autocomplete="off" id="form" data-action="create">
            @csrf @method('post')

            <div id="modalForm" class="modal" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-fullscreen-md-down modal-md modal-dialog-scrollable">
                    <div class="modal-content border">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Pemasok</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-5">
                                <x-form-input
                                    required
                                    type="text"
                                    name="name"
                                    id="name"
                                    label="Nama"
                                    placeholder="Masukan nama pemasok..."
                                    error="{{ $errors->has('name') }}"
                                    value="{{ old('name') }}"
                                />
                            </div>

                            <div class="mb-5">
                                <x-form-input
                                    type="text"
                                    name="phone_number"
                                    id="phone_number"
                                    label="Nomor telepon"
                                    placeholder="Masukan nomor telepon..."
                                    error="{{ $errors->has('phone_number') }}"
                                    value="{{ old('phone_number') }}"
                                />
                            </div>

                            <div class="mb-5">
                                <x-form-select
                                    name="is_active"
                                    id="is_active"
                                    label="Status"
                                    error="{{ $errors->has('is_active') }}"
                                    value="{{ old('is_active') }}"
                                >
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </x-form-select>
                            </div>

                            <div>
                                <x-form-input
                                    type="textarea"
                                    name="address"
                                    id="address"
                                    label="Alamat"
                                    placeholder="Masukan alamat..."
                                    error="{{ $errors->has('address') }}"
                                    value="{{ old('address') }}"
                                    rows="5"
                                />
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="w-100">
                                <div class="row justify-content-between">
                                    <div class="col-6">
                                        <x-button type="button" color="light" startIcon="close" data-bs-dismiss="modal" fullWidth>
                                            Batal
                                        </x-button>
                                    </div>

                                    <div class="col-6">
                                        <x-button type="submit" color="secondary" startIcon="save" fullWidth>
                                            Simpan
                                        </x-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif

    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.access = this.#getAccess();
                    this.form = $('#form');
                    this.modalForm = this.#modalFormInit();
                    this.buttonCreate = $('#buttonCreate');
                    this.tableSupplier = this.#dataTableSupplier();
                }

                /**
                 * Mengambil akses
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
                 * Modal form
                 */
                #modalFormInit() {
                    const modalFormEl = $('#modalForm');

                    if (modalFormEl.length === 0) {
                        return null;
                    }

                    return new bootstrap.Modal('#modalForm', {
                        backdrop: 'static',
                    });
                }

                /**
                 * Inisialisasi datatable
                 */
                #dataTableSupplier() {
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
                            render: (data) => data ? 'Aktif' : 'Tidak aktif',
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
                                            onclick="removeSupplier(event, '${data}')"
                                        >
                                            ${app.icon('delete', 'fs-6')}
                                        </button>
                                    `;
                                }

                                return html;
                            },
                        });
                    }

                    return app.dataTable('#tableSupplier', {
                        columns,
                        serverSide: true,
                        ajax: "{{ route('masters.suppliers') }}",
                        fixedColumns: {
                            start: 0,
                            end: this.access.update || this.access.remove ? 1 : 0,
                        },
                        createdRow: (row, data, index) => {
                            $(row).find('button[data-action="edit"]').click((e) => { 
                                e.preventDefault();
                                
                                if (this.modalForm === null) {
                                    return;
                                }

                                app.clearFormErrors(service.form);

                                this.form.attr('action', app.url(`/masters/suppliers/${data.id}`));
                                this.form.find('.modal-title').text('Edit Pemasok');
                                this.form.find('[name="_method"]').val('put');
                                this.form.find('[name="name"]').val(data.name);
                                this.form.find('[name="phone_number"]').val(data.phone_number);
                                this.form.find('[name="is_active"]').val(data.is_active);
                                this.form.find('[name="address"]').val(data.address);
                                this.modalForm.show();
                            });
                        }
                    });
                }

                /**
                 * Tambah atau update data supplier
                 */
                submitForm() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: this.form.find('input[name="_method"]').val(),
                            url: this.form.attr('action'),
                            data: this.form.serialize(),
                            dataType: "json",
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }

                /**
                 * Remove supplier
                 */
                remove(id) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "delete",
                            url: app.url(`/masters/suppliers/${id}`),
                            data: {
                                _token: app.csrfToken,
                                _method: 'delete',
                            },
                            dataType: "json",
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    })
                }
            }

            window.service = new Service();
        </script>

        {{-- Global script --}}
        <script>
            "use strict"

            /**
             * Buka modal form untuk create
             */
            service.buttonCreate.click(function (e) { 
                e.preventDefault();
                
                if (service.modalForm === null) {
                    return;
                }

                app.clearFormErrors(service.form);
                service.form.attr('action', "{{ route('masters.suppliers.store') }}");
                service.form.data('action', 'create');
                service.form.trigger('reset');
                service.form.find('.modal-title').text('Tambah Pemasok');
                service.form.find('[name="_method"]').val('post');
                service.modalForm.show();
            });

            /**
             * Submit form 
             */
            service.form.submit(async function (e) { 
                e.preventDefault();
                app.clearFormErrors(service.form);
                app.showPreloader();
                
                try {
                    const response = await service.submitForm();
                    const { status, message } = response;
                    
                    if (status === 200 || status === 201) {
                        app.alert('success', message);
                        service.tableSupplier.ajax.reload();
                        service.modalForm.hide();
                    }
                } catch (error) {
                    app.setFormErrors(service.form, error);
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Remove supplier
             */
            const removeSupplier = (event, id) => {
                event.preventDefault();

                app.delete('Pemasok', async (removed) => {
                    if (!removed) return;

                    app.showPreloader();

                    try {
                        const response = await service.remove(id);
                        const { status, message } = response;

                        if (status === 200) {
                            app.alert('success', message);
                            service.tableSupplier.ajax.reload();
                        }
                    } catch (error) {
                        app.alert('danger', error.message);
                    } finally {
                        app.hidePreloader();
                    }
                })
            }
        </script>
    </x-slot:script>
</x-layouts.auth>