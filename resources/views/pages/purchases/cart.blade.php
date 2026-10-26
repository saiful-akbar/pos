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

                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary text-secondary"
                                                data-bs-toggle="tooltip"
                                                data-bs-title="Pilih produk"
                                                id="buttonModalProduct"
                                            >
                                                <x-icon>table</x-icon>
                                            </button>
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

        <div class="col-12 col-xl-4 mt-7 mt-xl-0">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h2 class="fs-5 mb-0">Total</h2>
                        </div>

                        <div class="card-body">
                            <ul class="list-group list-group-flush m-0 p-0">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-body-secondary">Total Produk</span>
                                    <span class="fs-6 fw-bold" id="totalProduct"></span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-body-secondary">Total Qty</span>
                                    <span class="fs-6 fw-bold" id="totalQuantity"></span>
                                </li>
        
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-body-secondary">Grand Total</span>
                                    <span class="fs-6 fw-bold" id="totalPrice"></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-7">
                    <form action="{{ route('transactions.purchases') }}" method="post" id="formCreatePurchase" autocomplete="off">
                        @csrf @method('post')
                        
                        <div class="card shadow sm">
                            <div class="card-header">
                                <h2 class="fs-5 mb-0">Pembayaran</h2>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-5">
                                        <x-form-select
                                            required
                                            name="supplier_id"
                                            id="supplierId"
                                            label="Pemasok"
                                            placeholder="Pemasok"
                                            error="{{ $errors->has('supplier_id') }}"
                                        >
                                            <option selected disabled>Pilih</option>
                
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </x-form-select>
                                    </div>

                                    <div class="col-12 mb-5">
                                        <x-form-input
                                            required
                                            type="text"
                                            name="transaction_date"
                                            id="transactionDate"
                                            label="Tanggal"
                                            placeholder="Pilih tanggal"
                                            error="{{ $errors->has('transaction_date') }}"
                                            value="{{ old('transaction_date', date('Y-m-d')) }}"
                                            data-flatpickr=""
                                        />
                                    </div>

                                    <div class="col-12 mb-5">
                                        <x-form-input
                                            type="text"
                                            name="invoice_number"
                                            id="invoiceNumber"
                                            label="Nomor faktur"
                                            placeholder="Masukan nomor faktur pembelian..."
                                            error="{{ $errors->has('invoice_number') }}"
                                            value="{{ old('invoice_number') }}"
                                        />
                                    </div>

                                    <div class="col-12 mb-5">
                                        <x-form-select
                                            label="Metode pembayaran"
                                            placeholder="Metode Pembayaran"
                                            name="payment_method"
                                            id="paymentMethod"
                                            error="{{ $errors->has('payment_method') }}"
                                        >
                                            <option disabled selected>Pilih</option>
                                            <option value="cash" @selected(old('payment_method') == 'cash')>Tunai</option>
                                            <option value="transfer" @selected(old('payment_method') == 'transfer')>Transfer</option>
                                            <option value="debit" @selected(old('payment_method') == 'debit')>Debit</option>
                                            <option value="credit" @selected(old('payment_method') == 'credit')>Kredit</option>
                                        </x-form-select>
                                    </div>

                                    <div class="col-12 mb-5">
                                        <x-form-input
                                            type="number"
                                            name="amount_paid"
                                            id="amountPaid"
                                            label="Jumlah yang dibayarkan"
                                            placeholder="Masukan nominal yang dibayarkan..."
                                            error="{{ $errors->has('amount_paid') }}"
                                            value="{{ old('amount_paid') }}"
                                            step="any"
                                        />
                                    </div>

                                    <div class="col-12">
                                        <x-form-input
                                            type="textarea"
                                            name="notes"
                                            id="notes"
                                            label="Catatan"
                                            placeholder="Masukan catatan..."
                                            error="{{ $errors->has('notes') }}"
                                            value="{{ old('notes') }}"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <x-icon class="me-1">save</x-icon>
                                    <span>Simpan</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal product --}}
    <x-modal title="Produk" size="xl" fullScreen="md" id="modalProduct">
        <table id="productTable" class="table table-sm table-hover align-middle w-100"></table>
    </x-modal>

    {{-- Form modal edit cart detail --}}
    <form action="#" method="post" id="formEditCartDetail" autocomplete="off">
        @csrf @method('patch')

        <x-modal id="modalEditCartDetail" title="Edit" size="md" fullScreen="md">
            <div class="row">
                <div class="col-12 mb-5">
                    <x-form-input type="text" name="product" id="productEdit" label="Produk" disabled />
                </div>

                <div class="col-12 mb-5">
                    <x-form-select name="unit_id" id="unitId" label="Unit (Satuan)" required />
                </div>

                <div class="col-12 mb-5">
                    <x-form-input type="number" name="quantity" id="quantity" step="0.01" label="Quantity" required />
                </div>
            </div>

            <x-slot:footer>
                <div class="row">
                    <div class="col-6">
                        <x-button
                            type="button"
                            color="light"
                            startIcon="close"
                            data-bs-dismiss="modal"
                            fullWidth
                        >
                            Batal
                        </x-button>
                    </div>

                    <div class="col-6">
                        <x-button
                            type="submit"
                            color="secondary"
                            startIcon="save"
                            fullWidth
                        >
                            Simpan
                        </x-button>
                    </div>
                </div>
            </x-slot:footer>
        </x-modal>
    </form>

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
                    this.cartDetailTable = this.#cartDetailDataTableInit();
                    this.productTable = this.#productDataTableInit();
                    this.modalEditCartDetail = new bootstrap.Modal('#modalEditCartDetail');
                    this.formEditCartDetail = $('#formEditCartDetail');
                }

                /**
                 * Inisialisasi product dataTable.
                 */
                #productDataTableInit() {
                    return app.modalDataTable('#productTable', {
                        serverSide: true,
                        scrollX: false,
                        ajax: "{{ route('masters.products', ['is_active' => true]) }}",
                        columns: [
                            {
                                title: 'Kode',
                                name: 'code',
                                data: 'code',
                                type: 'string',
                                className: 'text-nowrap',
                            },
                            {
                                title: 'Nama (Deskripsi)',
                                name: 'name',
                                data: 'name',
                                type: 'string',
                                className: 'text-nowrap',
                            },
                            {
                                title: 'Kategori',
                                name: 'category.code',
                                data: 'category.code',
                                type: 'string',
                                width: '300px',
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
                        ajax: "{{ route('transactions.purchases.carts.details.table', ['cartId' => $cart->id]) }}",
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
                                name: 'purchase_cart_details.price',
                                data: 'price',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data, type, row) => `Rp ${app.numberFormat(data)}`,
                            },
                            {
                                title: 'Qty',
                                name: 'purchase_cart_details.quantity',
                                data: 'quantity',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data, type, row) => app.numberFormat(data),
                            },
                            {
                                title: 'Subtotal',
                                name: 'purchase_cart_details.subtotal',
                                data: 'subtotal',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data, type, row) => `Rp ${app.numberFormat(data)}`,
                            },
                            {
                                title: app.icon('settings', 'fs-6'),
                                name: 'purchase_cart_details.id',
                                data: 'id',
                                type: 'html',
                                className: 'text-nowrap text-center',
                                searchable: false,
                                orderable: false,
                                render: (data, type, row) => {
                                    return `
                                        <button
                                            type="button"
                                            class="btn btn-light btn-sm rounded"
                                            title="Edit"
                                            data-action="edit"
                                        >
                                            ${app.icon('edit', 'fs-6')}
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-light btn-sm rounded"
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
                            $(row).find('button[data-action="edit"]').click(async (e) => { 
                                e.preventDefault();
                                
                                try {
                                    const selectEl = this.formEditCartDetail.find('select[name="unit_id"]');
                                    const response = await this.getUnitsByProduct(data.product_id);
    
                                    if (response.status === 200) {
                                        this.formEditCartDetail.attr('action', app.url(`/transactions/purchases/carts/${this.cartId}/details/${data.id}`));
                                        this.formEditCartDetail.find('input[name="product"]').val(`${data.product_name} (${data.product_code})`);
                                        this.formEditCartDetail.find('input[name="quantity"]').val(data.quantity);
                                        this.formEditCartDetail.find('select[name="unit_id"]').empty();

                                        response.data.forEach(price => {
                                            this.formEditCartDetail.find('select[name="unit_id"]').append(`
                                                <option value="${price.unit.id}" ${price.unit.id === data.unit_id ? 'selected' : ''}>
                                                    ${price.unit.name} (${price.unit.code})
                                                </option>
                                            `);
                                        });

                                        this.modalEditCartDetail.show();
                                    }
                                } catch (error) {
                                    app.alert('danger', error.message);
                                }
                            });
                        }
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
                 * Update cart detail.
                 */
                updateCartDetail() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "patch",
                            url: this.formEditCartDetail.attr('action'),
                            data: this.formEditCartDetail.serialize(),
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });
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
            }

            window.service = new Service();
        </script>

        <script>
            "use strict"

            /**
             * Ambil data cart setelah dokumen selesai dimuat 
             */
            $(document).ready(async function () {
                try {
                    await service.findCart();
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
                        service.cartDetailTable.ajax.reload();

                        await service.findCart();
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
                            service.cartDetailTable.ajax.reload();

                            await service.findCart();
                        }
                    } catch (error) {
                        app.alert('danger', error.message);
                    } finally {
                        app.hidePreloader();
                    }
                });
            }

            /**
             * Submit form edit cart detail
             */
            service.formEditCartDetail.submit(async function (e) { 
                e.preventDefault();
                app.showPreloader();
                app.clearFormErrors(service.formEditCartDetail);
                
                try {
                    const response = await service.updateCartDetail();
                    const { status, message } = response;

                    if (status === 200) {
                        app.alert('success', message);
                        service.cartDetailTable.ajax.reload();
                        service.modalEditCartDetail.hide();

                        await service.findCart();
                    }
                } catch (error) {
                    app.setFormErrors(service.formEditCartDetail, error);
                } finally {
                    app.hidePreloader();
                }
            });
        </script>
    </x-slot:script>
</x-layouts.auth>