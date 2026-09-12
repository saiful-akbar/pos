<x-layouts.auth title="POS">
    <x-page-header title="POS" icon="shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-6 col-xl-4">
                    @isset($cart)
                        <form action="{{ route('transactions.sales.carts.delete', ['cartId' => $cart->id]) }}" method="post">
                            @csrf @method('delete')

                            <button type="submit" class="btn btn-danger w-100">
                                <x-icon class="me-1">close</x-icon>
                                <span>Batalkan Transaksi</span>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('transactions.sales.carts.store') }}" method="post">
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
            <div class="col-12 col-lg-8 mb-6 mb-lg-0">
                <div class="row">
                    <div class="col-12">
                        <form action="#" method="post" id="formCreateCartDetail" autocomplete="off">
                            @csrf @method('post')

                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-lg-8 col-xl-9 mb-6 mb-lg-0">
                                            <div class="input-group">
                                                <input type="text" name="product" id="product" class="form-control"
                                                    placeholder="Masukan kode atau barcode produk" required autofocus/>

                                                <button type="button" class="btn btn-outline-secondary input-group-text"
                                                    id="buttonProductTable" data-bs-toggle="tooltip"
                                                    data-bs-title="Pilih produk">
                                                    <x-icon class="text-secondary">table</x-icon>
                                                </button>
                                            </div>

                                            <div class="invalid-feedback" id="productError"></div>
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
                            <div class="card-body">
                                <table id="cartDetailTable" class="table table-hover table-img align-middle w-100"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <form method="{{ route('transactions.sales.store') }}" method="post" autocomplete="off" id="formSaveSale">
                    @csrf @method('post')

                    <input type="hidden" name="cart_id" value="{{ $cart->id }}">

                    <section class="card shadow-sm mb-6">
                        <div class="card-header">
                            <h3 class="fs-6 m-0">Pelanggan</h3>
                        </div>

                        <div class="card-body">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-12 mb-5">
                                    <div class="form-floating">
                                        <input type="hidden" name="customer_id">
                                        <input type="text" name="customer" id="customer" class="form-control cursor-pointer" placeholder="pelanggan" readonly>
                                        <label for="customer">Pelanggan</label>
                                    </div>

                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea name="notes" id="notes" placeholder="Catatan" cols="30" rows="50" class="form-control"></textarea>
                                        <label for="notes">Catatan</label>
                                    </div>

                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="card shadow-sm">
                        <div class="card-header">
                            <h3 class="fs-6 m-0">Pembayaran</h3>
                        </div>

                        <div class="card-body">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-12">
                                    <div class="bg-light rounded p-5">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">Total Qty</span>
                                            <span class="fs-6 fw-bold" id="totalQuantity">0</span>
                                        </div>

                                        <hr class="my-3">
    
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">Grand Total</span>
                                            <span class="fs-6 fw-bold" id="totalAmount">0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 my-4">
                                    <hr>
                                </div>

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
                                        <input type="number" name="paid_amount" id="paid_amount" class="form-control form-control-lg no-spinners" placeholder="Pembayaran" required>
                                        <label for="paid_amount">Pembayaran <span class="text-danger">*</span></label>
                                    </div>

                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="number" name="change_amount" id="change_amount" class="form-control form-control-lg no-spinners" placeholder="Kembalian" readonly>
                                        <label for="change_amount">Kembalian</label>
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
        <div class="modal" tabindex="-1" id="productModal">
            <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-md-down">
                <div class="modal-content border">
                    <div class="modal-header">
                        <h5 class="modal-title">Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <table id="productTable" class="table table-hover table-sm table-img align-middle w-100"></table>
                    </div>

                    <div class="modal-footer">
                        <div class="row justify-content-end w-100">
                            <div class="col-12 col-lg-4 col-xl-2">
                                <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">
                                    <x-icon class="me-1">close</x-icon>
                                    <span>Batal</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal update quantity cart detail --}}
        <form id="formUpdateCartDetail" method="post">
            @csrf @method('patch')
            
            <div class="modal" tabindex="-1" id="modalUpdateCartDetail">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content border">
                        <div class="modal-header">
                            <h5 class="modal-title">Ubah Quantity</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
    
                        <div class="modal-body">
                            <input type="hidden" name="cart_detail_id" class="d-none">
                            <input type="number" name="quantity" placeholder="Masukan quantity..." class="form-control form-control-lg" min="1" required>
                            <div class="invalid-feedback"></div>
                        </div>
    
                        <div class="modal-footer">
                            <div class="row w-100">
                                <div class="col-6">
                                    <button type="reset" class="btn btn-light w-100" data-bs-dismiss="modal">
                                        <x-icon class="me-1">close</x-icon>
                                        <span>Batal</span>
                                    </button>
                                </div>

                                <div class="col-6">
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

        {{-- Modal tabel pelanggan --}}
        <div class="modal" tabindex="-1" id="customerModal">
            <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-md-down">
                <div class="modal-content border">
                    <div class="modal-header">
                        <h5 class="modal-title">Pelanggan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <table id="customerTable" class="table table-hover table-sm table-img align-middle w-100"></table>
                    </div>

                    <div class="modal-footer">
                        <div class="row justify-content-end w-100">
                            <div class="col-12 col-lg-4 col-xl-2">
                                <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">
                                    <x-icon class="me-1">close</x-icon>
                                    <span>Batal</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-slot:script>
            <script>
                class Service {
                    constructor() {
                        this.cartId = "{{ $cart->id }}";
                        this.productModal = new bootstrap.Modal('#productModal');
                        this.productTable = this.productTable();
                        this.cartDetailTable = this.cartDetailTable();
                        this.csrfToken = $('meta[name="csrf-token"]').attr('content');
                        this.modalUpdateCartdetail = new bootstrap.Modal('#modalUpdateCartDetail');
                        this.formUpdateCartDetail = $('#formUpdateCartDetail');
                        this.formCreateCartDetail = $('#formCreateCartDetail');
                        this.formSaveSale = $('#formSaveSale');
                        this.customerModal = new bootstrap.Modal('#customerModal');
                        this.customerTable = this.customerTable();
                        this.formSaveSale = $('#formSaveSale')
                    }

                    /**
                     * Product dataTable
                     */
                    productTable() {
                        return app.modalDataTable('#productTable', {
                            serverSide: true,
                            ajax: "{{ route('master.products.active') }}",
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
                                                    <img src="${app.storageImage(row.image)}" class="rounded" alt="Produk" />
                                                </div>
                                                <div class="ms-4">
                                                    <div>${row.code}</div>
                                                    <div class="fs-sm text-body-secondary text-truncate" style="max-width: 150px;">
                                                        ${row.name}
                                                    </div>
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
                                    title: 'Stok',
                                    name: 'current_stock',
                                    data: 'current_stock',
                                    className: 'text-nowrap',
                                    type: 'num',
                                    render: (data) => app.numberFormat(data),
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
                            ],
                            createdRow: (row, data, index) => {
                                $(row).addClass('cursor-pointer');

                                $(row).click((e) => {
                                    e.preventDefault();
                                    this.formCreateCartDetail.find('input[name=product]').val(data.code);
                                    this.productModal.hide();
                                });
                            }
                        });
                    }

                    /**
                     * Customer dataTable
                     */
                    customerTable() {
                        return app.modalDataTable('#customerTable', {
                            serverSide: true,
                            ajax: "{{ route('master.customers') }}",
                            columns: [
                                {
                                    title: 'Nama',
                                    name: 'name',
                                    data: 'name',
                                    className: 'text-nowrap',
                                    type: 'string',
                                },
                                {
                                    title: 'No. Telp',
                                    name: 'phone_number',
                                    data: 'phone_number',
                                    className: 'text-nowrap',
                                    type: 'string',
                                },
                                {
                                    title: 'Alamat',
                                    name: 'address',
                                    data: 'address',
                                    type: 'string',
                                },
                            ],
                            createdRow: (row, data, index) => {
                                $(row).addClass('cursor-pointer');

                                $(row).click((e) => {
                                    e.preventDefault();

                                    this.formSaveSale.find('input[name=customer_id]').val(data.id);
                                    this.formSaveSale.find('input[name=customer]').val(data.name);
                                    this.customerModal.hide();
                                });
                            }
                        });
                    }

                    /**
                     * Cart detail table
                     */
                    cartDetailTable() {
                        return app.modalDataTable('#cartDetailTable', {
                            serverSide: true,
                            ajax: "{{ route('transactions.sales.carts.details', ['cartId' => $cart->id]) }}",
                            searching: false,
                            ordering: false,
                            scrollY: false,
                            fixedColumns: {
                                start: 0,
                                end: 1,
                            },
                            columns: [
                                {
                                    title: 'Produk',
                                    name: 'product.code',
                                    data: 'product.code',
                                    className: 'text-nowrap',
                                    type: 'string',
                                    render: (data, type, row) => {
                                        return `
                                            <div class="d-flex align-items-center">
                                                <div class="avatar">
                                                    <img src="${app.storageImage(row.product.image)}" class="rounded" alt="Produk" />
                                                </div>
                                                <div class="ms-4">
                                                    <div>${row.product.code}</div>
                                                    <div class="fs-sm text-body-secondary text-truncate" style="max-width: 150px;">
                                                        ${row.product.name}
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    }
                                },
                                {
                                    title: 'Qty',
                                    name: 'quantity',
                                    data: 'quantity',
                                    className: 'text-nowrap',
                                    type: 'num',
                                    render: (data, type, row) => {
                                        return `${app.numberFormat(data)} <span class="text-body-secondary">${row.product.unit.code}</span>`;
                                    },
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
                                    render: (data) => `${app.numberFormat(data)} %`,
                                },
                                {
                                    title: 'Subtotal',
                                    name: 'subtotal',
                                    data: 'subtotal',
                                    className: 'text-nowrap',
                                    type: 'num',
                                    render: (data) => app.numberFormat(data),
                                },
                                {
                                    className: 'text-nowrap',
                                    data: 'id',
                                    type: 'html',
                                    render: (data, type, row) => {
                                        return `
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-light" title="Edit" data-action="update">
                                                    <span class="material-symbols-outlined">edit</span>
                                                </button>
                                                <button type="button" class="btn btn-light" title="Hapus" onclick="deleteCartDetail(event, '${data}')">
                                                    <span class="material-symbols-outlined">delete</span>
                                                </button>
                                            </div>
                                        `;
                                    }
                                }
                            ],
                            createdRow: (row, data, index) => {
                                $(row).find('button[data-action="update"]').click((e) => {
                                    e.preventDefault();

                                    const actionUrl = `${app.baseUrl}/transactions/sales/carts/${this.cartId}/details/${data.id}`;

                                    this.formUpdateCartDetail.attr('action', actionUrl);
                                    this.formUpdateCartDetail.find('input[name=quantity]').val(data.quantity);
                                    this.formUpdateCartDetail.find(`input[name=quantity]`).removeClass('is-invalid');
                                    this.formUpdateCartDetail.find(`input[name=quantity]`).next().text('');
                                    this.modalUpdateCartdetail.show();
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
                                url: `${app.baseUrl}/transactions/sales/carts/${this.cartId}`,
                                success: (response) => {
                                    const cart = response.data;
                                    const totalQuantityEl = $('#totalQuantity');
                                    const totalAmountEl = $('#totalAmount');

                                    totalQuantityEl.text(cart.total_quantity.toLocaleString('id-ID'));
                                    totalQuantityEl.data('value', cart.total_quantity);
                                    totalAmountEl.text(cart.total_amount.toLocaleString('id-ID'));
                                    totalAmountEl.data('value', cart.total_amount);

                                    resolve(response)
                                },
                                error: (error) => {
                                    app.alert('danger', error.message);
                                    reject(error.responseJSON);
                                },
                            });
                        });
                    }

                    /**
                     * Menambah cart detail.
                     */
                    createCartDetail() {
                        const data = $('#formCreateCartDetail').serialize();
                        const url = `${app.baseUrl}/transactions/sales/carts/${this.cartId}/details`

                        return new Promise((resolve, reject) => {
                            $.ajax({
                                type: "post",
                                url: url,
                                data: data,
                                dataType: "json",
                                success: (response) => resolve(response),
                                error: (error) => reject(error.responseJSON),
                            });
                        });
                    }

                    /**
                     * Hapus produk dari daftar keranjang
                     */
                    deleteCartDetail(cartDetailId) {
                        return new Promise((resolve, reject) => {
                            $.ajax({
                                type: "delete",
                                url: `${app.baseUrl}/transactions/sales/carts/${this.cartId}/details/${cartDetailId}`,
                                data: {
                                    _token: this.csrfToken,
                                },
                                dataType: "json",
                                success: function (response) {
                                    resolve(response);
                                },
                                error: function (error) {
                                    reject(error.responseJSON);
                                },
                            });
                        })
                    }

                    /**
                     * Update quantity cart detail
                     */
                    updateCartDetail() {
                        return new Promise((resolve, reject) => {
                            const data = this.formUpdateCartDetail.serialize();
                            const url = this.formUpdateCartDetail.attr('action');

                            $.ajax({
                                type: "patch",
                                url: url,
                                data: data,
                                dataType: "json",
                                success: (response) => resolve(response),
                                error: (error) => reject(error.responseJSON),
                            });
                        });
                    }

                    /**
                     * Menyimpan data penjualan
                     */
                    saveSale() {
                        const action = this.formSaveSale.attr('action');
                        const values = this.formSaveSale.serialize();

                        return new Promise((resolve, reject) => {
                            $.ajax({
                                type: "post",
                                url: action,
                                data: values,
                                dataType: "json",
                                success: (response) => resolve(response),
                                error: (error) => reject(error.responseJSON),
                            });
                        });
                    }
                }

                service = new Service();
            </script>

            {{-- Global script --}}
            <script>
                $(document).ready(function () {
                    service.findCart();
                });

                /**
                 * Mengisi nilai defaul pada form pembayaran jika
                 * metode pembayaran yang dipilih bukan cash (tunai).
                 */
                service.formSaveSale.find('select[name=payment_method]').change(function(e) {
                    e.preventDefault();

                    const inputPaidAmount = service.formSaveSale.find('input[name=paid_amount]');
                    const inputChangeAmount = service.formSaveSale.find('input[name=change_amount]');
                    const totalAmount = $('#totalAmount').data('value');

                    if ($(this).val() !== 'cash') {
                        inputPaidAmount.attr('readonly', 'readonly');
                        inputPaidAmount.removeClass('is-invalid');
                        inputPaidAmount.val(totalAmount);
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
                service.formSaveSale.find('input[name=paid_amount]').keyup(function(e) {
                    e.preventDefault();

                    const totalAmount = $('#totalAmount').data('value');
                    const paidAmount = $(this).val();
                    const changeAmount = paidAmount - totalAmount;
                    const inputChangeAmount = service.formSaveSale.find('input[name=change_amount]');
                    const inputPaidAmount = service.formSaveSale.find('input[name=paid_amount]');

                    if (changeAmount >= 0) {
                        inputChangeAmount.val(changeAmount);
                        inputPaidAmount.removeClass('is-invalid');
                    } else {
                        inputChangeAmount.val(0);
                        inputPaidAmount.addClass('is-invalid');
                    }
                });

                /**
                 * Membuka modal product table.
                 */
                $('#buttonProductTable').click(function(e) {
                    e.preventDefault();
                    service.productModal.show();
                });

                /**
                 * Menampilkan modal daftar pelanggan (customer)
                 */
                service.formSaveSale.find('input[name=customer]').click(function (e) { 
                    e.preventDefault();
                    service.customerModal.show();
                });

                /**
                 * Submit form create cart detail.
                 */
                service.formCreateCartDetail.submit(async function (e) { 
                    e.preventDefault();
                    
                    $(this).find('input[name="product"]').removeClass('is-invalid border-danger');
                    $(this).find('input[name="product"]').parent().removeClass('is-invalid');
                    $(this).find('input[name="product"]').parent().next().text('');
                    
                    try {
                        const response = await service.createCartDetail();

                        app.alert('success', response.message);
                        service.findCart();
                        service.cartDetailTable.ajax.reload();

                        $(this).trigger('reset');
                        $(this).find('input[name="product"]').focus();
                    } catch (error) {
                        Object.entries(error.errors).forEach(([field, message]) => {
                            $(this).find('input[name="product"]').addClass('is-invalid border-danger');
                            $(this).find('input[name="product"]').parent().addClass('is-invalid');
                            $(this).find('input[name="product"]').parent().next().text(message[0]);
                        })
                    }
                });

                /**
                 * Submit form update cart detail.
                 */
                service.formUpdateCartDetail.submit(async (event) => {
                    event.preventDefault();
                    app.showPreloader();

                    service.formUpdateCartDetail.find(`input[name=quantity]`).removeClass('is-invalid');
                    service.formUpdateCartDetail.find(`input[name=quantity]`).next().text('');
                    
                    try {
                        const response = await service.updateCartDetail();
                        
                        service.findCart();
                        service.cartDetailTable.ajax.reload();
                        service.modalUpdateCartdetail.hide();
                        app.alert('success', response.message);
                    } catch (error) {
                        console.error(error);
                        
                        Object.entries(error.errors).forEach(([field, message]) => {
                            service.formUpdateCartDetail.find(`input[name=${field}]`).addClass('is-invalid');
                            service.formUpdateCartDetail.find(`input[name=${field}]`).next().text(message[0]);
                        });

                        app.alert('danger', error.message);
                    } finally {
                        app.hidePreloader();
                    }
                });

                /**
                 * Menghapus daftar cart detail
                 */
                const deleteCartDetail = (event, id) => {
                    event.preventDefault();

                    app.delete('Produk', async (deleted) => {
                        if (!deleted) return;

                        try {
                            const response = await service.deleteCartDetail(id);

                            service.findCart()
                            service.cartDetailTable.ajax.reload();
                            app.alert('success', response.message);
                        } catch (error) {
                            app.alert('danger', error.message);
                        }
                    })
                }

                /**
                 * Simpan penjualan
                 */
                service.formSaveSale.submit(async function(event) {
                    event.preventDefault();

                    app.showPreloader();
                    service.formSaveSale.find('.is-invalid').removeClass('is-invalid');

                    try {
                        await service.saveSale();
                        open("{{ route('transactions.sales') }}");
                        location.reload();
                    } catch (error) {
                        Object.entries(error.errors).forEach(([field, message]) => {
                            service.formSaveSale.find(`#${field}`).addClass('is-invalid');
                            service.formSaveSale.find(`#${field}`).parent().addClass('is-invalid');
                            service.formSaveSale.find(`#${field}`).parent().next().text(message[0]);
                        });

                        app.hidePreloader();
                        app.alert('danger', error.message);
                    }
                })
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
