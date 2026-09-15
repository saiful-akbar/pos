<x-layouts.auth title="Pengembalian Penjualan">
    @isset($cart)
        <x-page-header icon="assignment_return" title="Pengembalian Penjualan">
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
                            
                            <input type="hidden" name="sale_detail_id">
                            <input type="hidden" name="product_id">

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
                                        </div>

                                        <div class="col-12 col-lg-4 col-xl-3 mt-5 mt-lg-0">
                                            <x-button type="submit" start-icon="add" full-width>
                                                Tambah
                                            </x-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4"></div>
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

        <x-slot:script>
            <script>
                class Service {
                    constructor() {
                        this.buttonDeleteCart = $('#buttonDeleteCart');
                        this.formDeleteCart = $('#formDeleteCart');
                        this.buttonProduct = $('#buttonProduct');
                        this.modalProduct = new bootstrap.Modal('#modalProduct');
                        this.tableProduct = this.tableProduct();
                        this.formCreateCartDetail = $('#formCreateCartDetail');
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
                                    this.formCreateCartDetail.find('input[name=sale_detail_id]').val(data.id);
                                    this.formCreateCartDetail.find('input[name=product_id]').val(data.product.id);
                                    this.formCreateCartDetail.find('input[name=product_code]').val(data.product.code);
                                    this.modalProduct.hide();
                                });
                            }
                        });
                    }
                }

                window.service = new Service();
            </script>

            {{-- Global script --}}
            <script>
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
            </script>
        </x-slot:script>
    @else
        <x-page-header icon="assignment_return" title="Pengembalian Penjualan">
            <x-slot:action>
                <div class="row justify-content-end align-items-center w-100">
                    <div class="col-12 col-lg-6 col-xl-3 order-2 order-lg-1">
                        <button type="submit" class="btn btn-secondary w-100" id="buttonCreateCart">
                            <x-icon class="me-1">assignment_return</x-icon>
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