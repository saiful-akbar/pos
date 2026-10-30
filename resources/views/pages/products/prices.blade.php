<x-layouts.auth title="Harga Produk" :access="$access">
    <x-page-header title="Harga Produk" icon="price_change">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-4">
                    <x-button
                        type="link"
                        href="{{ route('masters.products') }}"
                        color="light"
                        startIcon="arrow_back"
                        fullWidth
                    >
                        Kembali
                    </x-button>
                </div>
            </div>
        </x-slot:action>
    </x-page-header>

    <div class="row mt-7 gx-7">
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm position-sticky" style="top: 20px;">
                <div class="card-img-top" style="height: 250px; background: no-repeat url('{{ storage_image($product->image) }}') center center / cover"></div>

                <div class="card-body text-center">
                    <h1 class="card-title fs-5">{{ $product->code }}</h1>
                    <p class="text-body-secondary mb-6">{{ $product->name }}</p>

                    <ul class="list-group list-group-flush mb-0">
                        <li class="list-group-item d-flex align-items-center justify-content-between bg-body px-0">
                            <span class="text-body-secondary me-10">Kategori</span>
                            <span class="text-end">{{ $product->category->name }} ({{ $product->category->code }})</span>
                        </li>
                        
                        <li class="list-group-item d-flex align-items-center justify-content-between bg-body px-0">
                            <span class="text-body-secondary me-10">Stok</span>
                            <span class="text-end">{{ number_format($product->currentStock, 2) }}</span>
                        </li>

                        <li class="list-group-item d-flex align-items-center justify-content-between bg-body px-0">
                            <span class="text-body-secondary me-10">Minimum stock</span>
                            <span class="text-end">{{ number_format($product->minimumStock, 2) }}</span>
                        </li>

                        <li class="list-group-item d-flex align-items-center justify-content-between bg-body px-0">
                            <span class="text-body-secondary me-10">Status</span>
                            <span class="text-end">{{ $product->isActive ? 'Aktif' : 'Tidak aktif' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-8 mt-7 mt-lg-0">
            <div class="row gx-7">
                <div class="col-12">
                    <form action="{{ route('masters.products.prices.store', ['productId' => $product->id]) }}" method="post" autocomplete="off" id="formCreate">
                        @csrf
        
                        <div class="card">
                            <div class="card-header">
                                <h2 class="fs-5 mb-0">Tambah Harga</h2>
                            </div>
        
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-6 mb-5">
                                        <x-form-select
                                            required
                                            name="unit_id"
                                            id="unit_id"
                                            label="Unit (Satuan)"
                                            value="{{ old('unit_id') }}"
                                            error="{{ $errors->has('unit_id') }}"
                                        >
                                            <option value="" selected disabled>Pilih</option>
        
                                            @foreach ($units as $unit)
                                                <option value="{{ $unit->id }}" @selected($unit->id == old('unit_id'))>
                                                    {{ $unit->name }} ({{ $unit->code }})
                                                </option>
                                            @endforeach
                                        </x-form-select>
                                    </div>
        
                                    <div class="col-12 col-lg-6 mb-5">
                                        <x-form-input
                                            required
                                            type="number"
                                            name="multiples_quantity"
                                            id="multiples_quantity"
                                            label="Kelipatan Stok"
                                            placeholder="Masukan nilai kelipatan stok..."
                                            value="{{ old('multiples_quantity') }}"
                                            error="{{ $errors->has('multiples_quantity') }}"
                                            min="1.00"
                                            step="0.01"
                                        />
                                    </div>
        
                                    <div class="col-12 col-lg-6 mb-5">
                                        <x-form-input
                                            required
                                            type="number"
                                            name="purchase_price"
                                            id="purchase_price"
                                            label="Harga Beli"
                                            placeholder="Masukan harga beli..."
                                            value="{{ old('purchase_price') }}"
                                            error="{{ $errors->has('purchase_price') }}"
                                            min="1.00"
                                            step="0.01"
                                        />
                                    </div>
        
                                    <div class="col-12 col-lg-6 mb-5">
                                        <x-form-input
                                            required
                                            type="number"
                                            name="selling_price"
                                            id="selling_price"
                                            label="Harga Jual"
                                            placeholder="Masukan harga jual..."
                                            value="{{ old('selling_price') }}"
                                            error="{{ $errors->has('selling_price') }}"
                                            min="1.00"
                                            step="0.01"
                                        />
                                    </div>
        
                                    <div class="col-12 col-lg-6">
                                        <x-form-select
                                            required
                                            name="is_default"
                                            id="is_default"
                                            label="Harga Default"
                                            value="{{ old('is_default') }}"
                                            error="{{ $errors->has('is_default') }}"
                                        >
                                            <option value="0">Tidak</option>
                                            <option value="1">Ya</option>
                                        </x-form-select>
                                    </div>
                                </div>
                            </div>
        
                            <div class="card-footer">
                                <div class="row justify-content-end">
                                    <div class="col-12 col-lg-4 col-xl-3">
                                        <x-button type="submit" color="secondary" startIcon="add" fullWidth>Tambah</x-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-12 mt-7">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <table id="priceTable" class="table table-hover table-sm align-middle w-100"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal edit harga --}}
    <form action="" method="post" autocomplete="off" id="formEdit">
        @csrf @method('patch')

        <x-modal title="Edit Harga" id="modalEdit" fullScreen="sm">
            <div class="mb-5">
                <x-form-input
                    disabled
                    type="text"
                    id="edit_unit"
                    name="unit_name"
                    label="Unit (Satuan)"
                />
            </div>

            <div class="mb-5">
                <x-form-select
                    required
                    name="is_default"
                    id="edit_is_default"
                    label="Harga Default"
                >
                    <option value="0">Tidak</option>
                    <option value="1">Ya</option>
                </x-form-select>
            </div>

            <div class="mb-5">
                <x-form-input
                    required
                    type="number"
                    name="purchase_price"
                    id="edit_purchase_price"
                    label="Harga Beli"
                    placeholder="Masukan harga beli..."
                    min="0"
                    step="0.01"
                />
            </div>

            <div class="mb-5">
                <x-form-input
                    required
                    type="number"
                    name="selling_price"
                    id="edit_selling_price"
                    label="Harga Jual"
                    placeholder="Masukan harga jual..."
                    min="0"
                    step="0.01"
                />
            </div>

            <div>
                <x-form-input
                    required
                    type="number"
                    name="multiples_quantity"
                    id="edit_multiples_quantity"
                    label="Kelipatan Stok"
                    placeholder="Masukan nilai kelipatan stok..."
                    helperText="Perubahan kelipatan tidak mengubah stok dan riwayat transaksi, tetapi berlaku untuk keranjang yang masih terbuka dan import stok berikutnya."
                    min="1"
                    step="0.01"
                />
            </div>

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

    {{-- Javascript --}}
    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.productId = "{{ $product->id }}",
                    this.formCreate = $('#formCreate');
                    this.formEdit = $('#formEdit');
                    this.modalEdit = new bootstrap.Modal('#modalEdit', { backdrop: 'static' });
                    this.priceTable = this.#priceDataTableInit();
                }

                /**
                 * Inisialisasi dataTable pride
                 */
                #priceDataTableInit() {
                    return app.dataTable('#priceTable', {
                        serverSide: true,
                        ajax: "{{ route('masters.products.prices.table', ['productId' => $product->id]) }}",
                        fixedColumns: {
                            start: 0,
                            end: 1,
                        },
                        columns: [
                            {
                                title: 'Unit (Satuan)',
                                name: 'units.name',
                                data: 'unit_name',
                                className: 'text-nowrap',
                                type: 'string',
                                render: (data, type, row) => `${row.unit_name} (${row.unit_code})`,
                            },
                            {
                                title: 'Harga Default',
                                name: 'prices.is_default',
                                data: 'is_default',
                                className: 'text-nowrap',
                                type: 'string',
                                render: (data, type, row) => data === 1 ? 'Ya' : 'Tidak',
                            },
                            {
                                title: 'Kelipatan Stok',
                                name: 'prices.multiples_quantity',
                                data: 'multiples_quantity',
                                className: 'text-nowrap',
                                type: 'num',
                                render: (data, type, row) => app.numberFormat(data, 2),
                            },
                            {
                                title: 'Harga Beli',
                                name: 'prices.purchase_price',
                                data: 'purchase_price',
                                className: 'text-nowrap',
                                type: 'num',
                                render: (data, type, row) => app.numberFormat(data, 2),
                            },
                            {
                                title: 'Harga Jual',
                                name: 'prices.selling_price',
                                data: 'selling_price',
                                className: 'text-nowrap',
                                type: 'num',
                                render: (data, type, row) => app.numberFormat(data, 2),
                            },
                            {
                                title: app.icon('settings', 'fs-6'),
                                name: 'prices.id',
                                data: 'id',
                                className: 'text-nowrap text-center',
                                type: 'html',
                                orderable: false,
                                searchable: false,
                                render: (data, type, row) => {
                                    const canUpdate = $('html').data('update') === 1;

                                    return `
                                        ${canUpdate ? `
                                            <button type="button" class="btn btn-light" title="Edit" onclick="editPrice(event, '${data}')">
                                                ${app.icon('edit', 'fs-6')}
                                            </button>
                                        ` : ''}
                                        <button type="button" class="btn btn-light" title="Hapus" onclick="deletePrice(event, '${data}')">
                                            ${app.icon('delete', 'fs-6')}
                                        </button>
                                    `;
                                },
                            },
                        ],
                    });
                }

                /**
                 * Submit form create
                 */
                create() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "post",
                            url: this.formCreate.attr('action'),
                            data: this.formCreate.serialize(),
                            dataType: "json",
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }

                /**
                 * Submit form edit
                 */
                update() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "post",
                            url: this.formEdit.attr('action'),
                            data: this.formEdit.serialize(),
                            dataType: "json",
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }

                /**
                 * Hapus harga
                 */
                delete(id) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "delete",
                            url: app.url(`/masters/products/${this.productId}/prices/${id}`),
                            data: {
                                _token: app.csrfToken,
                                _method: 'delete'
                            },
                            dataType: "json",
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }
            }

            window.service = new Service();
        </script>

        <script>
            "use strict"

            /**
             * Submit form create
             */
            service.formCreate.submit(async function (e) { 
                e.preventDefault();
                
                app.showPreloader();
                app.clearFormErrors(service.formCreate);

                try {
                    const response = await service.create();
                    const { status, message } = response;

                    if (status === 201) {
                        app.alert('success', message);
                        service.formCreate.trigger('reset');
                        service.priceTable.ajax.reload();
                    }
                } catch (error) {
                    app.setFormErrors(service.formCreate, error);
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Tampilkan modal edit harga
             */
            const editPrice = (event, id) => {
                event.preventDefault();

                // Ambil data baris dari dataTable berdasarkan id harga.
                const row = service.priceTable.rows().data().toArray().find((item) => item.id === id);

                if (row === undefined) {
                    return;
                }

                app.clearFormErrors(service.formEdit);

                service.formEdit.attr('action', app.url(`/masters/products/${service.productId}/prices/${id}`));
                service.formEdit.find('#edit_unit').val(`${row.unit_name} (${row.unit_code})`);
                service.formEdit.find('#edit_is_default').val(row.is_default === 1 ? '1' : '0');
                service.formEdit.find('#edit_purchase_price').val(row.purchase_price);
                service.formEdit.find('#edit_selling_price').val(row.selling_price);
                service.formEdit.find('#edit_multiples_quantity').val(row.multiples_quantity);
                service.modalEdit.show();
            }

            /**
             * Submit form edit
             */
            service.formEdit.submit(async function (e) {
                e.preventDefault();

                app.showPreloader();
                app.clearFormErrors(service.formEdit);

                try {
                    const response = await service.update();
                    const { status, message } = response;

                    if (status === 200) {
                        app.alert('success', message);
                        service.modalEdit.hide();
                        service.priceTable.ajax.reload();
                    }
                } catch (error) {
                    app.setFormErrors(service.formEdit, error);
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Hapus harga
             */
            const deletePrice = (event, id) => {
                event.preventDefault();

                app.delete('Harga', async (deleted) => {
                    if (!deleted) {
                        return;
                    }

                    try {
                        const response = await service.delete(id);
                        const { status, message } = response;

                        if (status === 200) {
                            app.alert('success', message);
                            service.priceTable.ajax.reload();
                        }
                    } catch (error) {
                        app.alert('danger', error.message);
                    }
                })
            }
        </script>
    </x-slot:script>
</x-layouts.auth>
