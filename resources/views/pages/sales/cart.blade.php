<x-layouts.auth title="Penjualan">
    <x-page-header title="Penjualan" icon="shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-6 col-xl-4">
                    <form action="{{ route('transactions.sales.carts.delete', ['cartId' => $cart->id]) }}" method="post" id="formCancel">
                        @csrf @method('delete')

                        <x-button
                            type="submit"
                            color="danger"
                            startIcon="close"
                            fullWidth
                        >
                            Batalkan
                        </x-button>
                    </form>
                </div>
            </div>
        </x-slot:action>
    </x-page-header>

    <div class="row mt-7 gx-7">
        <div class="col-12 col-xl-8">
            <div class="row gx-7">
                <div class="col-12">
                    <form action="{{ route('transactions.sales.carts.details.store', ['cartId' => $cart->id]) }}" method="post" id="formCreateCartDetail" autocomplete="off">
                        @csrf @method('post')
        
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-9">
                                        <div class="input-group border" data-invalid="product_code">
                                            <input
                                                required
                                                autofocus
                                                type="text"
                                                name="product_code"
                                                id="productCode"
                                                placeholder="Pilih atau masukan kode produk..."
                                                class="form-control border"
                                                value="{{ old('product_code') }}"
                                                data-invalid="product_code"
                                            />
        
                                            <x-button
                                                type="button"
                                                class="text-secondary"
                                                id="buttonProduct"
                                                data-bs-toggle="tooltip"
                                                data-bs-title="Pilih produk"
                                            >
                                                <x-icon class="fs-6">table</x-icon>
                                            </x-button>
                                        </div>
        
                                        <div class="invalid-feedback" data-invalid-feedback="product_code"></div>
                                    </div>
        
                                    <div class="col-12 col-lg-3 mt-5 mt-lg-0">
                                        <x-button
                                            type="submit"
                                            color="secondary"
                                            startIcon="add"
                                            fullWidth
                                        >
                                            Tambah
                                        </x-button>
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

    {{-- Form modal edit cart detail quantity --}}
    <form action="#" method="post" id="formEditCartDetailQuantity" autocomplete="off">
        @csrf @method('patch')

        <x-modal id="modalEditCartDetailQuantity" title="Edit" size="md" fullScreen="md">
            <div class="mb-5">
                <x-form-select name="unit_id" id="unitEditQuantity" label="Unit (Satuan)" required></x-form-select>
            </div>

            <div>
                <x-form-input type="number" name="quantity" id="quantity" step="0.01" label="Quantity" required />
            </div>

            <x-slot:footer>
                <div class="row">
                    <div class="col-6">
                        <x-button type="button" color="light" startIcon="close" data-bs-dismiss="modal" fullWidth>Batal</x-button>
                    </div>
                    <div class="col-6">
                        <x-button type="submit" color="secondary" startIcon="save" fullWidth>Simpan</x-button>
                    </div>
                </div>
            </x-slot:footer>
        </x-modal>
    </form>

    {{-- Form modal edit cart detail amount --}}
    <form action="#" method="post" id="formEditCartDetailPrice" autocomplete="off">
        @csrf @method('patch')

        <x-modal id="modalEditCartDetailPrice" title="Edit" size="md" fullScreen="md">
            <div class="mb-5">
                <x-form-select name="unit_id" id="unitEditPrice" label="Unit (Satuan)" required></x-form-select>
            </div>

            <div>
                <x-form-input type="number" name="price" id="price" step="0.01" label="Jumlah harga" required />
            </div>

            <x-slot:footer>
                <div class="row">
                    <div class="col-6">
                        <x-button type="button" color="light" startIcon="close" data-bs-dismiss="modal" fullWidth>Batal</x-button>
                    </div>
                    <div class="col-6">
                        <x-button type="submit" color="secondary" startIcon="save" fullWidth>Simpan</x-button>
                    </div>
                </div>
            </x-slot:footer>
        </x-modal>
    </form>

    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.cartId = '{{ $cart->id }}';
                    this.buttonProduct = $('#buttonProduct');
                    this.formCancel = $('#formCancel');
                    this.modalProduct = new bootstrap.Modal('#modalProduct');
                    this.productTable = this.#productDataTableInit();
                    this.cartDetailTable = this.#cartDetailDataTableInit();
                    this.formCreateCartDetail = $('#formCreateCartDetail');
                    this.modalEditCartDetailQuantity = new bootstrap.Modal('#modalEditCartDetailQuantity');
                    this.formEditCartDetailQuantity = $('#formEditCartDetailQuantity');
                    this.modalEditCartDetailPrice = new bootstrap.Modal('#modalEditCartDetailPrice');
                    this.formEditCartDetailPrice = $('#formEditCartDetailPrice');
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
                                title: 'Diskon (%)',
                                name: 'discount',
                                data: 'discount',
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
                                this.modalProduct.hide();
                                this.formCreateCartDetail.find('[name="product_code"]').val(data.code);
                                this.formCreateCartDetail.submit();
                            });
                        },
                    });
                }

                /**
                 * Inisialisasi cart detail datatable.
                 */
                #cartDetailDataTableInit() {
                    return app.dataTable('#cartDetailTable', {
                        serverSide: true,
                        ajax: "{{ route('transactions.sales.carts.details', ['cartId' => $cart->id]) }}",
                        ordering: false,
                        fixedColumns: {
                            start: 0,
                            end: 1,
                        },
                        columns: [
                            {
                                title: 'Produk',
                                name: 'products.name',
                                data: 'product_name',
                                type: 'string',
                                className: 'text-nowrap',
                                render: (data, type, row) => `${row.product_name} (${row.product_code})`
                            },
                            {
                                title: 'Unit (Satuan)',
                                name: 'units.name',
                                data: 'unit_name',
                                type: 'string',
                                className: 'text-nowrap',
                                render: (data, type, row) => `${row.unit_name} (${row.unit_code})`
                            },
                            {
                                title: 'Harga',
                                name: 'sale_cart_details.selling_price',
                                data: 'selling_price',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data, type, row) => `Rp ${app.numberFormat(data)}`,
                            },
                            {
                                title: 'Qty',
                                name: 'sale_cart_details.quantity',
                                data: 'quantity',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data, type, row) => app.numberFormat(data),
                            },
                            {
                                title: 'Subtotal',
                                name: 'sale_cart_details.subtotal',
                                data: 'subtotal',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data, type, row) => `Rp ${app.numberFormat(data)}`,
                            },
                            {
                                title: 'Diskon',
                                name: 'sale_cart_details.discount',
                                data: 'discount',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data, type, row) => `${app.numberFormat(data, 0)}%`,
                            },
                            {
                                title: 'Grand Total',
                                name: 'sale_cart_details.grand_total',
                                data: 'grand_total',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data, type, row) => `Rp ${app.numberFormat(data)}`,
                            },
                            {
                                title: app.icon('settings', 'fs-6'),
                                name: 'sale_cart_details.id',
                                data: 'id',
                                type: 'html',
                                className: 'text-nowrap text-center',
                                searchable: false,
                                orderable: false,
                                render: (data, type, row) => {
                                    return `
                                        <button
                                            type="button"
                                            class="btn btn-light"
                                            title="Edit quantity"
                                            data-action="edit-quantity"
                                        >
                                            ${app.icon('edit_square', 'fs-6')}
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-light"
                                            title="Edit harga"
                                            data-action="edit-price"
                                        >
                                            ${app.icon('price_change', 'fs-6')}
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-light"
                                            title="Hapus"
                                            onclick="deleteCartDetail(event, '${data}')"
                                        >
                                            ${app.icon('delete', 'fs-6')}
                                        </button>
                                    `;
                                }
                            },
                        ],
                        createdRow: (row, data, index) => {
                            $(row).find('button[data-action="edit-quantity"]').click(async (e) => { 
                                e.preventDefault();
                                
                                try {
                                    const selectEl = this.formEditCartDetailQuantity.find('select[name="unit_id"]');
                                    const response = await this.getUnitsByProduct(data.product_id);
    
                                    if (response.status === 200) {
                                        this.formEditCartDetailQuantity.attr('action', app.url(`/transactions/sales/carts/${this.cartId}/details/${data.id}/quantity`));
                                        this.formEditCartDetailQuantity.find('input[name="quantity"]').val(data.quantity);
                                        this.formEditCartDetailQuantity.find('select[name="unit_id"]').empty();

                                        response.data.forEach(price => {
                                            this.formEditCartDetailQuantity.find('select[name="unit_id"]').append(`
                                                <option value="${price.unit.id}" ${price.unit.id === data.unit_id ? 'selected' : ''}>
                                                    ${price.unit.name} (${price.unit.code})
                                                </option>
                                            `);
                                        });

                                        this.modalEditCartDetailQuantity.show();
                                    }
                                } catch (error) {
                                    app.alert('danger', error.message);
                                }
                            });

                            $(row).find('button[data-action="edit-price"]').click(async (e) => { 
                                e.preventDefault();
                                
                                try {
                                    const selectEl = this.formEditCartDetailPrice.find('select[name="unit_id"]');
                                    const response = await this.getUnitsByProduct(data.product_id);
    
                                    if (response.status === 200) {
                                        this.formEditCartDetailPrice.attr('action', app.url(`/transactions/sales/carts/${this.cartId}/details/${data.id}/price`));
                                        this.formEditCartDetailPrice.find('input[name="price"]').val(data.subtotal);
                                        this.formEditCartDetailPrice.find('select[name="unit_id"]').empty();

                                        response.data.forEach(price => {
                                            this.formEditCartDetailPrice.find('select[name="unit_id"]').append(`
                                                <option value="${price.unit.id}" ${price.unit.id === data.unit_id ? 'selected' : ''}>
                                                    ${price.unit.name} (${price.unit.code})
                                                </option>
                                            `);
                                        });

                                        this.modalEditCartDetailPrice.show();
                                    }
                                } catch (error) {
                                    app.alert('danger', error.message);
                                }
                            });
                        }
                    })
                }

                /**
                 * Mengambil data cart
                 */
                findCart() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "get",
                            url: app.url(`/transactions/purchases/carts`),
                            success: (response) => {
                                resolve(response);
                                $('#totalProduct').text(app.numberFormat(response.data.total_product, 0));
                                $('#totalQuantity').text(app.numberFormat(response.data.total_quantity));
                                $('#totalPrice').text(`Rp ${app.numberFormat(response.data.total_price)}`);
                            },
                            error: (error) => reject(error.responseJSON),
                        });
                    });
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
                 * Delete cart detail
                 */
                deleteCartDetail(cartDetailId) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "delete",
                            url: app.url(`/transactions/sales/carts/${this.cartId}/details/${cartDetailId}`),
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

                /**
                 * Mengambil daftar unit produk
                 */
                getUnitsByProduct(productId) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "get",
                            url: app.url(`/masters/products/${productId}/prices`),
                            data: { _token: app.csrfToken },
                            dataType: 'json',
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }

                /**
                 * Update cart detail berdasarkan quantity.
                 */
                updateCartDetailByQuantity() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "patch",
                            url: this.formEditCartDetailQuantity.attr('action'),
                            data: this.formEditCartDetailQuantity.serialize(),
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
                }

                /**
                 * Update cart detail berdasarkan price.
                 */
                updateCartDetailByPrice() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "patch",
                            url: this.formEditCartDetailPrice.attr('action'),
                            data: this.formEditCartDetailPrice.serialize(),
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
             * Mengambil data cart dan cart detail
             */
            $(document).ready(async function () {
                await service.findCart();
            });

            /**
             * Submit form close (cancel) 
             */
            service.formCancel.find('button[type="submit"]').click(function (e) { 
                e.preventDefault();
                
                app.delete('Transaksi', (deleted) => {
                    if (deleted) {
                        service.formCancel.submit();
                    }
                });
            });

            /**
             * Buka modal product table
             */
            service.buttonProduct.click(function (e) { 
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
                        await service.findCart();

                        service.cartDetailTable.ajax.reload();
                        service.formCreateCartDetail.trigger('reset');
                        service.formCreateCartDetail.find('[name="product_code"]').focus();
                        app.alert('success', message);
                        app.clearFormErrors(service.formCreateCartDetail);
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

                        if (response.status === 200) {
                            await service.findCart();

                            service.cartDetailTable.ajax.reload();
                            app.alert('success', response.message);
                        }
                    } catch (error) {
                        app.alert('danger', error.message);
                    } finally {
                        app.hidePreloader();
                    }
                });
            }

            /**
             * Submit form edit cart detail by quantity
             */
            service.formEditCartDetailQuantity.submit(async function (e) { 
                e.preventDefault();
                app.showPreloader();
                app.clearFormErrors(service.formEditCartDetailQuantity);
                
                try {
                    const response = await service.updateCartDetailByQuantity();

                    if (response.status === 200) {
                        await service.findCart();

                        service.cartDetailTable.ajax.reload();
                        service.modalEditCartDetailQuantity.hide();
                        app.alert('success', response.message);
                    }
                } catch (error) {
                    console.log(error);
                    
                    app.setFormErrors(service.formEditCartDetailQuantity, error);
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Submit form edit cart detail by price
             */
            service.formEditCartDetailPrice.submit(async function (e) { 
                e.preventDefault();
                app.showPreloader();
                app.clearFormErrors(service.formEditCartDetailPrice);
                
                try {
                    const response = await service.updateCartDetailByPrice();

                    if (response.status === 200) {
                        await service.findCart();

                        service.cartDetailTable.ajax.reload();
                        service.modalEditCartDetailPrice.hide();
                        app.alert('success', response.message);
                    }
                } catch (error) {
                    console.log(error);
                    
                    app.setFormErrors(service.formEditCartDetailPrice, error);
                } finally {
                    app.hidePreloader();
                }
            });
        </script>
    </x-slot:script>
</x-layouts.auth>