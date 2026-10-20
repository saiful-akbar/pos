<x-layouts.auth title="Detail Pembelian" :access="$access">
    <x-page-header title="Detail Pembelian" icon="shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-6 col-xl-4">
                    <x-button
                        type="button"
                        color="danger"
                        startIcon="close"
                        id="buttonClose"
                        fullWidth
                    >
                        Tutup
                    </x-button>
                </div>
            </div>
        </x-slot:action>
    </x-page-header>

    <div class="row gx-7 mt-7">
        <div class="col-12 col-xl-4">
            <div class="card shadow-sm position-sticky" style="top: 20px;">
                <div class="card-header">
                    <h3 class="fs-6 mb-0">Summary</h3>
                </div>

                <div class="card-body">
                    <ul class="list-group list-group-flush m-0 p-0">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">User</span>
                            <span class="fw-bold ms-10 text-end" id="user">{{ $purchase->user->fullName }}</span>
                        </li>
                        
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Pemasok</span>
                            <span class="fw-bold ms-10 text-end" id="supplier">{{ $purchase->supplier->name }}</span>
                        </li>
                        
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Tanggal</span>
                            <span class="fw-bold ms-10 text-end" id="transactionDate">{{ $purchase->transactionDate->format('Y-m-d') }}</span>
                        </li>
                        
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">No. Faktur</span>
                            <span class="fw-bold ms-10 text-end" id="invoiceNumber">{{ $purchase->invoiceNumber }}</span>
                        </li>
                        
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Status Pembayaran</span>
                            <span class="fw-bold ms-10 text-end" id="paymentStatus">{{ $purchase->paymentStatus->value }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Total Produk</span>
                            <span class="fw-bold ms-10 text-end" id="totalProduct">{{ number_format($purchase->totalProduct) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Total Qty</span>
                            <span class="fw-bold ms-10 text-end" id="totalQuantity">{{ number_format($purchase->totalQuantity, 2) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Total Harga</span>
                            <span class="fw-bold ms-10 text-end" id="totalPrice">{{ number_format($purchase->totalPrice, 2) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Kurang Bayar</span>
                            <span class="fw-bold ms-10 text-end" id="underpayment">
                                @php
                                    $underpayment = $purchase->totalPrice - $purchase->alreadyPaid;
                                @endphp

                                {{ number_format($underpayment <= 0 ? 0 : $underpayment, 2) }}
                            </span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-body-secondary">Catatan</span>
                            <span class="fw-bold ms-10 text-end" id="notes">{{ $purchase->notes }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8 mt-7 mt-xl-0">
            <div class="row gx-7">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h3 class="fs-6 mb-0">Detail Produk</h3>
                        </div>

                        <div class="card-body">
                            <table id="detailTable" class="table table-sm table-hover align-middle w-100"></table>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-7">
                    <div class="card shadow-sm">
                        <div class="card-header w-100">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-12 col-lg-9 col-xl-8">
                                    <h3 class="fs-6 mb-0">Pembayaran</h3>
                                </div>
                                
                                @if ($access?->create ?? false)
                                    <div class="col-12 col-lg-3 mt-5 mt-lg-0">
                                        <x-button startIcon="add" id="buttonCreatePayment" fullWidth>
                                            Tambah
                                        </x-button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            <table id="paymentTable" class="table table-sm table-hover align-middle w-100"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($access?->create ?? false)
        <form action="{{ route('transactions.purchases.payments.store', ['purchaseId' => $purchase->id]) }}" method="post" autocomplete="off" id="formCreatePayment">
            @csrf @method('post')

            <x-modal id="modalCreatePayment" title="Tambah Pembayaran" fullScreen="md">
                <div class="row">

                    <div class="col-12 mb-5">
                        <x-form-input
                            required
                            type="text"
                            name="payment_date"
                            id="paymentDate"
                            label="Tanggal pembayaran"
                            placeholder="Pilih tanggal"
                            error="{{ $errors->has('payment_date') }}"
                            value="{{ old('payment_date', date('Y-m-d')) }}"
                            data-flatpickr=""
                        />
                    </div>

                    <div class="col-12 mb-5">
                        <x-form-select
                            required
                            label="Metode pembayaran"
                            placeholder="Metode Pembayaran"
                            name="payment_method"
                            id="paymentMethod"
                            error="{{ $errors->has('payment_method') }}"
                        >
                            <option disabled selected>Pilih</option>
                            <option value="cash" @selected(old('payment_method') == 'cash')>Tunai</option>
                            <option value="transfer" @selected(old('payment_method') == 'transfer')>Transfer</option>
                            <option value="debit" @selected(old('payment_method') == 'debit')>Debit</option>
                            <option value="credit" @selected(old('payment_method') == 'credit')>Kredit</option>
                        </x-form-select>
                    </div>

                    <div class="col-12 mb-5">
                        <x-form-input
                            required
                            type="number"
                            name="amount"
                            id="amount"
                            label="Jumlah"
                            placeholder="Masukan jumlah yang dibayarakan..."
                            error="{{ $errors->has('amount') }}"
                            value="{{ old('amount') }}"
                            step="any"
                        />
                    </div>

                    <div class="col-12">
                        <x-form-input
                            type="textarea"
                            name="notes"
                            id="notes"
                            label="Catatan"
                            placeholder="Masukan catatan..."
                            error="{{ $errors->has('notes') }}"
                            value="{{ old('notes') }}"
                        />
                    </div>
                </div>

                <x-slot:footer>
                <div class="row">
                    <div class="col-6">
                        <x-button type="button" color="light" startIcon="close" data-bs-dismiss="modal" fullWidth>
                            Batal
                        </x-button>
                    </div>

                    <div class="col-6">
                        <x-button type="submit" color="secondary" startIcon="save" fullWidth>
                            Simpan
                        </x-button>
                    </div>
                </div>
            </x-slot:footer>
            </x-modal>
        </form>
    @endif

    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.purchaseId = "{{ $purchase->id }}"
                    this.buttonClose = $('#buttonClose');
                    this.access = this.#getAccess();
                    this.detailTable = this.#detailDataTableInit();
                    this.paymentTable = this.#paymentDataTableInit();
                    this.buttonCreatePayment = $('#buttonCreatePayment');
                    this.formCreatePayment = $('#formCreatePayment');
                    this.modalCreatePayment = this.#modalCreatePaymentInit();
                }

                /**
                 * Mengambil hak akses user.
                 */
                #getAccess() {
                    return {
                        read: $('html').data('read') === 1,
                        create: $('html').data('create') === 1,
                        update: $('html').data('update') === 1,
                        remove: $('html').data('remove') === 1,
                        delete: $('html').data('delete') === 1,
                    }
                }

                /**
                 * Inisialisasi dataTable detail
                 */
                #detailDataTableInit() {
                    return app.dataTable('#detailTable', {
                        serverSide: true,
                        ajax: "{{ route('transactions.purchases.details', ['purchaseId' => $purchase->id]) }}",
                        columns: [
                            {
                                title: 'Produk',
                                name: 'product.name',
                                data: 'product.name',
                                type: 'string',
                                className: 'text-nowrap',
                                render: (data, type, row) => `${row.product.name} (${row.product.code})`
                            },
                            {
                                title: 'Unit (Satuan)',
                                name: 'unit.name',
                                data: 'unit.name',
                                type: 'string',
                                className: 'text-nowrap',
                                render: (data, type, row) => `${row.unit.name} (${row.unit.code})`
                            },
                            {
                                title: 'Qty',
                                name: 'quantity',
                                data: 'quantity',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data, type, row) => app.numberFormat(data),
                            },
                            {
                                title: 'Harga',
                                name: 'price',
                                data: 'price',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data, type, row) => app.numberFormat(data),
                            },
                            {
                                title: 'Subtotal',
                                name: 'subtotal',
                                data: 'subtotal',
                                type: 'num',
                                className: 'text-nowrap',
                                render: (data, type, row) => app.numberFormat(data),
                            },
                        ],
                    })
                }

                /**
                 * Inisialisasi dataTable payment
                 */
                #paymentDataTableInit() {
                    const columns = [
                        {
                            title: 'Tanggal',
                            name: 'payment_date',
                            data: 'payment_date',
                            type: 'date',
                            className: 'text-nowrap',
                        },
                        {
                            title: 'Metode Pembayaran',
                            name: 'payment_method',
                            data: 'payment_method',
                            type: 'string',
                            className: 'text-nowrap',
                        },
                        {
                            title: 'Nominal',
                            name: 'amount',
                            data: 'amount',
                            type: 'num',
                            className: 'text-nowrap',
                            render: (data, type, row) => app.numberFormat(data),
                        },
                        {
                            title: 'Catatan',
                            name: 'notes',
                            data: 'notes',
                            type: 'string',
                            width: '200px',
                        },
                    ];

                    if (this.access.delete) {
                        columns.push({
                            title: app.icon('settings', 'fs-6'),
                            name: 'id',
                            data: 'id',
                            type: 'html',
                            className: 'text-center text-nowrap no-export',
                            searchable: false,
                            orderable: false,
                            render: (data) => {
                                return `
                                    <button
                                        type="button"
                                        class="btn btn-light"
                                        title="Hapus"
                                        onclick="deletePayment(event, '${data}')"
                                    >
                                        ${app.icon('delete', 'fs-6')}
                                    </button>
                                `;
                            }
                        })
                    }
                    return app.dataTable('#paymentTable', {
                        columns,
                        serverSide: true,
                        ajax: "{{ route('transactions.purchases.payments', ['purchaseId' => $purchase->id]) }}",
                        fixedColumns: {
                            start: 0,
                            end: this.access.delete ? 1 : 0,
                        },
                    });
                }

                /**
                 * Inisialisasi modal create payment
                 */
                #modalCreatePaymentInit() {
                    if ($('#modalCreatePayment').length === 0) {
                        return null;
                    }

                    return new bootstrap.Modal('#modalCreatePayment');
                }

                /**
                 * Mengambil data pembelian
                 */
                findPurchase() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "get",
                            url: app.url(`/transactions/purchases/${this.purchaseId}`),
                            data: { _token: app.csrfToken },
                            success: (response) => {
                                resolve(response);

                                $('#user').text(response.data.user.full_name);
                                $('#supplier').text(response.data.supplier.name);
                                $('#transactionDate').text(response.data.transaction_date);
                                $('#invoiceNumber').text(response.data.invoice_number);
                                $('#paymentStatus').text(response.data.payment_status);
                                $('#totalProduct').text(app.numberFormat(response.data.total_product, 0));
                                $('#totalQuantity').text(app.numberFormat(response.data.total_quantity, 2));
                                $('#totalPrice').text(app.numberFormat(response.data.total_price, 2));
                                $('#notes').text(response.data.notes);
                                
                                const underpayment = response.data.total_price - response.data.already_paid;
                                
                                $('#underpayment').text(app.numberFormat(underpayment <= 0 ? 0 : underpayment));
                            },
                            error: (error) => {
                                reject(error.responseJSON)
                            },
                        });
                    })
                }

                /**
                 * Create payment
                 */
                createPayment() {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "post",
                            url: this.formCreatePayment.attr('action'),
                            data: this.formCreatePayment.serialize(),
                            success: (response) => resolve(response),
                            error: (error) => reject(error.responseJSON),
                        });
                    })
                }

                /**
                 * Delete payment
                 */
                deletePayment(paymentId) {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "delete",
                            url: app.url(`/transactions/purchases/${this.purchaseId}/payments/${paymentId}`),
                            data: { _token: app.csrfToken },
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
             * Mengambil data pembelian
             */
            $(document).ready(async function () {
                try {
                    await service.findPurchase();
                } catch (error) {
                    app.alert('danger', error.message);
                }
            });

            /**
             * Close tab
             */
            service.buttonClose.click(function (e) { 
                e.preventDefault();
                window.close();
            });

            /**
             * Buka modal create payment
             */
            service.buttonCreatePayment.click(function (e) { 
                e.preventDefault();
                app.clearFormErrors(service.formCreatePayment);
                service.formCreatePayment.trigger('reset');
                service.modalCreatePayment?.show();
            });

            /**
             * Submit form create payment
             */
            service.formCreatePayment.submit(async function (e) { 
                e.preventDefault();
                app.clearFormErrors(service.formCreatePayment);
                app.showPreloader();

                try {
                    const response = await service.createPayment();

                    if (response.status === 201) {
                        app.alert('success', response.data);
                        service.paymentTable.ajax.reload();
                        service.modalCreatePayment?.hide();

                        await service.findPurchase();
                    }
                } catch (error) {
                    app.setFormErrors(service.formCreatePayment, error);
                } finally {
                    app.hidePreloader();
                }
            });

            /**
             * Delete payment
             */
            const deletePayment = (event, paymentId) => {
                event.preventDefault();

                app.delete('Pembayaran', async (deleted) => {
                    if (!deleted) return;

                    app.showPreloader();

                    try {
                        const response = await service.deletePayment(paymentId);

                        if (response.status === 200) {
                            app.alert('success', response.message);
                            service.paymentTable.ajax.reload();
                            
                            await service.findPurchase();
                        }
                    } catch (error) {
                        app.alert('danger', error.message);
                    } finally {
                        app.hidePreloader();
                    }
                })
            }
        </script>
    </x-slot:script>
</x-layouts.auth>