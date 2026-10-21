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

        <div class="col-12 col-xl-4 mt-7 mt-xl-0">
            <div class="row gx-7">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="fs-6 mb-0">Total</h3>
                                </div>

                                <div class="col-auto my-n1">
                                    <div class="dropdown">
                                        <div data-bs-toggle="dropdown">
                                            <x-button
                                                size="sm"
                                                color="light"
                                                class="rounded"
                                                data-bs-toggle="tooltip"
                                                data-bs-title="Pilih atau hapus member"
                                            >
                                                <x-icon class="fs-6">person</x-icon>
                                            </x-button>
                                        </div>

                                        <ul class="dropdown-menu border">
                                            <li>
                                                <a class="dropdown-item" href="#" id="buttonAddCustomer">
                                                    <x-icon class="me-1">check</x-icon>
                                                    <span>Pilih member</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#" id="buttonRemoveCustomer">
                                                    <x-icon class="me-1">remove</x-icon>
                                                    <span>Hapus member</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('masters.customers') }}" target="_blank">
                                                    <x-icon class="me-1">add</x-icon>
                                                    <span>Tambah member baru</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <ul class="list-group list-group-flush m-0 p-0">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-body-secondary">Member</span>
                                    <span class="fw-bold ms-10 text-end" id="customer">{{ $cart->customer?->name ?? '-' }}</span>
                                </li>
                                
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-body-secondary">Qty</span>
                                    <span class="fw-bold ms-10 text-end" id="totalQuantity">{{ number_format($cart->totalQuantity, 2) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-body-secondary">Subtotal</span>
                                    <span class="fw-bold ms-10 text-end" id="totalAmount">Rp {{ number_format($cart->totalAmount, 2) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-body-secondary">Diskon</span>
                                    <span class="fw-bold ms-10 text-end" id="discount">{{ number_format($cart->discount) }}%</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-body-secondary">Grand Total</span>
                                    <span class="fw-bold ms-10 text-end" id="grandTotal" data-value="{{ $cart->grandTotal }}">Rp {{ number_format($cart->grandTotal, 2) }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-7">
                    <form id="formCreateSale" action="{{ route('transactions.sales.store') }}" method="post" autocomplete="off">
                        @csrf

                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h3 class="fs-6 mb-0">Pembayaran</h3>
                            </div>

                            <div class="card-body">
                                <div class="mb-5">
                                    <x-form-select
                                        required
                                        name="payment_method"
                                        id="paymentMethod"
                                        label="Metode pembayaran"
                                        error="{{ $errors->has('payment_method') }}"
                                    >
                                        <option value="cash" @selected(old('payment_method') == 'cash')>Tunai</option>
                                        <option value="qris" @selected(old('payment_method') == 'qris')>Qris</option>
                                        <option value="debit" @selected(old('payment_method') == 'debit')>Kartu debit</option>
                                        <option value="credit" @selected(old('payment_method') == 'credit')>Kartu kredit</option>
                                    </x-form-select>
                                </div>

                                <div class="mb-5">
                                    <x-form-input
                                        required
                                        type="number"
                                        name="paid_amount"
                                        id="paidAmount"
                                        label="Pembayaran"
                                        placeholder="Masukan pembayaran..."
                                        value="{{ old('paid_amount') }}"
                                        error="{{ $errors->has('paid_amount') }}"
                                    />
                                </div>

                                <div class="mb-5">
                                    <x-form-input
                                        type="number"
                                        name="change_amount"
                                        id="changeAmount"
                                        label="Kembalian"
                                        placeholder="Kembalian..."
                                        value="{{ old('change_amount') }}"
                                        error="{{ $errors->has('change_amount') }}"
                                        readonly
                                    />
                                </div>

                                <div class="">
                                    <x-form-input
                                        type="textarea"
                                        name="notes"
                                        id="notes"
                                        label="Catatan"
                                        placeholder="Masukan catatan..."
                                        value="{{ old('notes') }}"
                                        error="{{ $errors->has('notes') }}"
                                    />
                                </div>
                            </div>

                            <div class="card-footer">
                                <x-button
                                    type="submit"
                                    color="secondary"
                                    startIcon="save"
                                    fullWidth
                                >
                                    Selesaikan Transaksi
                                </x-button>
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

    {{-- Modal customer --}}
    <x-modal title="Pelanggan (Member)" size="xl" fullScreen="md" id="modalCustomer">
        <table id="customerTable" class="table table-sm table-hover align-middle w-100"></table>
    </x-modal>

    {{-- Form modal edit cart detail quantity --}}
    <form action="#" method="post" id="formEditCartDetailQuantity" autocomplete="off">
        @csrf @method('patch')

        <x-modal id="modalEditCartDetailQuantity" title="Edit" size="md" fullScreen="md">
            <div class="mb-5">
                <x-form-input type="text" name="product" id="productEditQuantity" label="Produk" disabled />
            </div>

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
                <x-form-input type="text" name="product" id="productEditPrice" label="Produk" disabled />
            </div>

            <div class="mb-5">
                <x-form-select name="unit_id" id="unitEditPrice" label="Unit (Satuan)" required></x-form-select>
            </div>

            <div>
                <x-form-input type="number" name="price" id="price" step="0.01" label="Harga" required />
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
                    this.buttonAddCustomer = $('#buttonAddCustomer');
                    this.buttonRemoveCustomer = $('#buttonRemoveCustomer');
                    this.modalCustomer = new bootstrap.Modal('#modalCustomer');
                    this.customerTable = this.#customerDataTableInit();
                    this.formCreateSale = $('#formCreateSale');
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
                                app.showPreloader();
                                
                                try {
                                    const selectEl = this.formEditCartDetailQuantity.find('select[name="unit_id"]');
                                    const response = await this.getUnitsByProduct(data.product_id);
    
                                    if (response.status === 200) {
                                        this.formEditCartDetailQuantity.attr('action', app.url(`/transactions/sales/carts/${this.cartId}/details/${data.id}/quantity`));
                                        this.formEditCartDetailQuantity.find('input[name="product"]').val(`${data.product_name} (${data.product_code})`);
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
                                } finally {
                                    app.hidePreloader();
                                }
                            });

                            $(row).find('button[data-action="edit-price"]').click(async (e) => { 
                                e.preventDefault();
                                app.showPreloader();
                                
                                try {
                                    const selectEl = this.formEditCartDetailPrice.find('select[name="unit_id"]');
                                    const response = await this.getUnitsByProduct(data.product_id);
    
                                    if (response.status === 200) {
                                        this.formEditCartDetailPrice.attr('action', app.url(`/transactions/sales/carts/${this.cartId}/details/${data.id}/price`));
                                        this.formEditCartDetailPrice.find('input[name="product"]').val(`${data.product_name} (${data.product_code})`);
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
                                } finally {
                                    app.hidePreloader();
                                }
                            });
                        }
                    })
                }

                /**
                 * Inisialisasi customer dataTable.
                 */
                #customerDataTableInit() {
                    return app.modalDataTable('#customerTable', {
                        serverSide: true,
                        ajax: "{{ route('masters.customers') }}",
                        columns: [
                            {
                                title: 'Nama',
                                name: 'name',
                                data: 'name',
                                type: 'string',
                                className: 'text-nowrap',
                            },
                            {
                                title: 'Telepon',
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
                                title: 'Diskon (%)',
                                name: 'discount',
                                data: 'discount',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data) => app.numberFormat(data),
                            },
                        ],
                        createdRow: (row, data) => {
                            $(row).addClass('cursor-pointer');

                            $(row).click(async (e) => { 
                                e.preventDefault();
                                app.showPreloader();

                                try {
                                    const response = await this.addCustomer(data.id);

                                    if (response.status === 200) {
                                        await this.findCart();
                                    }
                                } catch (error) {
                                    app.alert('danger', error.message);
                                } finally {
                                    app.hidePreloader();
                                    this.modalCustomer.hide();
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
                            url: app.url(`/transactions/sales/carts`),
                            success: (response) => {
                                resolve(response);
                                
                                $('#customer').text(response.data.customer?.name ?? '-');
                                $('#totalQuantity').text(app.numberFormat(response.data.total_quantity));
                                $('#totalAmount').text(`Rp ${app.numberFormat(response.data.total_amount)}`);
                                $('#discount').text(`${app.numberFormat(response.data.discount, 0)}%`);
                                $('#grandTotal').text(`Rp ${app.numberFormat(response.data.grand_total)}`);
                                $('#grandTotal').data('value', response.data.grand_total);
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

                /**
                 * Tambah pelanggan
                 */
                addCustomer(customerId) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "patch",
                            url: app.url(`/transactions/sales/carts/${this.cartId}`),
                            data: {
                                _token: app.csrfToken,
                                _method: 'patch',
                                customer_id: customerId,
                            },
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

            /**
             * Buka modal table customer
             */
            service.buttonAddCustomer.click(function (e) { 
                e.preventDefault();
                service.modalCustomer.show();
                service.customerTable.ajax.reload();
            });

            /**
             * Hapus customer
             */
            service.buttonRemoveCustomer.click(async function (e) { 
                e.preventDefault();
                app.showPreloader();

                try {
                    const response = await service.addCustomer(null);

                    if (response.status === 200) {
                        await service.findCart();
                    }
                } catch (error) {
                    app.alert('danger', error.message);
                } finally {
                    app.hidePreloader();
                    service.modalCustomer.hide();
                }
            });

            /**
             * Mengisi nilai defaul pada form pembayaran jika
             * metode pembayaran yang dipilih bukan cash (tunai).
             */
            service.formCreateSale.find('select[name=payment_method]').change(function(e) {
                e.preventDefault();

                const inputPaidAmount = service.formCreateSale.find('input[name=paid_amount]');
                const inputChangeAmount = service.formCreateSale.find('input[name=change_amount]');
                const grandTotal = $('#grandTotal').data('value');

                if ($(this).val() !== 'cash') {
                    inputPaidAmount.attr('readonly', 'readonly');
                    inputPaidAmount.removeClass('is-invalid');
                    inputPaidAmount.val(grandTotal);
                    inputChangeAmount.val('');
                } else {
                    inputPaidAmount.val("");
                    inputPaidAmount.removeAttr('readonly');
                }
            });

            /**
             * Update nilai kembalian ketika form
             * pembayaran diinput.
             */
            service.formCreateSale.find('input[name=paid_amount]').keyup(function(e) {
                e.preventDefault();

                const grandTotal = $('#grandTotal').data('value');
                const paidAmount = $(this).val();
                const changeAmount = paidAmount - grandTotal;
                const inputChangeAmount = service.formCreateSale.find('input[name=change_amount]');
                const inputPaidAmount = service.formCreateSale.find('input[name=paid_amount]');

                if (changeAmount >= 0) {
                    inputChangeAmount.val(changeAmount);
                    inputPaidAmount.removeClass('is-invalid');
                    inputPaidAmount.addClass('border');
                    inputPaidAmount.next().text('');
                } else {
                    inputChangeAmount.val(0);
                    inputPaidAmount.addClass('is-invalid');
                    inputPaidAmount.removeClass('border');
                    inputPaidAmount.next().text('Pembayaran kurang');
                }
            });

            /**
             * Submit form create sales
             */
            service.formCreateSale.find('button[type="submit"]').click(function (e) { 
                e.preventDefault();
                app.showPreloader();
                service.formCreateSale.submit();
            });
        </script>
    </x-slot:script>
</x-layouts.auth>