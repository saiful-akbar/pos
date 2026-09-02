<x-layouts.auth title="Tambah Pembelian">
    <x-page-header title="Tambah Pembelian" icon="add_shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end align-items-center">
                <div class="col-12 col-lg-6 col-xl-4">
                    <form action="{{ route('transactions.purchases.cart.cancel') }}" method="post">
                        @csrf @method('delete')

                        <button type="submit" class="btn btn-danger w-100">
                            <x-icon class="me-1">close</x-icon>
                            <span>Batalkan Pembelian</span>
                        </button>
                    </form>
                </div>
            </div>
        </x-slot:action>
    </x-page-header>

    {{-- Modal product table --}}
    <div class="modal" tabindex="-1" id="modalProductTable">
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-md-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <table id="productTable" class="table table-hover align-middle w-100"></table>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <x-icon class="me-1">close</x-icon>
                        <span>Batal</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Form tambah produk dan daftar cart detail --}}
    <div class="row mt-7">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <form action="{{ route('transactions.purchases.cart.store') }}" method="post" autocomplete="off">
                        @csrf @method('post')
                        
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h3 class="fs-6 m-0">Tambah Produk</h3>
                            </div>
    
                            <div class="card-body">
                                <div class="row align-items-end">
                                    <div class="col-12 col-lg-3 mb-6 mb-lg-0">
                                        <label for="productCode" class="form-label">
                                            Produk <span class="text-danger">*</span>
                                        </label>

                                        <div @class(['input-group', 'is-invalid' => $errors->has('product_code')])>
                                            <input
                                                required
                                                type="text"
                                                name="product_code"
                                                id="productCode"
                                                placeholder="Masukan kode atau barcode..."
                                                value="{{ old('product_code') }}"
                                                @class(['form-control', 'is-invalid' => $errors->has('product_code')])
                                            />

                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary"
                                                id="buttonProductTable"
                                                data-bs-toggle="tooltip"
                                                data-bs-title="Pilih produk"
                                            >
                                                <x-icon class="text-secondary">table</x-icon>
                                            </button>
                                        </div>

                                        @error('product_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-lg-3 mb-6 mb-lg-0">
                                        <x-form-input
                                            type="text"
                                            name="purchase_price"
                                            id="purchasePrice"
                                            label="Harga beli"
                                            placeholder="Masukan harga beli..."
                                            value="{{ old('purchase_price') }}"
                                            error="{{ $errors->has('purchase_price') }}"
                                        />
                                    </div>

                                    <div class="col-12 col-lg-3 mb-6 mb-lg-0">
                                        <x-form-input
                                            type="text"
                                            name="quantity"
                                            id="quantity"
                                            label="Quantity"
                                            placeholder="Masukan quantity..."
                                            value="{{ old('quantity') }}"
                                            error="{{ $errors->has('quantity') }}"
                                        />
                                    </div>

                                    <div class="col-12 col-lg-3">
                                        <button type="submit" class="btn btn-secondary w-100">
                                            <x-icon class="me-1">add</x-icon>
                                            <span>Tambah</span>
                                        </button>
                                    </div>
                                </div>

                                <hr class="mt-7">

                                <div class="row">
                                    <div class="col-12">
                                        <table id="cartDetailTable" class="table table-hover table-sm align-middle w-100">
                                            <thead>
                                                <tr>
                                                    <th class="text-nowrap text-start">No</th>
                                                    <th class="text-nowrap text-start">Produk</th>
                                                    <th class="text-nowrap text-end">Harga Beli</th>
                                                    <th class="text-nowrap text-end">Quantity</th>
                                                    <th class="text-nowrap text-end">Subtotal</th>
                                                    <th class="text-nowrap text-center">Opsi</th>
                                                </tr>
                                            </thead>
                
                                            <tbody>
                                                @foreach ($details as $detail)
                                                    <tr>
                                                        <td class="text-start">{{ number_format($loop->iteration) }}</td>
                                                        <td class="text-nowrap text-start">
                                                            <div class="row">
                                                                <div class="col-auto">
                                                                    <img
                                                                        src="{{ storage_image($detail->product->image) }}"
                                                                        alt="Product image"
                                                                        class="rounded"
                                                                        width="50"
                                                                        height="50"
                                                                    />
                                                                </div>
                                                                
                                                                <div class="col">
                                                                    <h4 class="fs-6 mb-1">{{ $detail->product->code }}</h4>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-nowrap text-end">Rp {{ number_format($detail->purchasePrice) }}</td>
                                                        <td class="text-nowrap text-end">{{ number_format($detail->quantity) }}</td>
                                                        <td class="text-nowrap text-end">Rp {{ number_format($detail->subtotalPrice) }}</td>
                                                        <td class="text-center">
                                                            <div class="btn-group w-100">
                                                                <button
                                                                    type="button"
                                                                    class="btn btn-light edit-cart-detail"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-title="Edit"
                                                                    data-id="{{ $detail->id }}"
                                                                    data-quantity="{{ $detail->quantity }}"
                                                                    data-purchase-price="{{ $detail->purchasePrice }}"
                                                                >
                                                                    <x-icon>edit</x-icon>
                                                                </button>
                                                                
                                                                <button
                                                                    type="button"
                                                                    class="btn btn-light delete-cart-detail"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-title="Hapus"
                                                                    data-id="{{ $detail->id }}"
                                                                >
                                                                    <x-icon>delete</x-icon>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Total dan form simpan pembelian --}}
    <div class="row mt-7">
        <div class="col-12 col-lg-6 col-xl-8 order-2 order-lg-1">
            <form action="{{ route('transactions.purchases.save') }}" method="post" id="formSavePurchase">
                @csrf @method('post')
                
                <div class="card shadow sm">
                    <div class="card-header">
                        <h3 class="fs-6 m-0">Pembayaran</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-6">
                                <x-form-input
                                    type="text"
                                    name="invoice_number"
                                    id="invoiceNumber"
                                    label="Nomor Invoice"
                                    placeholder="Nomor invoice dari pemasok..."
                                    error="{{ $errors->has('invoice_number') }}"
                                    value="{{ old('invoice_number') }}"
                                />
                            </div>

                            <div class="col-12 mb-6">
                                <x-form-select
                                    required
                                    name="supplier_id"
                                    id="supplierId"
                                    label="Pemasok"
                                    error="{{ $errors->has('supplier_id') }}"
                                    data-choices=""
                                >
                                    <option selected disabled>Pilih</option>
        
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                            {{ $supplier->code }} - {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </x-form-select>
                            </div>

                            <div class="col-12 mb-6">
                                <x-form-select
                                    required
                                    label="Metode Pembayaran"
                                    name="payment_method"
                                    id="paymentMethod"
                                    error="{{ $errors->has('payment_method') }}"
                                    data-choices=""
                                >
                                    <option disabled selected>Pilih</option>
                                    <option value="cash" @selected(old('payment_method') == 'cash')>Tunai</option>
                                    <option value="transfer" @selected(old('payment_method') == 'transfer')>Transfer</option>
                                    <option value="debit" @selected(old('payment_method') == 'debit')>Debit</option>
                                    <option value="credit" @selected(old('payment_method') == 'credit')>Kredit</option>
                                </x-form-select>
                            </div>

                            <div class="col-12 mb-6">
                                <x-form-input
                                    type="number"
                                    name="amount_paid"
                                    id="amountPaid"
                                    label="Jumlah yang dibayarkan"
                                    placeholder="Jumlah yang dibayarkan"
                                    error="{{ $errors->has('amount_paid') }}"
                                    value="{{ old('amount_paid') }}"
                                    class="no-spinners"
                                />
                            </div>

                            <div class="col-12">
                                <x-form-input
                                    type="textarea"
                                    name="notes"
                                    id="notes"
                                    label="Catatan"
                                    placeholder="Catatan"
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

        <div class="col-12 col-lg-6 col-xl-4 order-1 order-lg-2 mb-7 mb-lg-0">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h3 class="fs-6 m-0">Total</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush m-0 p-0">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-body-secondary">Total Quantity</span>
                                    <span class="fs-6 fw-bold">{{ number_format($cart->totalQuantity) }}</span>
                                </li>
    
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-body-secondary">Total Harga</span>
                                    <span class="fs-6 fw-bold">Rp {{ number_format($cart->totalPrice) }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal edit cart detail --}}
    <form action="#" method="post" id="formEditCartDetail" autocomplete="off">
        @csrf @method('patch')

        <div class="modal" tabindex="-1" id="modalEditCartDetail">
            <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-lg-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-5 mb-lg-0">
                                <x-form-input
                                    required
                                    type="number"
                                    name="purchase_price"
                                    id="editPurchasePrice"
                                    label="Harga Beli"
                                    class="no-spinners"
                                />
                            </div>

                            <div class="col-12 col-lg-6 mb-5 mb-lg-0">
                                <x-form-input
                                    required
                                    type="number"
                                    name="quantity"
                                    id="editQuantity"
                                    label="Quantity"
                                    class="no-spinners"
                                />
                            </div>
                        </div>
                    </div>
    
                    <div class="modal-footer">
                        <div class="row justify-content-end align-items-center w-100">
                            <div class="col-6 col-lg-3 col-xl-2">
                                <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">
                                    <x-icon class="me-1">close</x-icon>
                                    <span>Batal</span>
                                </button>
                            </div>

                            <div class="col-6 col-lg-3 col-xl-2">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <x-icon class="me-1">save</x-icon>
                                    <span>Simpan</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Form delete cart detail --}}
    <form action="#" method="post" id="formDeleteCartDetail" class="d-none">
        @csrf @method('delete')
    </form>

    <x-slot:script>
        {{-- Modal product table --}}
        <script>
            const modalProductTable = new bootstrap.Modal('#modalProductTable');
            let productTable = null;

            $('#buttonProductTable').click(function (e) { 
                e.preventDefault();
                modalProductTable.show();

                if (productTable === null) {
                    productTable = app.dataTable('#productTable', {
                        serverSide: true,
                        ajax: "{{ route('master.products.active') }}",
                        scrollY: "400px",
                        scrollX: false,
                        layout: {
                            topStart: 'pageLength',
                            topEnd: "search",
                            bottomStart: null,
                            bottomEnd: "paging",
                        },
                        order: {
                            name: 'code',
                            dir: 'asc',
                        },
                        columns: [
                            {
                                title: 'Gambar',
                                name: 'image',
                                data: 'image',
                                className: 'text-center',
                                type: 'html',
                                searchable: false,
                                orderable: false,
                                render: (data) => {
                                    if (data === null) {
                                        return `
                                            <img
                                                src="${app.baseUrl}/assets/images/empty.jpg"
                                                class="rounded"
                                                width="50"
                                                height="50"
                                                alt="Produk"
                                            />
                                        `;
                                    }

                                    return `
                                        <img
                                            src="${app.baseUrl}/storage/${data}"
                                            class="rounded"
                                            width="50"
                                            height="50"
                                            alt="Produk"
                                        />
                                    `;
                                }
                            },
                            {
                                title: 'Kode Produk',
                                name: 'code',
                                data: 'code',
                                type: 'string',
                                className: 'text-nowrap',
                            },
                            {
                                title: 'Barcode',
                                name: 'barcode',
                                data: 'barcode',
                                type: 'string',
                                className: 'text-nowrap',
                            },
                            {
                                title: 'Kategori',
                                name: 'category.name',
                                data: 'category.name',
                                type: 'string',
                                className: 'text-nowrap',
                            },
                            {
                                title: 'Unit',
                                name: 'unit.name',
                                data: 'unit.name',
                                type: 'string',
                                className: 'text-nowrap',
                            },
                            {
                                title: 'Harga Beli',
                                name: 'purchase_price',
                                data: 'purchase_price',
                                className: 'text-nowrap',
                                render: (data) => app.numberFormat(data),
                            },
                        ],
                        createdRow: (row, data, index) => {
                            $(row).addClass('cursor-pointer');

                            $(row).click(function (e) { 
                                e.preventDefault();
                                $('#productCode').val(data.code);
                                $('#purchasePrice').val(data.purchase_price);
                                modalProductTable.hide();
                            });
                        }
                    });
                } else {
                    productTable.ajax.reload(null, false);
                }
            });
        </script>

        {{-- Table cart detail --}}
        <script>
            const cartDetailTable = app.dataTable('#cartDetailTable', {
                scrollX: true,
                ordering: false,
                searching: false,
                info: false,
                lengthChange: false,
                pageLength: 5,
                fixedColumns: {
                    start: 0,
                    end: 1,
                }
            })
        </script>

        {{-- Edit cart detail --}}
        <script>
            const modalEditCartDetail = new bootstrap.Modal('#modalEditCartDetail');

            $('.edit-cart-detail').click(function (e) { 
                e.preventDefault();
                
                modalEditCartDetail.show();

                const id = $(this).data('id');
                const purchasePrice = $(this).data('purchase-price');
                const quantity = $(this).data('quantity');

                $('#editPurchasePrice').val(purchasePrice);
                $('#editQuantity').val(quantity);
                $('#formEditCartDetail').attr('action', `${app.baseUrl}/transactions/purchases/cart/${id}`);
            });
        </script>

        {{-- Delete cart detail --}}
        <script>
            $('.delete-cart-detail').click(function (e) { 
                e.preventDefault();

                app.delete('Produk', (result) => {
                    if (!result) {
                        return;
                    }

                    const id = $(this).data('id');
                    const formDeleteCartDetail = $('#formDeleteCartDetail');
                    const url = `${app.baseUrl}/transactions/purchases/cart/${id}`;

                    formDeleteCartDetail.attr('action', url);
                    formDeleteCartDetail.submit();
                });
            });
        </script>
    </x-slot:script>
</x-layouts.auth>