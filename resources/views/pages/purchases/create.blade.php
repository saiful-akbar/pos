<x-layouts.auth title="Tambah Pembelian">
    <x-page-header title="Tambah Pembelian" icon="add_shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end align-items-center w-100">
                <div class="col-12 col-lg-6 col-xl-3">
                    <a href="{{ route('transactions.purchases') }}" class="btn btn-light w-100" role="button">
                        <x-icon class="me-1">arrow_back</x-icon>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>
        </x-slot:action>
    </x-page-header>

    <div class="row mt-7">
        <div class="col-12 col-lg-8">
            <form action="{{ route('transactions.purchases.create.cart') }}" method="post" id="formAddCart" autocomplete="off">
                @csrf @method('post')
                
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="h5 mb-1">Tambah Produk</h3>
                        <p class="text-body-secondary m-0">Pilih dan tambahkan produk kedalam daftar</p>
                    </div>
                
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-12 col-lg-8 col-xl-9">
                                <div @class(['input-group', 'is-invalid' => $errors->has('product_code')])>
                                    <input
                                        autofocus
                                        required
                                        type="text"
                                        name="product_code"
                                        id="product_code"
                                        placeholder="Masukan kode atau barcode produk..."
                                        value="{{ old('product_code') }}"
                                        @class(['form-control', 'is-invalid' => $errors->has('product_code'), 'border' => !$errors->has('product_code')])
                                    />

                                    <button type="button" class="btn btn-outline-secondary" id="buttonModalProductTable">
                                        <x-icon class="fs-6">table</x-icon>
                                    </button>
                                </div>

                                @error('product_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-lg-4 col-xl-3 mt-5 mt-lg-0">
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

    <div class="modal" tabindex="-1" id="modalProductTable">
        <div class="modal-dialog modal-xl modal-fullscreen-md-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <table id="productTable" class="table table-hover align-middle w-100"></table>
                </div>
                
                <div class="modal-footer">
                    <div class="row justify-content-end w-100">
                        <div class="col-12 col-lg-6 col-xl-3">
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
            const baseUrl = app.baseUrl;
            const modalProductTable = new bootstrap.Modal('#modalProductTable');
            let productTable = null;

            $('#buttonModalProductTable').click(function (e) { 
                e.preventDefault();
                modalProductTable.show();

                if (productTable === null) {
                    productTable = app.dataTable('#productTable', {
                        serverSide: true,
                        ajax: "{{ route('master.products') }}",
                        scrollY: '500px',
                        layout: {
                            topStart: null,
                            topEnd: "search",
                            bottomStart: null,
                            bottomEnd: "paging",
                        },
                        columns: [
                            {
                                title: 'Gambar',
                                name: 'products.image',
                                data: 'image',
                                type: 'html',
                                searchable: false,
                                orderable: false,
                                render: (data) => {
                                    if (data === null) return `<img src="${app.baseUrl}/assets/images/empty.jpg" class="rounded border" width="50" height="50" />`
                                    return `<img src="${app.baseUrl}/storage/${data}" class="rounded border" width="50" height="50" />`
                                }
                            },
                            {
                                title: 'Kode Produk',
                                name: 'products.code',
                                data: 'code',
                                type: 'string',
                            },
                            {
                                title: 'Barcode',
                                name: 'products.barcode',
                                data: 'barcode',
                                type: 'string',
                            },
                            {
                                title: 'Nama (Deskripsi)',
                                name: 'products.name',
                                data: 'name',
                                type: 'string',
                            },
                            {
                                title: 'Kategory',
                                name: 'category.name',
                                data: 'category.name',
                                type: 'string',
                            },
                            {
                                title: 'Stok',
                                name: 'products.current_stock',
                                data: 'current_stock',
                                type: 'string',
                                render: (data) => app.numberFormat(data)
                            },
                        ],
                        order: {
                            name: 'products.code',
                            dir: 'asc',
                        },
                        createdRow: (row, data, index) => {
                            $(row).addClass('cursor-pointer');
                            
                            $(row).click(function (e) { 
                                e.preventDefault();
                                $('#product_code').attr('value', data.code);
                                modalProductTable.hide();
                            });
                        }
                    });
                } else {
                    productTable.ajax.reload(null, false);
                }
            });
        </script>
    </x-slot:script>
</x-layouts.auth>