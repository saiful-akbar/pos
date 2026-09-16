<x-layouts.auth title="Pengembalian">
    @isset($cart)
        <x-page-header icon="assignment_return" title="Pengembalian">
            <x-slot:action>
                <div class="row justify-content-end align-items-center w-100">
                    <div class="col-12 col-lg-6 col-xl-4">
                        <button type="button" class="btn btn-danger w-100" id="buttonDeleteCart">
                            <x-icon class="me-1">close</x-icon>
                            <span>Batalkan Transaksi</span>
                        </button>

                        <form action="{{ route('transactions.sales.return.carts.delete') }}" method="post" id="formDeleteCart">
                            @csrf @method('delete')
                        </form>
                    </div>
                </div>
            </x-slot:action>
        </x-page-header>

        <div class="row">
            <div class="col-12 col-lg-8 mt-6">
                <div class="row">
                    <div class="col-12">
                        <form action="#" method="post" id="formCreateCartDetail" autocomplete="off">
                            @csrf @method('post')
                            
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-lg-8 col-xl-9">
                                            <div class="input-group">
                                                <x-form-input type="text" name="product_code" id="productCode"
                                                    placeholder="Masukan kode atau barcode produk..."
                                                    required autofocus />

                                                <x-button id="buttonProduct" title="Pilih produk" outline>
                                                    <x-icon class="text-secondary">table</x-icon>
                                                </x-button>
                                            </div>

                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <div class="col-12 col-lg-4 col-xl-3 mt-5 mt-lg-0">
                                            <x-button type="submit" start-icon="add" full-width>Tambah</x-button>
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
                                <table id="tableCartDetail" class="table table-hover align-middle w-100"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4 mt-6">
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('transactions.sales.return.store') }}" method="post" id="formCreateReturn">
                            @csrf @method('post')

                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <h3 class="fs-6 m-0">Total</h3>
                                </div>

                                <div class="card-body">
                                    <div class="row">
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
    
                                        <div class="col-12">
                                            <x-form-floating-input
                                                type="textarea"
                                                name="notes"
                                                id="notes"
                                                label="Catatan"
                                                error="{{ $errors->has('notes') }}"
                                                value="{{ old('notes') }}"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <x-button type="submit" start-icon="save" full-width>
                                        Simpan Transaksi
                                    </x-button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal product --}}
        <div class="modal" tabindex="-1" id="modalProduct">
            <div class="modal-dialog modal-xl modal-dialog-scrollable modal-fullscreen-lg-down">
                <div class="modal-content border">
                    <div class="modal-header">
                        <h5 class="modal-title">Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <table id="tableProduct" class="table table-hover align-middle w-100"></table>
                    </div>

                    <div class="modal-footer">
                        <div class="row justify-content-end w-100">
                            <div class="col-12 col-lg-4 col-xl-2">
                                <button type="reset" class="btn btn-light w-100" data-bs-dismiss="modal">
                                    <x-icon class="me-1">close</x-icon>
                                    <span>Batal</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal edit quantity return cart detail --}}
        <form action="#" method="post" autocomplete="off" id="formEditCartDetail">
            @csrf
            @method('patch')
            
            <div class="modal" tabindex="-1" id="modalEditCartDetail">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content border">
                        <div class="modal-header">
                            <h5 class="modal-title">Ubah Quantity</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
    
                        <div class="modal-body">
                            <x-form-input type="number" name="quantity" id="editQuantity" placeholder="Masukan quantity" step="any" min="0.01" required />
                            <div class="invalid-feedback"></div>
                        </div>
    
                        <div class="modal-footer">
                            <div class="row justify-content-end w-100">
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

        <x-slot:script>
            <script>
                class Service {
                    constructor() {
                        this.csrfToken = $('meta[name="csrf-token"]').attr('content');
                        this.buttonDeleteCart = $('#buttonDeleteCart');
                        this.formDeleteCart = $('#formDeleteCart');
                        this.buttonProduct = $('#buttonProduct');
                        this.modalProduct = new bootstrap.Modal('#modalProduct');
                        this.tableProduct = this.tableProduct();
                        this.formCreateCartDetail = $('#formCreateCartDetail');
                        this.tableCartDetail = this.tableCartDetail();
                        this.formEditCartDetail = $('#formEditCartDetail');
                        this.modalEditCartDetail = new bootstrap.Modal('#modalEditCartDetail');
                    }

                    /**
                     * Modal product table.
                     */
                    tableProduct() {
                        return app.modalDataTable('#tableProduct', {
                            serverSide: true,
                            ajax: "{{ route('transactions.sales.details.table', ['saleId' => $cart->saleId]) }}",
                            columns: [
                                {
                                    title: 'Kode',
                                    name: 'product.code',
                                    data: 'product.code',
                                    className: 'text-nowrap',
                                    type: 'string',
                                    render: (data, type, row) => {
                                        return `
                                            <div class="d-flex align-items-center">
                                                <img src="${app.storageImage(row.product.image)}" class="rounded avatar" alt="Produk" />
                                                <div class="ms-4">
                                                    <div>${row.product.code}</div>
                                                    <div class="text-body-secondary text-truncate" style="max-width: 200px;">${row.product.name}</div>
                                                </div>
                                            </div>
                                        `;
                                    }
                                },
                                {
                                    title: 'Harga',
                                    name: 'selling_price',
                                    data: 'selling_price',
                                    className: 'text-nowrap',
                                    type: 'num',
                                    render: (data, type, row) => `Rp ${app.numberFormat(data)}`,
                                },
                                {
                                    title: 'Quantity',
                                    name: 'quantity',
                                    data: 'quantity',
                                    className: 'text-nowrap',
                                    type: 'num',
                                    render: (data, type, row) => {
                                        return `
                                            <span>${app.numberFormat(data)}</span>
                                            <span class="fs-sm text-body-secondary">${row.product.unit.code}</span>
                                        `;
                                    },
                                },
                                {
                                    title: 'Diskon',
                                    name: 'discount',
                                    data: 'discount',
                                    className: 'text-nowrap',
                                    type: 'num',
                                    render: (data, type, row) => `${app.numberFormat(data)} %`,
                                },
                                {
                                    title: 'Subtotal',
                                    name: 'subtotal',
                                    data: 'subtotal',
                                    className: 'text-nowrap',
                                    type: 'num',
                                    render: (data, type, row) => `Rp ${app.numberFormat(data)}`,
                                },
                            ],
                            createdRow: (row, data, index) => {
                                $(row).addClass('cursor-pointer');
                                
                                $(row).click((e) => { 
                                    e.preventDefault();
                                    this.formCreateCartDetail.find('input[name=product_code]').val(data.product.code);
                                    this.formCreateCartDetail.submit();
                                    this.modalProduct.hide();
                                });
                            }
                        });
                    }

                    /**
                     * Tabel return cart detail
                     */
                    tableCartDetail() {
                        return app.dataTable('#tableCartDetail', {
                            serverSide: true,
                            ajax: "{{ route('transactions.sales.return.carts.details') }}",
                            ordering: false,
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
                                    render: (data, type, row) => {
                                        return `
                                            <div class="d-flex align-items-center">
                                                <img src="${app.storageImage(row.product.image)}" class="rounded avatar" alt="Produk" />
                                                <div class="ms-4">
                                                    <div>${row.product.code}</div>
                                                    <div class="text-body-secondary text-truncate" style="max-width: 100px;">${row.product.name}</div>
                                                </div>
                                            </div>
                                        `;
                                    }
                                },
                                {
                                    title: 'Harga',
                                    name: 'selling_price',
                                    data: 'selling_price',
                                    type: 'num',
                                    className: 'text-nowrap',
                                    render: (data) => `Rp ${app.numberFormat(data)}`,
                                },
                                {
                                    title: 'Qty',
                                    name: 'quantity',
                                    data: 'quantity',
                                    type: 'num',
                                    className: 'text-nowrap',
                                    render: (data, type, row) => {
                                        return `
                                            <span>${app.numberFormat(data)}</span>
                                            <span class="fs-sm text-body-secondary ms-1">${row.product.unit.code}</span>
                                        `
                                    },
                                },
                                {
                                    title: 'Diskon',
                                    name: 'discount',
                                    data: 'discount',
                                    type: 'num',
                                    className: 'text-nowrap',
                                    render: (data) => `${app.numberFormat(data)} %`,
                                },
                                {
                                    title: 'Subtotal',
                                    name: 'subtotal',
                                    data: 'subtotal',
                                    type: 'num',
                                    className: 'text-nowrap',
                                    render: (data) => `Rp ${app.numberFormat(data)}`,
                                },
                                {
                                    title: `<span class="material-symbols-outlined">settings</span>`,
                                    name: 'id',
                                    data: 'id',
                                    type: 'html',
                                    className: 'text-nowrap text-center',
                                    ordering: false,
                                    searching: false,
                                    render: (data, type, row) => {
                                        return `
                                            <div class="btn-group w-100">
                                                <button type="button" class="btn btn-light w-100" title="edit" data-action="edit">
                                                    <span class="material-symbols-outlined">edit</span>
                                                </button>
                                                <button type="button" class="btn btn-light w-100" title="edit" onclick="deleteCartDetail(event, '${data}')">
                                                    <span class="material-symbols-outlined">delete</span>
                                                </button>
                                            </div>
                                        `;
                                    },
                                },
                            ],
                            createdRow: (row, data, index) => {
                                $(row).find('button[data-action="edit"]').click((e) => { 
                                    e.preventDefault();
                                    this.formEditCartDetail.attr('action', `${app.baseUrl}/transactions/sales/return/carts/details/${data.id}`);
                                    this.formEditCartDetail.find('input[name="quantity"]').val(data.quantity);
                                    this.formEditCartDetail.find('input[name="quantity"]').focus();
                                    this.modalEditCartDetail.show();
                                });
                            }
                        })
                    }

                    /**
                     * Mengambil data return cart.
                     */
                    findCart() {
                        return new Promise((resolve, reject) => {
                            $.ajax({
                                type: "get",
                                url: "{{ route('transactions.sales.return.carts') }}",
                                success: function (response) {
                                    $('#totalQuantity').text(app.numberFormat(response.data.total_quantity));
                                    $('#totalAmount').text(`Rp ${app.numberFormat(response.data.total_amount)}`);
                                }
                            });
                        })
                    }

                    /**
                     * Create return cart detail
                     */
                    createCartDetail() {
                        return new Promise((resolve, reject) => {
                            $.ajax({
                                type: "post",
                                url: "{{ route('transactions.sales.return.carts.details.store') }}",
                                data: this.formCreateCartDetail.serialize(),
                                dataType: "json",
                                success: (response) => resolve(response),
                                error: (error) => reject(error.responseJSON),
                            });
                        })
                    }

                    /**
                     * Delete return cart detail
                     */
                    deleteCartDetail(id) {
                        return new Promise((resolve, reject) => {
                            $.ajax({
                                type: "delete",
                                url: `${app.baseUrl}/transactions/sales/return/carts/details/${id}`,
                                data: {
                                    _token: this.csrfToken,
                                },
                                dataType: "json",
                                success: (response) => resolve(response),
                                error: (error) => reject(error.responseJSON),
                            });
                        })
                    }

                    /**
                     * Update quantity return cart detail.
                     */
                    updateCartDetail(id) {
                        return new Promise((resolve, reject) => {
                            $.ajax({
                                type: "patch",
                                url: this.formEditCartDetail.attr('action'),
                                data: this.formEditCartDetail.serialize(),
                                dataType: "json",
                                success: (response) => resolve(response),
                                error: (error) => reject(error.responseJSON),
                            });
                        })
                    }
                }

                window.service = new Service();
            </script>

            {{-- Global script --}}
            <script>
                $(document).ready(function () {
                    service.findCart();
                });

                /**
                 * Hapus (batal) transaksi
                 */
                service.buttonDeleteCart.click((e) => {
                    e.preventDefault();

                    app.delete('Transaksi', (deleted) => {
                        if (!deleted) return;
                        service.formDeleteCart.submit();
                    });
                });

                /**
                 * Menampilkan modal product table
                 */
                service.buttonProduct.click(function (e) { 
                    e.preventDefault();
                    service.modalProduct.show();
                    service.tableProduct.ajax.reload();
                });

                /**
                 * Submit form create return cart detail.
                 */
                service.formCreateCartDetail.submit(async function (e) { 
                    e.preventDefault();
                    
                    service.formCreateCartDetail.find(`input[name=product_code]`).removeClass('is-invalid');
                    service.formCreateCartDetail.find(`input[name=product_code]`).parent().removeClass('is-invalid');
                    service.formCreateCartDetail.find(`input[name=product_code]`).parent().next().text('');
                    
                    try {
                        const response = await service.createCartDetail();
                        
                        service.formCreateCartDetail.trigger('reset');
                        service.formCreateCartDetail.find(`input[name=product_code]`).focus();
                        service.tableCartDetail.ajax.reload();
                        service.findCart();
                    } catch (error) {
                        app.alert('danger', error.message);
                        
                        Object.entries(error.errors).forEach(([field, message]) => {
                            service.formCreateCartDetail.find(`input[name=${field}]`).focus();
                            service.formCreateCartDetail.find(`input[name=${field}]`).addClass('is-invalid');
                            service.formCreateCartDetail.find(`input[name=${field}]`).parent().addClass('is-invalid');
                            service.formCreateCartDetail.find(`input[name=${field}]`).parent().next().text(message[0]);
                        });
                    }
                });

                /**
                 * Fungsi untuk menghapus return cart detail
                 */
                const deleteCartDetail = (e, id) => {
                    e.preventDefault();

                    app.delete('Produk', async (deleted) => {
                        if (!deleted) return;

                        try {
                            const response = await service.deleteCartDetail(id);

                            service.tableCartDetail.ajax.reload();
                            service.findCart();
                            app.alert('success', response.message);
                        } catch (error) {
                            app.alert('danger', error.message);
                        } finally {
                            service.formCreateCartDetail.find('input[name="product_code"]').focus();
                        }
                    })
                }

                /**
                 * Update quantity return cart detail
                 */
                service.formEditCartDetail.submit(async (e) => { 
                    e.preventDefault();
                    
                    app.showPreloader();
                    service.formEditCartDetail.find('input[name="quantity"]').removeClass('is-invalid');
                    service.formEditCartDetail.find('input[name="quantity"]').next().text('');

                    try {
                        const response = await service.updateCartDetail();

                        app.alert('success', response.message);
                        service.modalEditCartDetail.hide();
                        service.tableCartDetail.ajax.reload();
                        service.findCart();
                    } catch (error) {
                        Object.entries(error.errors).forEach(([field, message]) => {
                            service.formEditCartDetail.find(`input[name=${field}]`).addClass('is-invalid');
                            service.formEditCartDetail.find(`input[name=${field}]`).next().text(message[0]);
                        })
                    } finally {
                        app.hidePreloader();
                        service.formCreateCartDetail.find('input[name="product_code"]').focus();
                    }
                });
            </script>
        </x-slot:script>
    @else
        <x-page-header icon="assignment_return" title="Pengembalian">
            <x-slot:action>
                <div class="row justify-content-end align-items-center w-100">
                    <div class="col-12 col-lg-6 col-xl-3 order-2 order-lg-1">
                        <button type="submit" class="btn btn-secondary w-100" id="buttonCreateCart">
                            <x-icon class="me-1">add</x-icon>
                            <span>Mulai Transaksi</span>
                        </button>

                        <form action="{{ route('transactions.sales.return.carts.store') }}" method="post" id="formCreateCart">
                            @csrf @method('post')

                            <div class="modal" tabindex="-1" id="modalCreateCart">
                                <div class="modal-dialog">
                                    <div class="modal-content border">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Nomor Invoice (Faktur)</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <x-form-input type="text" name="invoice_number" id="invoiceNumber" class="border" placeholder="Masukan nomor invoice..." required autofocus/>
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
                                                        <x-icon class="me-1">send</x-icon>
                                                        <span>Kirim</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-12 col-lg-6 col-xl-3 mb-5 mb-lg-0 order-1 order-lg-2">
                        <a href="{{ route('transactions.sales') }}" class="btn btn-light w-100" role="button">
                            <x-icon class="me-1">arrow_back</x-icon>
                            <span>Kembali</span>
                        </a>
                    </div>
                </div>
            </x-slot:action>
        </x-page-header>

        <div class="empty-cart">
            <img src="{{ asset('assets/images/empty_cart.svg') }}" alt="empty cart" class="img-fluid">
            <h1 class="fs-3 mt-7">Belum ada transaksi</h1>
        </div>

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

        <x-slot:script>
            <script>
                class Service {
                    constructor() {
                        this.buttonCreateCart = $('#buttonCreateCart');
                        this.formCreateCart = $('#formCreateCart');
                        this.modalCreateCart = new bootstrap.Modal('#modalCreateCart');
                    }
                }

                window.service = new Service();
            </script>

            {{-- Global script --}}
            <script>
                service.buttonCreateCart.click((e) => {
                    e.preventDefault();
                    service.modalCreateCart.show();
                    service.formCreateCart.find('input[name=invoice_number]').focus()
                });
            </script>
        </x-slot:script>
    @endisset
</x-layouts.auth>