<x-layouts.auth title="Edit Pembelian">
    <x-page-header title="Edit Pembelian" icon="add_shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-6 col-xl-3">
                    <a href="{{ route('transactions.purchases') }}" class="btn btn-light w-100" role="button">
                        <x-icon class="me-1">arrow_back</x-icon>
                        <span>Kembali</span>
                    </a>
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
                                        <label for="productCode" class="form-label">Produk <span class="text-danger">*</span></label>

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
                                        <table id="purchaseDetailTable" class="table table-hover table-sm align-middle w-100">
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
                                                @foreach ($purchaseDetails as $detail)
                                                    <tr>
                                                        <td class="text-start">{{ number_format($loop->iteration) }}</td>
                                                        <td class="text-nowrap d-flex align-items-center">
                                                            <div class="me-3">
                                                                <img src="{{ storage_image($detail->product->image) }}" alt="Product image" class="rounded" width="50" height="50"/>
                                                            </div>

                                                            <div>
                                                                <h4 class="fs-6 mb-1">{{ $detail->product->code }}</h4>
                                                                <p class="text-body-secondary m-0 text-truncate" style="max-width: 250px;">{{ $detail->product->name }}</p>
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

    <x-slot:script>
        {{-- Table cart detail --}}
        <script>
            const purchaseDetailTable = app.dataTable('#purchaseDetailTable', {
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
            });
        </script>
    </x-slot:script>
</x-layouts.auth>