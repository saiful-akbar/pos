<x-layouts.auth title="Pembayaran Pembelian">
    <x-page-header title="Pembayaran Pembelian" icon="payments">
        <x-slot:action>
            <div class="row justify-content-end align-items-center">
                <div class="col-12 col-lg-3">
                    <a href="{{ route('transactions.purchases.detail', ['purchaseId' => $purchase->id]) }}" class="btn btn-light w-100" role="button">
                        <x-icon class="me-1">arrow_back</x-icon>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>
        </x-slot:action>
    </x-page-header>

    <div class="row mt-6">
        <div class="col-12 col-lg-6 col-xl-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="fs-6 m-0">Informasi Pembelian</h3>
                </div>

                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="text-body-secondary">Pemasok</span>
                            <span>{{ $purchase->supplier->name }}</span>
                        </li>
                        
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="text-body-secondary">Tanggal</span>
                            <span>{{ $purchase->purchaseDate->format('d M Y') }}</span>
                        </li>
                        
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="text-body-secondary">Nomor Faktur</span>
                            <span>{{ $purchase->invoiceNumber }}</span>
                        </li>
                        
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="text-body-secondary">Total Qty</span>
                            <span>{{ number_format($purchase->totalQuantity) }}</span>
                        </li>
                        
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="text-body-secondary">Total</span>
                            <span>Rp {{ number_format($purchase->totalPrice) }}</span>
                        </li>
                        
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="text-body-secondary">Sudah Dibayar</span>
                            <span>Rp {{ number_format($totalPaymentAmount) }}</span>
                        </li>

                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="text-body-secondary">Kurang Bayar</span>
                            <span>Rp {{ number_format($totalPaymentAmount - $purchase->totalPrice) }}</span>
                        </li>
                        
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="text-body-secondary">Status Pembayaran</span>
                            <span>{{ $purchase->paymentStatus->value }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6 col-xl-8 mt-6 mt-lg-0">
            <div class="row">
                <div class="col-12">
                    <form action="{{ route('transactions.purchases.payments.create', ['purchaseId' => $purchase->id]) }}" method="post" autocomplete="off">
                        @csrf @method('post');

                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h3 class="fs-6 m-0">Tambah Pembayaran</h3>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-lg-6 mb-6">
                                        <x-form-floating-input
                                            required
                                            type="text"
                                            name="payment_date"
                                            id="payment_date"
                                            label="Tanggal Pembayaran"
                                            placeholder="Tanggal Pembayaran"
                                            error="{{ $errors->has('payment_date') }}"
                                            value="{{ old('payment_date', date('Y-m-d')) }}"
                                            data-flatpickr=""
                                        />
                                    </div>

                                    <div class="col-12 col-lg-6 mb-6">
                                        <x-form-floating-select
                                            required
                                            type="text"
                                            name="payment_method"
                                            id="payment_method"
                                            label="Metode Pembayaran"
                                            placeholder="Metode Pembayaran"
                                            error="{{ $errors->has('payment_method') }}"
                                            value="{{ old('payment_method', date('Y-m-d')) }}"
                                        >
                                            <option disabled selected>Pilih</option>
                                            <option value="cash" @selected(old('payment_method') == 'cash')>Cash</option>
                                            <option value="transfer" @selected(old('payment_method') == 'transfer')>Transfer</option>
                                            <option value="credit" @selected(old('payment_method') == 'credit')>Kartu Kredit</option>
                                            <option value="debit" @selected(old('payment_method') == 'debit')>Kartu Debit</option>
                                        </x-form-floating-select>
                                    </div>

                                    <div class="col-12 col-lg-6 mb-6">
                                        <x-form-floating-input
                                            type="string"
                                            name="reference_number"
                                            id="reference_number"
                                            label="Nomor Referensi"
                                            placeholder="Nomor Referensi"
                                            error="{{ $errors->has('reference_number') }}"
                                            value="{{ old('reference_number') }}"
                                        />
                                    </div>

                                    <div class="col-12 col-lg-6 mb-6">
                                        <x-form-floating-input
                                            required
                                            type="number"
                                            name="amount"
                                            id="amount"
                                            label="Jumlah"
                                            placeholder="Jumlah"
                                            min="0"
                                            error="{{ $errors->has('amount') }}"
                                            value="{{ old('amount') }}"
                                        />
                                    </div>

                                    <div class="col-12">
                                        <x-form-floating-input
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
                                    <x-icon class="me-1">add</x-icon>
                                    <span>Tambah</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-12 mt-6">
                    <div class="card shadow-sm">
                        <div class="card-body table-responsive">
                            <table id="paymentsTable" class="table table-hover align-middle w-100">
                                <thead>
                                    <tr>
                                        <th class="text-start text-nowrap">Tanggal</th>
                                        <th class="text-start text-nowrap">Metode Pembayaran</th>
                                        <th class="text-start text-nowrap">Nomor Referensi</th>
                                        <th class="text-end text-nowrap">Jumlah</th>
                                        <th class="text-start text-nowrap">Catatan</th>
                                        <th></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($payments as $payment)
                                        <tr>
                                            <td class="text-start text-nowrap">{{ $payment->paymentDate->format('d M Y') }}</td>
                                            <td class="text-start text-nowrap">{{ strtoupper($payment->paymentMethod->value) }}</td>
                                            <td class="text-start text-nowrap">{{ $payment->referenceNumber }}</td>
                                            <td class="text-end text-nowrap">Rp {{ number_format($payment->amount) }}</td>
                                            <td class="text-start" style="width: 1000px">{{ $payment->notes }}</td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm" data-action="delete" data-purchase-id="{{ $purchase->id }}" data-payment-id="{{ $payment->id }}" data-bs-toggle="tooltip" data-bs-title="Hapus">
                                                    <x-icon class="fs-6">delete</x-icon>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- form delete --}}
    <form action="#" method="post" class="d-none" id="formDeletePayment">
        @csrf @method('delete')
    </form>

    <x-slot:script>
        <script>
            const paymentsTable = app.dataTable('#paymentsTable', {
                ordering: false,
                lengthChange: false,
                info: false,
                searching: false,
                fixedColumns: {
                    start: 0,
                    end: 1,
                },
                layouts: {
                    topStart: null,
                    topEnd: null,
                    bottomStart: null,
                    bottomEnd: "paging",
                },
            });

            $("button[data-action='delete']").click(function (e) { 
                e.preventDefault();
                
                app.delete('Pembayaran', (result) => {
                    if (result) {
                        const purchaseId = $(this).data('purchase-id');
                        const paymentId = $(this).data('payment-id');
                        const actionUrl = `${app.baseUrl}/transactions/purchases/${purchaseId}/payments/${paymentId}`;
                        const formDeletePayment = $('#formDeletePayment');

                        formDeletePayment.attr('action', actionUrl);
                        formDeletePayment.submit();
                    }
                });
            });
        </script>
    </x-slot:script>
</x-layouts.auth>