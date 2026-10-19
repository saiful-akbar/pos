<x-layouts.auth title="Tambah Pembelian">
    <x-page-header title="Tambah Pembelian" icon="add_shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-6 col-xl-4">
                    <form action="{{ route('transactions.purchases.carts.delete', ['cartId' => $cart->id]) }}" method="post" id="formCancel">
                        @csrf @method('delete')
                    </form>

                    <x-button type="submit" id="buttonCancel" color="danger" startIcon="close" fullWidth>
                        Batalkan
                    </x-button>
                </div>
            </div>
        </x-slot:action>
    </x-page-header>

    <div class="row gx-7 mt-7">
        <div class="col-12 col-xl-8">
            <div class="row gx-7">
                <div class="col-12">
                    <form action="{{ route('transactions.purchases.carts.details.store', ['cartId' => $cart->id]) }}" method="post" id="formCreateCartDetail" autocomplete="off">
                        @csrf @method('post')

                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="row gx-7">
                                    <div class="col-12 col-lg-8">
                                        <div class="input-group border" data-invalid="product_code">
                                            <input
                                                required
                                                autofocus
                                                type="text"
                                                name="product_code"
                                                id="productCode"
                                                placeholder="Masukan atau pilih kode produk"
                                                class="form-control border"
                                                value="{{ old('product_code') }}"
                                                data-invalid="product_code"
                                            />

                                            <x-button
                                                type="button"
                                                class="text-secondary"
                                                data-bs-toggle="tooltip"
                                                data-bs-title="Pilih produk"
                                                id="buttonModalProduct"
                                            >
                                                <x-icon>table</x-icon>
                                            </x-button>
                                        </div>

                                        <div class="invalid-feedback" data-invalid-feedback="product_code"></div>
                                    </div>

                                    <div class="col-12 col-lg-4 mt-5 mt-lg-0">
                                        <x-button type="submit" startIcon="add" fullWidth>Tambah</x-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-12 mt-7">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <table id="cartDetailTable" class="table table-hover table-sm align-middle w-100"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal product --}}
    <x-modal title="Produk" size="xl" fullScreen="md" id="modalProduct">
        <table id="productTable" class="table table-sm table-hover align-middle w-100"></table>
    </x-modal>

    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.cartId = "{{ $cart->id }}"
                    this.buttonCancel = $('#buttonCancel');
                    this.formCancel = $('#formCancel');
                    this.formCreateCartDetail = $('#formCreateCartDetail');
                    this.modalProduct = new bootstrap.Modal('#modalProduct');
                    this.buttonModalProduct = $('#buttonModalProduct');
                    this.productTable = this.#productDataTableInit();
                    this.cartDetailTable = this.#cartDetailDataTableInit();
                }

                /**
                 * Inisialisasi product dataTable.
                 */
                #productDataTableInit() {
                    return app.modalDataTable('#productTable', {
                        serverSide: true,
                        scrollX: true,
                        ajax: "{{ route('masters.products', ['is_active' => true]) }}",
                        columns: [
                            {
                                title: 'Kode Produk',
                                name: 'code',
                                data: 'code',
                                type: 'string',
                                className: 'text-nowrap'
                            },
                            {
                                title: 'Nama (Deskripsi)',
                                name: 'name',
                                data: 'name',
                                type: 'string',
                                width: '300px',
                            },
                            {
                                title: 'Kategori',
                                name: 'category.code',
                                data: 'category.code',
                                type: 'string',
                                className: 'text-nowrap',
                                render: (data, type, row) => `${row.category.name} (${row.category.code})`,
                            },
                            {
                                title: 'Stok Minimum',
                                name: 'minimum_stock',
                                data: 'minimum_stock',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data) => app.numberFormat(data),
                            },
                            {
                                title: 'Stok Tersedia',
                                name: 'current_stock',
                                data: 'current_stock',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data) => app.numberFormat(data),
                            },
                        ],
                        createdRow: (row, data, index) => {
                            $(row).addClass('cursor-pointer');
                            
                            $(row).click(async (e) => { 
                                e.preventDefault();
                                app.showPreloader();
                                app.clearFormErrors(this.formCreateCartDetail);
                                
                                this.formCreateCartDetail.find('[name="product_code"]').val(data.code);
                                this.modalProduct.hide();

                                try {
                                    const response = await this.createCartDetail();
                                    const { status, message } = response;

                                    if (status === 201) {
                                        app.alert('success', message);
                                        app.clearFormErrors(this.formCreateCartDetail);

                                        this.formCreateCartDetail.trigger('reset');
                                        this.formCreateCartDetail.find('[name="product_code"]').focus();

                                        await this.getCartDetails();
                                    }
                                } catch (error) {
                                    app.setFormErrors(this.formCreateCartDetail, error);
                                } finally {
                                    app.hidePreloader();
                                }
                            });
                            
                        },
                    });
                }

                /**
                 * Inisialisasi cart detail datatable.
                 */
                #cartDetailDataTableInit() {
                    return app.dataTable('#cartDetailTable', {
                        fixedColumns: {
                            start: 0,
                            end: 1,
                        },
                        columns: [
                            {
                                title: 'Produk',
                                name: 'product.code',
                                data: 'product.code',
                                type: 'string',
                                className: 'text-nowrap',
                                render: (data, type, row) => `${row.product.name} (${row.product.code})`
                            },
                            {
                                title: 'Unit (Satuan)',
                                name: 'unit.code',
                                data: 'unit.code',
                                type: 'string',
                                className: 'text-nowrap',
                                render: (data, type, row) => `${row.unit.name} (${row.unit.code})`
                            },
                            {
                                title: 'Harga',
                                name: 'price',
                                data: 'price',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data, type, row) => `Rp ${app.numberFormat(data)}`,
                            },
                            {
                                title: 'Qty',
                                name: 'quantity',
                                data: 'quantity',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data, type, row) => app.numberFormat(data),
                            },
                            {
                                title: 'Subtotal',
                                name: 'subtotal',
                                data: 'subtotal',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data, type, row) => `Rp ${app.numberFormat(data)}`,
                            },
                            {
                                title: app.icon('settings', 'fs-6'),
                                name: 'id',
                                data: 'id',
                                type: 'html',
                                className: 'text-nowrap text-center',
                                searchable: false,
                                orderable: false,
                                render: (data) => {
                                    return `
                                        <button type="button" class="btn btn-light" title="Edit">
                                            ${app.icon('edit', 'fs-6')}
                                        </button>

                                        <button type="button" class="btn btn-light" title="Hapus" onclick="deleteCartDetail(event, '${data}')">
                                            ${app.icon('delete', 'fs-6')}
                                        </button>
                                    `;
                                }
                            },
                        ],
                    })
                }

                /**
                 * Create cart detail
                 */
                createCartDetail() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "post",
                            url: this.formCreateCartDetail.attr('action'),
                            data: this.formCreateCartDetail.serialize(),
                            dataType: "json",
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }

                /**
                 * Get cart details
                 */
                getCartDetails() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "get",
                            url: "{{ route('transactions.purchases.carts.details', ['cartId' => $cart->id]) }}",
                            success: (response) => {
                                resolve(response);
                                this.cartDetailTable.clear().draw();
                                this.cartDetailTable.rows.add(response.data).draw();
                            },
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }

                /**
                 * Delete cart detail
                 */
                deleteCartDetail(cartDetailId) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "delete",
                            url: app.url(`/transactions/purchases/carts/${this.cartId}/details/${cartDetailId}`),
                            data: {
                                _token: app.csrfToken,
                                _method: 'delete',
                            },
                            dataType: 'json',
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
             * Ambil data cart detail setelah dokumen selesai dimuat.
             */
            $(document).ready(async function () {
                try {
                    await service.getCartDetails();
                } catch (error) {
                    app.alert('danger', error.message);
                }
            });

            /**
             * Submit form cancel 
             */
            service.buttonCancel.click(function (e) { 
                e.preventDefault();
                
                app.delete('Transaksi Pembelian', (deleted) => {
                    if (deleted) {
                        app.showPreloader();
                        service.formCancel.submit();
                    }
                });
            });

            /**
             * Buka modal product table
             */
            service.buttonModalProduct.click(function (e) { 
                e.preventDefault();
                service.modalProduct.show();
            });

            /**
             * Submit form create cart detail. 
             */
            service.formCreateCartDetail.submit(async function (e) { 
                e.preventDefault();
                app.showPreloader();
                app.clearFormErrors(service.formCreateCartDetail);

                try {
                    const response = await service.createCartDetail();
                    const { status, message } = response;

                    if (status === 201) {
                        app.alert('success', message);
                        app.clearFormErrors(service.formCreateCartDetail);
                        service.formCreateCartDetail.trigger('reset');
                        service.formCreateCartDetail.find('[name="product_code"]').focus();
                    }
                } catch (error) {
                    app.setFormErrors(service.formCreateCartDetail, error);
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Delete cart detail
             */
            const deleteCartDetail = (e, cartDetailId) => {
                app.delete('Produk', async (deleted) => {
                    if (!deleted) return;

                    app.showPreloader();

                    try {
                        const response = await service.deleteCartDetail(cartDetailId);
                        const { status, message } = response;

                        if (status === 200) {
                            app.alert('success', message);
                            await service.getCartDetails();
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