<x-layouts.auth title="Unit (Satuan produk)" :access="$access">
    <x-page-header title="Unit (Satuan produk)" icon="data_table">
        <x-slot:action>
            <div class="row justify-content-end">
                <div
                    @class([
                        'order-2',
                        'order-lg-1',
                        'col-12',
                        'col-lg-8' => ($access?->create ?? false) === true,
                        'col-lg-4' => ($access?->create ?? false) === false
                    ])
                >
                    <div class="btn-group w-100">
                        <x-button
                            type="link"
                            href="{{ route('masters.units.export') }}"
                            color="secondary"
                            startIcon="download"
                            fullWidth
                        >
                            Export Excel
                        </x-button>
                        
                        @if ($access?->create ?? false)
                            <x-button
                                type="button"
                                color="secondary"
                                startIcon="upload"
                                id="buttonImport"
                                fullWidth
                            >
                                Import Excel
                            </x-button>
                        @endif
                    </div>
                </div>

                @if ($access?->create ?? false)
                    <div class="col-12 col-lg-4 mb-5 mb-lg-0 order-1 order-lg-2">
                        <x-button type="button" color="secondary" startIcon="add" id="buttonCreate" fullWidth>
                            Tambah
                        </x-button>
                    </div>
                @endif
            </div>
        </x-slot:action>
    </x-page-header>

    {{-- Table --}}
    <div class="row mt-7 gx-7">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="unitTable" class="table table-hover table-sm align-middle w-100"></table>
                </div>
            </div>
        </div>
    </div>

    {{-- Form modal --}}
    @if (($access?->create ?? false) || ($access?->update ?? false))
        <form action="#" method="post" autocomplete="off" id="formUpsert">
            @csrf @method('post')

            <x-modal title="Tambah Unit (Satuan)" id="modalUpsert">
                <div class="mb-5">
                    <x-form-input
                        required
                        type="text"
                        name="code"
                        id="code"
                        label="Kode"
                        placeholder="Masukan kode..."
                        value="{{ old('code') }}"
                        error="{{ $errors->has('code') }}"
                    />
                </div>

                <div class="mb-5">
                    <x-form-input
                        required
                        type="text"
                        name="name"
                        id="name"
                        label="Nama"
                        placeholder="Masukan nama atau deskripsi..."
                        value="{{ old('name') }}"
                        error="{{ $errors->has('name') }}"
                    />
                </div>

                <div>
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

                <x-slot:footer>
                    <div class="row">
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
                </x-slot:footer>
            </x-modal>
        </form>
    @endif

    {{-- Form import --}}
    @if ($access?->create ?? false)
        <form action="{{ route('masters.units.import') }}" method="post" autocomplete="off" id="formImport" enctype="multipart/form-data">
            @csrf

            <x-modal id="modalImport" title="Import Excel" fullScreen="sm">
                <x-button
                    type="link"
                    href="{{ route('masters.units.import.template') }}"
                    startIcon="download"
                    color="secondary"
                    fullWidth
                >
                    Unduh Template
                </x-button>

                <hr class="my-5">

                <x-form-input
                    required
                    type="file"
                    name="file"
                    id="file"
                    error="{{ $errors->has('file') }}"
                />

                <x-slot:footer>
                    <div class="row justify-content-between">
                        <div class="col-6">
                            <x-button type="button" color="light" data-bs-dismiss="modal" startIcon="close" fullWidth>
                                Batal
                            </x-button>
                        </div>

                        <div class="col-6">
                            <x-button type="submit" color="secondary" startIcon="Save" fullWidth>
                                Simpan
                            </x-button>
                        </div>
                    </div>
                </x-slot:footer>
            </x-modal>
        </form>
    @endif

    {{-- Javascript --}}
    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.access = this.#getAccess();
                    this.buttonImport = $('#buttonImport');
                    this.formImport = $('#formImport');
                    this.modalImport = this.#modalImportInit();
                    this.buttonCreate = $('#buttonCreate');
                    this.formUpsert = $('#formUpsert');
                    this.modalUpsert = this.#modalUpsertInit();
                    this.unitTable = this.#unitDataTableInit();

                }

                /**
                 * Get user access
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
                 * Inisialisasi modal import
                 */
                #modalImportInit() {
                    if ($('#modalImport').length === 0) {
                        return null;
                    }

                    return new bootstrap.Modal('#modalImport', {
                        backdrop: 'static',
                    });
                }

                /**
                 * Inisialisasi modal update atau insert
                 */
                #modalUpsertInit() {
                    if ($('#modalUpsert').length === 0) {
                        return null;
                    }

                    return new bootstrap.Modal('#modalUpsert', {
                        backdrop: 'static',
                    });
                }

                /**
                 * Inisialisasi datatable
                 */
                #unitDataTableInit() {
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
                            title: 'Status',
                            name: 'is_active',
                            data: 'is_active',
                            type: 'string',
                            className: 'text-nowrap',
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
                                            onclick="removeUnit(event, '${data}')"
                                        >
                                            ${app.icon('delete', 'fs-6')}
                                        </button>
                                    `;
                                }

                                return html;
                            },
                        });
                    }

                    return app.dataTable('#unitTable', {
                        columns,
                        serverSide: true,
                        ajax: "{{ route('masters.units') }}",
                        fixedColumns: {
                            start: 0,
                            end: this.access.update || this.access.remove ? 1 : 0,
                        },
                        createdRow: (row, data, index) => {
                            $(row).find('button[data-action="edit"]').click((e) => { 
                                e.preventDefault();

                                if (this.modalUpsert === null) {
                                    return;
                                }

                                app.clearFormErrors(this.formUpsert);

                                this.formUpsert.attr('action', app.url(`/masters/units/${data.id}`));
                                this.formUpsert.find('.modal-title').text('Edit Kategori');
                                this.formUpsert.find('input[name="_method"]').val('patch');
                                this.formUpsert.find('input[name="code"]').val(data.code);
                                this.formUpsert.find('input[name="name"]').val(data.name);
                                this.formUpsert.find('select[name="is_active"]').val(data.is_active);
                                this.modalUpsert.show();
                            });
                        }
                    });
                }

                /**
                 * Submit form modal 
                 */
                submitFormUpsert() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: this.formUpsert.find('input[name="_method"]').val(),
                            url: this.formUpsert.attr('action'),
                            data: this.formUpsert.serialize(),
                            dataType: "json",
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    })
                }

                /**
                 * Remove (soft delete)
                 */
                remove(id) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "delete",
                            url: app.url(`/masters/units/${id}`),
                            data: {
                                _token: app.csrfToken,
                                _method: 'delete'
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
             * Buka modal form create
             */
            service.buttonCreate.click(function (e) { 
                e.preventDefault();
                
                if (service.formUpsert === null) {
                    return;
                }
                
                app.clearFormErrors(service.formUpsert);
                service.formUpsert.attr('action', "{{ route('masters.units.store') }}");
                service.formUpsert.find('input[name="_method"]').val('post');
                service.formUpsert.find('.modal-title').text('Tambah Kategori');
                service.formUpsert.trigger('reset');
                service.modalUpsert.show();
            });

            /**
             * Submit modal form update atau insert
             */
            service.formUpsert.submit(async function (e) { 
                e.preventDefault();

                app.showPreloader();
                app.clearFormErrors(service.formUpsert);

                try {
                    const response = await service.submitFormUpsert();
                    const { message, status } = response;

                    if (status === 200 || status === 201) {
                        app.alert('success', message);
                        service.unitTable.ajax.reload();
                        service.modalUpsert.hide();
                    }
                } catch (error) {
                    app.setFormErrors(service.formUpsert, error);
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Fungsi remove (soft delete) 
             */
            const removeUnit = (event, id) => {
                event.preventDefault();

                app.delete('Unit (Satuan produk)', async (removed) => {
                    if (!removed) {
                        return;
                    }
                    
                    try {
                        const response = await service.remove(id);
                        const { status, message } = response;

                        if (status === 200) {
                            app.alert('success', message);
                            service.unitTable.ajax.reload();
                        }
                    } catch (error) {
                        app.alert('danger', error.message);
                    }
                })
            }

            /**
             * Buka modal import excel
             */
            service.buttonImport.click(function (e) { 
                e.preventDefault();
                
                if (service.formImport === null) {
                    return null;
                }

                service.modalImport.show();
            });

            /**
             * Submit form import excel
             */
            service.formImport.submit(function (e) { 
                app.showPreloader();
            });
        </script>
    </x-slot:script>
</x-layouts.auth>