<x-layouts.auth title="POS">
    <x-page-header title="POS" icon="shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-6 col-xl-4">
                    <form action="#" method="post" id="formCreateCart">
                        @csrf @method('post')

                        <button type="submit" class="btn btn-secondary w-100">
                            <x-icon class="me-1">shopping_cart</x-icon>
                            <span>Mulai Transaksi</span>
                        </button>
                    </form>
                </div>
            </div>
        </x-slot:action>
    </x-page-header>
    
    @isset ($cart)
        <x-slot:style>
            <style>
                .empty-cart {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    flex-direction: column;
                    min-height: 70dvh;
                }

                .empty-cart img {
                    width: 400px;
                    height: 400px;
                }
                
                @media screen and (max-width: 600px) {
                    .empty-cart {
                        min-height: 50dvh;
                    }

                    .empty-cart img {
                        width: 200px;
                        height: 200px;
                    }
                }
            </style>
        </x-slot:style>

        <div class="empty-cart">
            <img src="{{ asset('assets/images/empty_cart.svg') }}" alt="empty cart" class="img-fluid">
            <h1 class="fs-3 mt-7">Belum ada transaksi</h1>
        </div>
    @endif

    <div class="row mt-6">
        <div class="col-12 col-xl-8 mb-6 mb-xl-0">
            <div class="row">
                <div class="col-12">
                    <form action="#" method="post" id="formCreateCartDetail" autocomplete="off">
                        @csrf @method('post')

                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-8 col-xl-9 mb-6 mb-lg-0">
                                        <div class="input-group @error('product') is-invalid @enderror">
                                            <input type="text" name="product" id="product" value="{{ old('product') }}"
                                                placeholder="Masukan kode atau barcode produk..."
                                                class="form-control @error('product') is-invalid @enderror"
                                                required autofocus>

                                            <button type="button" class="btn btn-outline-secondary" id="buttonProductTable"
                                                data-bs-toggle="tooltip" data-bs-title="Pilih produk">
                                                <x-icon class="text-secondary">table</x-icon>
                                            </button>
                                        </div>

                                        @error('product')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-lg-4 col-xl-3">
                                        <button type="submit" class="btn btn-secondary w-100">
                                            <x-icon class="me-1">add</x-icon>
                                            <span>Tambah</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mt-6">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="table-responsive">
                            <table id="cartDetailTable" class="table align-middle w-100 m-0">
                                <thead>
                                    <tr>
                                        <th class="text-start text-nowrap">Produk</th>
                                        <th class="text-start text-nowrap">Qty</th>
                                        <th class="text-start text-nowrap">Harga</th>
                                        <th class="text-start text-nowrap">Diskon</th>
                                        <th class="text-start text-nowrap">Subtotal</th>
                                        <th class="text-start text-nowrap"><x-icon>settings</x-icon></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td colspan="6" class="text-center text-body-secondary">
                                            Belum ada produk dalam daftar
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <section class="card shadow-sm mb-6">
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Total Qty</span>
                            <span class="fs-6 fw-bold">10</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Subtotal</span>
                            <span class="fs-6 fw-bold">{{ number_format(50000) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Grand Total</span>
                            <span class="fs-6 fw-bold" id="grandTotalAmount" data-value="50000">{{ number_format(50000) }}</span>
                        </li>
                    </ul>
                </div>
            </section>
            
            <form action="" method="post" autocomplete="off" id="formSaveSale">
                @csrf @method('post')
                
                <section class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="fs-6 m-0">Pembayaran</h3>
                    </div>
    
                    <div class="card-body">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-12 mb-5">
                                <x-form-select
                                    required
                                    label="Metode Pembayaran"
                                    name="payment_method"
                                    id="paymentMethod"
                                    error="{{ $errors->has('payment_method') }}"
                                    data-choices=""
                                >
                                    <option value="cash" @selected(old('payment_method', 'cash') == 'cash')>Tunai</option>
                                    <option value="qris" @selected(old('payment_method', 'cash') == 'qris')>Qris</option>
                                    <option value="credit" @selected(old('payment_method', 'cash') == 'credit')>Kartu Kredit</option>
                                    <option value="debit" @selected(old('payment_method', 'cash') == 'debit')>Kartu Debit</option>
                                </x-form-select>
                            </div>
    
                            <div class="col-12 mb-5">
                                <x-form-input
                                    required
                                    type="number"
                                    name="paid_amount"
                                    id="paidAmount"
                                    label="Jumlah yang dibayarkan"
                                    placeholder="Masukan nominal..."
                                    error="{{ $errors->has('paid_amount') }}"
                                    value="{{ old('paid_amount') }}"
                                />
                            </div>
    
                            <div class="col-12">
                                <x-form-input
                                    readonly
                                    type="number"
                                    name="change_amount"
                                    id="changeAmount"
                                    label="Kembalian"
                                    error="{{ $errors->has('change_amount') }}"
                                    value="{{ old('change_amount', 0) }}"
                                />
                            </div>
                        </div>
                    </div>
    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-secondary w-100">
                            <x-icon class="me-1">save</x-icon>
                            <span>Selesaikan Transaksi</span>
                        </button>
                    </div>
                </section>
            </form>
        </div>
    </div>

    {{-- Modal product table --}}
    <div class="modal" tabindex="-1" id="modalProductTable">
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-md-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <table id="productTable" class="table table-hover table-sm align-middle w-100"></table>
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

    <x-slot:script>
        <script>
            const paymentMethodEl = $('#paymentMethod');
            const paidAmountEl = $('#paidAmount');
            const changeAmountEl = $('#changeAmount');
            
            /**
             * Mengisi nilai defaul pada form pembayaran jika
             * metode pembayaran yang dipilih bukan cash (tunai).
             */
            paymentMethodEl.change(function (e) { 
                e.preventDefault();

                if ($(this).val() !== 'cash') {
                    paidAmountEl.attr('readonly', 'readonly');
                    paidAmountEl.val($('#grandTotalAmount').data('value'));
                } else {
                    paidAmountEl.val("");
                    paidAmountEl.removeAttr('readonly');
                }
            });
            
            /**
             * Update nilai kembalian ketika form
             * pembayaran diinput.
             */
            paidAmountEl.change(function (e) { 
                e.preventDefault();
                
                const grandTotalMount = $('#grandTotalAmount').data('value');
                const paidAmount = $(this).val();
                const changeAmount = paidAmount - grandTotalMount;

                if (changeAmount >= 0) {
                    changeAmountEl.val(changeAmount);
                    paidAmountEl.removeClass('border-danger');
                } else {
                    changeAmountEl.val(0);
                    paidAmountEl.addClass('border-danger');
                }
            });
        </script>

        {{-- Modal product datatable --}}
        <script>
            const modalProductTable = new bootstrap.Modal('#modalProductTable');

            const productTable = app.dataTable('#productTable', {
                serverSide: true,
                ajax: "{{ route('master.products.active') }}",
                scrollY: "400px",
                scrollX: false,
                ordering: false,
                columns: [
                    {
                        title: 'Kode',
                        name: 'code',
                        data: 'code',
                        className: 'text-nowrap',
                        type: 'string',
                        render: (data, type, row) => {
                            return `
                                <div class="d-flex align-items-center">
                                    <div class="avatar">
                                        <img src="${app.storageImage(row.image)}" class="avatar-img" width="50" height="50" alt="Produk" />
                                    </div>
                                    <div class="ms-4">
                                        <div>${row.code}</div>
                                        <div class="fs-sm text-body-secondary text-truncate" style="max-width: 200px;">${row.name}</div>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        title: 'Barcode',
                        name: 'barcode',
                        data: 'barcode',
                        type: 'string',
                        className: 'text-nowrap',
                    },
                    {
                        title: 'Harga',
                        name: 'selling_price',
                        data: 'selling_price',
                        className: 'text-nowrap',
                        type: 'num',
                        render: (data) => app.numberFormat(data),
                    },
                    {
                        title: 'Diskon',
                        name: 'discount',
                        data: 'discount',
                        className: 'text-nowrap',
                        type: 'num',
                        render: (data) => `${app.numberFormat(data)}%`,
                    },
                    {
                        title: 'Stok',
                        name: 'current_stock',
                        data: 'current_stock',
                        className: 'text-nowrap',
                        type: 'num',
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

            $('#buttonProductTable').click(function (e) { 
                e.preventDefault();
                modalProductTable.show();
                productTable.ajax.reload();
            });
        </script>
    </x-slot:script>
</x-layouts.auth>