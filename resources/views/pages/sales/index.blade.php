<x-layouts.auth title="POS">
    <x-page-header title="POS" icon="shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-6 col-xl-4">
                    @isset($cart)
                        <form action="{{ route('transactions.sales.cart.delete', ['cartId' => $cart->id]) }}" method="post">
                            @csrf @method('delete')

                            <button type="submit" class="btn btn-danger w-100">
                                <x-icon class="me-1">close</x-icon>
                                <span>Batalkan Transaksi</span>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('transactions.sales.cart.store') }}" method="post">
                            @csrf @method('post')

                            <button type="submit" class="btn btn-secondary w-100">
                                <x-icon class="me-1">shopping_cart</x-icon>
                                <span>Mulai Transaksi</span>
                            </button>
                        </form>
                    @endisset
                </div>
            </div>
        </x-slot:action>
    </x-page-header>

    @isset($cart)
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
                                                <input type="text" name="product" id="product" class="form-control"
                                                    placeholder="Masukan kode atau barcode produk..." required autofocus
                                                    readonly>

                                                <button type="button" class="btn btn-outline-secondary"
                                                    id="buttonProductTable" data-bs-toggle="tooltip"
                                                    data-bs-title="Pilih produk">
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
                                <span class="fs-6 fw-bold" id="totalQuantity">0</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-body-secondary">Subtotal</span>
                                <span class="fs-6 fw-bold" id="subtotal">0</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-body-secondary">Grand Total</span>
                                <span class="fs-6 fw-bold" id="grandTotal">0</span>
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
                                    <div class="form-floating">
                                        <select name="payment_method" id="payment_method" class="form-select" required>
                                            <option value="cash">Tunai</option>
                                            <option value="qris">Qris</option>
                                            <option value="debit">Kartu debit</option>
                                            <option value="credit">Kartu kredit</option>
                                        </select>

                                        <label for="payment_method">
                                            Metode pembayaran <span class="text-danger">*</span>
                                        </label>
                                    </div>

                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="col-12 mb-5">
                                    <div class="form-floating">
                                        <input type="number" name="paid_amount" id="paid_amount"
                                            class="form-control form-control-lg no-spinners" placeholder="Pembayaran" required>
                                        
                                            <label for="paid_amount">
                                                Pembayaran <span class="text-danger">*</span>
                                            </label>
                                    </div>

                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="number" name="change_amount" id="change_amount"
                                            class="form-control form-control-lg no-spinners" placeholder="Kembalian" readonly>
                                        
                                            <label for="change_amount">
                                                Kembalian
                                            </label>
                                    </div>

                                    <div class="invalid-feedback"></div>
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

            {{-- Modal product table --}}
            <script>
                class ModalProduct {
                    constructor() {
                        this.modal = new bootstrap.Modal('#modalProductTable');
                        this.table = this.dataTable();
                    }

                    /**
                     * Product dataTable
                     */
                    dataTable() {
                        return app.modalDataTable('#productTable', {
                            serverSide: true,
                            ajax: "{{ route('master.products.active') }}",
                            columns: [{
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

                                $(row).click((e) => {
                                    e.preventDefault();
                                    $('#product').val(data.code);
                                    this.hide();
                                });
                            }
                        });
                    }

                    /**
                     * Menmpilkan modal
                     */
                    show() {
                        this.table.ajax.reload();
                        this.modal.show();
                    }

                    /**
                     * Menyembunyikan modal
                     */
                    hide() {
                        this.modal.hide();
                    }
                }

                window.modalProduct = new ModalProduct();
            </script>

            {{-- Service --}}
            <script>
                class Service {
                    constructor() {
                        this.cartId = "{{ $cart->id }}";
                    }

                    find() {
                        $.ajax({
                            type: "get",
                            url: "{{ route('transactions.sales.cart.show', ['cartId' => $cart->id]) }}",
                            success: function(response) {
                                const {
                                    total_quantity: totalQuantity,
                                    subtotal: subtotal,
                                    grand_total: grandTotal
                                } = response.data;

                                $('#totalQuantity').text(totalQuantity.toLocaleString('id-ID'));
                                $('#totalQuantity').data('value', totalQuantity);
                                $('#subtotal').text(subtotal.toLocaleString('id-ID'));
                                $('#subtotal').data('value', subtotal);
                                $('#grandTotal').text(grandTotal.toLocaleString('id-ID'));
                                $('#grandTotal').data('value', grandTotal);
                            }
                        });
                    }
                }

                window.service = new Service();
            </script>

            {{-- Global script --}}
            <script>
                /**
                 * Mengisi nilai defaul pada form pembayaran jika
                 * metode pembayaran yang dipilih bukan cash (tunai).
                 */
                $('#payment_method').change(function(e) {
                    e.preventDefault();

                    if ($(this).val() !== 'cash') {
                        $('#paid_amount').attr('readonly', 'readonly');
                        $('#paid_amount').val($('#grandTotal').data('value'));
                        $('#paid_amount').removeClass('is-invalid');
                        $('#change_amount').val('');
                    } else {
                        $('#paid_amount').val("");
                        $('#paid_amount').removeAttr('readonly');
                    }
                });

                /**
                 * Update nilai kembalian ketika form
                 * pembayaran diinput.
                 */
                $('#paid_amount').keyup(function(e) {
                    e.preventDefault();

                    const grandTotal = $('#grandTotal').data('value');
                    const paidAmount = $(this).val();
                    const changeAmount = paidAmount - grandTotal;

                    if (changeAmount >= 0) {
                        $('#change_amount').val(changeAmount);
                        $('#paid_amount').removeClass('is-invalid');
                    } else {
                        $('#change_amount').val(0);
                        $('#paid_amount').addClass('is-invalid');
                    }
                });

                /**
                 * Membuka modal product table.
                 */
                $('#buttonProductTable').click(function(e) {
                    e.preventDefault();
                    modalProduct.show();
                });

                /**
                 * Request data cart
                 */
                $(document).ready(function() {
                    service.find();
                });
            </script>
        </x-slot:script>
    @else
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
    </x-layouts.auth>
