<x-layouts.auth title="Pengembalian">
    {{-- Header halaman beserta tombol batalkan transaksi pengembalian --}}
    <x-page-header title="Pengembalian" icon="assignment_return">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-6 col-xl-4">
                    <form action="{{ route('transactions.returns.carts.delete', ['cartId' => $cart->id]) }}" method="post" id="formCancel">
                        @csrf @method('delete')

                        <x-button
                            type="button"
                            id="buttonCancel"
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

    {{-- Kolom kiri (form input produk + tabel detail) dan kolom kanan (informasi & total) sejajar --}}
    <div class="row mt-7 gx-7">
        <div class="col-12 col-xl-8">
            <div class="row gx-7">
                {{-- Form input/pilih kode produk untuk ditambahkan ke keranjang pengembalian --}}
                <div class="col-12">
                    <form action="{{ route('transactions.returns.carts.details.store', ['cartId' => $cart->id]) }}" method="post" id="formCreateCartDetail" autocomplete="off">
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

                {{-- Tabel daftar produk yang sudah ditambahkan ke keranjang pengembalian --}}
                <div class="col-12 mt-7">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <table id="cartDetailTable" class="table table-hover table-sm align-middle w-100"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Informasi pelanggan, tanggal transaksi penjualan, dan total pengembalian --}}
        <div class="col-12 col-xl-4 mt-7 mt-xl-0">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="fs-6 mb-0">Informasi</h3>
                </div>

                <div class="card-body">
                    <ul class="list-group list-group-flush m-0 p-0">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Pelanggan (Member)</span>
                            <span class="fw-bold ms-10 text-end" id="customer">-</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Tanggal Transaksi</span>
                            <span class="fw-bold ms-10 text-end" id="saleTransactionDate">-</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Qty</span>
                            <span class="fw-bold ms-10 text-end" id="totalQuantity">0</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Subtotal</span>
                            <span class="fw-bold ms-10 text-end" id="totalAmount">Rp 0</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Diskon</span>
                            <span class="fw-bold ms-10 text-end" id="discount">0%</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Grand Total</span>
                            <span class="fw-bold ms-10 text-end" id="grandTotal">Rp 0</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Form catatan (opsional) dan tombol untuk menyelesaikan transaksi pengembalian --}}
            <form id="formCreateSaleReturn" action="{{ route('transactions.returns.carts.complete', ['cartId' => $cart->id]) }}" method="post" autocomplete="off" class="mt-7">
                @csrf

                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="fs-6 mb-0">Catatan</h3>
                    </div>

                    <div class="card-body">
                        <x-form-input
                            type="textarea"
                            name="notes"
                            id="notes"
                            label="Catatan (opsional)"
                            placeholder="Masukan catatan..."
                            value="{{ old('notes') }}"
                            error="{{ $errors->has('notes') }}"
                        />
                    </div>

                    <div class="card-footer">
                        <x-button type="submit" color="secondary" startIcon="save" fullWidth>
                            Selesaikan Transaksi
                        </x-button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal produk pada faktur penjualan asli --}}
    <x-modal title="Produk" size="xl" fullScreen="md" id="modalProduct">
        <table id="productTable" class="table table-sm table-hover align-middle w-100"></table>
    </x-modal>

    {{-- Modal form edit quantity detail keranjang pengembalian --}}
    <form action="#" method="post" id="formEditCartDetailQuantity" autocomplete="off">
        @csrf @method('patch')

        <x-modal id="modalEditCartDetailQuantity" title="Edit Quantity" size="md" fullScreen="md">
            {{-- Kode & nama produk (hanya informasi, tidak bisa diubah) --}}
            <div class="mb-5">
                <x-form-input type="text" name="product" id="productEditQuantity" label="Produk" disabled />
            </div>

            {{-- Quantity baru yang akan disimpan --}}
            <div>
                <x-form-input type="number" name="quantity" id="quantityEdit" step="0.01" min="0.01" label="Quantity" required />
            </div>

            <x-slot:footer>
                <div class="w-100">
                    <div class="row">
                        <div class="col-6">
                            <x-button type="button" color="light" startIcon="close" data-bs-dismiss="modal" fullWidth>Batal</x-button>
                        </div>

                        <div class="col-6">
                            <x-button type="submit" color="secondary" startIcon="save" fullWidth>Simpan</x-button>
                        </div>
                    </div>
                </div>
            </x-slot:footer>
        </x-modal>
    </form>

    <x-slot:script>
        <script>
            "use strict"

            // Elemen-elemen utama halaman keranjang pengembalian.
            const formCancel = $('#formCancel');
            const buttonCancel = $('#buttonCancel');
            const formCreateCartDetail = $('#formCreateCartDetail');
            const buttonProduct = $('#buttonProduct');
            const modalProduct = new bootstrap.Modal('#modalProduct');
            const formEditCartDetailQuantity = $('#formEditCartDetailQuantity');
            const modalEditCartDetailQuantity = new bootstrap.Modal('#modalEditCartDetailQuantity');

            /**
             * Mengambil informasi pelanggan, tanggal transaksi penjualan, dan total keranjang pengembalian.
             */
            function findCartInfo() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'get',
                        url: "{{ route('transactions.returns.carts.info', ['cartId' => $cart->id]) }}",
                        dataType: 'json',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        success: (response) => {
                            resolve(response);

                            // Tampilkan data informasi yang diterima ke masing-masing elemen.
                            const data = response.data;

                            $('#customer').text(data.customer?.name ?? '-');
                            $('#saleTransactionDate').text(data.sale_transaction_date ?? '-');
                            $('#totalQuantity').text(app.numberFormat(data.total_quantity));
                            $('#totalAmount').text(`Rp ${app.numberFormat(data.total_amount)}`);
                            $('#discount').text(`${app.numberFormat(data.discount, 0)}%`);
                            $('#grandTotal').text(`Rp ${app.numberFormat(data.grand_total)}`);
                        },
                        error: (error) => reject(error.responseJSON),
                    });
                });
            }

            /**
             * Inisialisasi dataTables detail keranjang pengembalian penjualan.
             */
            const cartDetailTable = app.dataTable('#cartDetailTable', {
                serverSide: true,
                ajax: "{{ route('transactions.returns.carts.details', ['cartId' => $cart->id]) }}",
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
                        render: (data, type, row) => `${row.product_name} (${row.product_code})`,
                    },
                    {
                        title: 'Unit (Satuan)',
                        name: 'units.name',
                        data: 'unit_name',
                        type: 'string',
                        className: 'text-nowrap',
                        render: (data, type, row) => `${row.unit_name} (${row.unit_code})`,
                    },
                    {
                        title: 'Harga',
                        name: 'sale_return_cart_details.selling_price',
                        data: 'selling_price',
                        type: 'num',
                        className: 'text-nowrap',
                        render: (data) => `Rp ${app.numberFormat(data)}`,
                    },
                    {
                        title: 'Qty',
                        name: 'sale_return_cart_details.quantity',
                        data: 'quantity',
                        type: 'num',
                        className: 'text-nowrap',
                        render: (data) => app.numberFormat(data),
                    },
                    {
                        title: 'Subtotal',
                        name: 'sale_return_cart_details.subtotal',
                        data: 'subtotal',
                        type: 'num',
                        className: 'text-nowrap',
                        render: (data) => `Rp ${app.numberFormat(data)}`,
                    },
                    {
                        title: 'Diskon',
                        name: 'sale_return_cart_details.discount',
                        data: 'discount',
                        type: 'num',
                        className: 'text-nowrap',
                        render: (data) => `${app.numberFormat(data, 0)}%`,
                    },
                    {
                        title: 'Grand Total',
                        name: 'sale_return_cart_details.grand_total',
                        data: 'grand_total',
                        type: 'num',
                        className: 'text-nowrap',
                        render: (data) => `Rp ${app.numberFormat(data)}`,
                    },
                    {
                        title: app.icon('settings', 'fs-6'),
                        name: 'sale_return_cart_details.id',
                        data: 'id',
                        type: 'html',
                        className: 'text-nowrap text-center',
                        searchable: false,
                        orderable: false,
                        render: (data) => `
                            <button
                                type="button"
                                class="btn btn-light btn-sm rounded"
                                title="Edit"
                                data-action="edit"
                                data-id="${data}"
                            >
                                ${app.icon('edit_square', 'fs-6')}
                            </button>

                            <button
                                type="button"
                                class="btn btn-light btn-sm rounded"
                                title="Hapus"
                                data-action="delete"
                                data-id="${data}"
                            >
                                ${app.icon('delete', 'fs-6')}
                            </button>
                        `,
                    },
                ],
                createdRow: (row, data) => {
                    // Klik tombol edit: isi form edit quantity dengan data baris lalu tampilkan modal.
                    $(row).find('button[data-action="edit"]').click(function (e) {
                        e.preventDefault();
                        app.clearFormErrors(formEditCartDetailQuantity);

                        // Arahkan form ke endpoint update quantity untuk detail yang dipilih.
                        formEditCartDetailQuantity.attr('action', app.url(`/transactions/returns/carts/{{ $cart->id }}/details/${data.id}/quantity`));
                        formEditCartDetailQuantity.find('input[name="product"]').val(`${data.product_name} (${data.product_code})`);
                        formEditCartDetailQuantity.find('input[name="quantity"]').val(data.quantity);

                        modalEditCartDetailQuantity.show();
                    });

                    // Klik tombol hapus: tampilkan konfirmasi, lalu hapus detail jika disetujui.
                    $(row).find('button[data-action="delete"]').click(function (e) {
                        e.preventDefault();

                        app.delete('Produk', async function (confirmed) {
                            if (!confirmed) return;

                            app.showPreloader();

                            try {
                                // Kirim permintaan hapus ke endpoint deleteCartDetail secara ajax.
                                const response = await new Promise((resolve, reject) => {
                                    $.ajax({
                                        type: 'delete',
                                        url: app.url(`/transactions/returns/carts/{{ $cart->id }}/details/${data.id}`),
                                        data: {
                                            _token: app.csrfToken,
                                            _method: 'delete',
                                        },
                                        dataType: 'json',
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json',
                                        },
                                        success: (response) => resolve(response),
                                        error: (error) => reject(error.responseJSON),
                                    });
                                });

                                // Berhasil: reload tabel detail dan card informasi.
                                if (response.status === 200) {
                                    cartDetailTable.ajax.reload();
                                    await findCartInfo();
                                    app.alert('success', response.message);
                                }
                            } catch (error) {
                                app.alert('danger', error?.message ?? 'Terjadi kesalahan saat menghapus produk.');
                            } finally {
                                app.hidePreloader();
                            }
                        });
                    });
                },
            });

            /**
             * Batalkan transaksi pengembalian penjualan.
             */
            buttonCancel.click(function (e) {
                e.preventDefault();

                app.delete('Transaksi Pengembalian', function (confirmed) {
                    if (!confirmed) return;

                    formCancel.trigger('submit');
                });
            });

            /**
             * Inisialisasi dataTables produk berdasarkan faktur penjualan asli.
             */
            const productTable = app.modalDataTable('#productTable', {
                serverSide: true,
                scrollX: true,
                ajax: "{{ route('transactions.sales.details', ['saleId' => $cart->saleId]) }}",
                columns: [
                    {
                        title: 'Kode Produk',
                        name: 'products.code',
                        data: 'product_code',
                        type: 'string',
                        className: 'text-nowrap',
                    },
                    {
                        title: 'Nama (Deskripsi)',
                        name: 'products.name',
                        data: 'product_name',
                        type: 'string',
                        width: '300px',
                    },
                    {
                        title: 'Unit (Satuan)',
                        name: 'units.name',
                        data: 'unit_name',
                        type: 'string',
                        className: 'text-nowrap',
                        render: (data, type, row) => `${row.unit_name} (${row.unit_code})`,
                    },
                    {
                        title: 'Harga',
                        name: 'sale_details.selling_price',
                        data: 'selling_price',
                        type: 'num',
                        className: 'text-nowrap',
                        render: (data) => `Rp ${app.numberFormat(data)}`,
                    },
                    {
                        title: 'Qty Terjual',
                        name: 'sale_details.quantity',
                        data: 'quantity',
                        type: 'num',
                        className: 'text-nowrap',
                        render: (data) => app.numberFormat(data),
                    },
                ],
                createdRow: (row, data) => {
                    $(row).addClass('cursor-pointer');

                    // Klik baris: tutup modal, isi kode produk, lalu langsung submit form.
                    $(row).click(function (e) {
                        e.preventDefault();
                        modalProduct.hide();
                        formCreateCartDetail.find('[name="product_code"]').val(data.product_code);
                        formCreateCartDetail.trigger('submit');
                    });
                },
            });

            /**
             * Buka modal produk.
             */
            buttonProduct.click(function (e) {
                e.preventDefault();
                modalProduct.show();
            });

            /**
             * Submit form create cart detail.
             */
            formCreateCartDetail.submit(async function (e) {
                e.preventDefault();
                app.showPreloader();
                app.clearFormErrors(formCreateCartDetail);

                try {
                    // Kirim kode produk ke endpoint storeCartDetail secara ajax.
                    const response = await new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'post',
                            url: formCreateCartDetail.attr('action'),
                            data: formCreateCartDetail.serialize(),
                            dataType: 'json',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });

                    // Berhasil: reload tabel detail beserta informasi total, reset form, dan fokus kembali ke input kode produk.
                    if (response.status === 201) {
                        cartDetailTable.ajax.reload();
                        await findCartInfo();
                        formCreateCartDetail.trigger('reset');
                        formCreateCartDetail.find('[name="product_code"]').focus();
                        app.alert('success', response.message);
                        app.clearFormErrors(formCreateCartDetail);
                    }
                } catch (error) {
                    // Gagal validasi: tampilkan pesan error pada field terkait.
                    app.setFormErrors(formCreateCartDetail, error);
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Submit form edit quantity detail keranjang pengembalian.
             */
            formEditCartDetailQuantity.submit(async function (e) {
                e.preventDefault();
                app.showPreloader();
                app.clearFormErrors(formEditCartDetailQuantity);

                try {
                    // Kirim quantity baru ke endpoint updateCartDetailQuantity secara ajax (method patch).
                    const response = await new Promise((resolve, reject) => {
                        $.ajax({
                            type: 'post',
                            url: formEditCartDetailQuantity.attr('action'),
                            data: formEditCartDetailQuantity.serialize(),
                            dataType: 'json',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    });

                    // Berhasil: tutup modal lalu reload tabel detail dan card informasi.
                    if (response.status === 200) {
                        modalEditCartDetailQuantity.hide();
                        cartDetailTable.ajax.reload();
                        await findCartInfo();
                        app.alert('success', response.message);
                    }
                } catch (error) {
                    // Gagal validasi: tampilkan pesan error pada field quantity, modal tetap terbuka.
                    if (error?.errors) {
                        app.setFormErrors(formEditCartDetailQuantity, error);
                        return;
                    }

                    // Error lain (misal data tidak ditemukan): tutup modal dan tampilkan alert.
                    modalEditCartDetailQuantity.hide();
                    app.alert('danger', error?.message ?? 'Terjadi kesalahan saat memperbarui quantity.');
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Ambil informasi keranjang pengembalian saat halaman pertama kali dimuat.
             */
            $(document).ready(async function () {
                await findCartInfo();
            });
        </script>
    </x-slot:script>
</x-layouts.auth>
