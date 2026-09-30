<x-layouts.auth title="Detail Pembelian" :access="$access">
    <x-page-header title="Detail Pembelian" icon="add_shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-6 col-xl-4">
                    <x-button type="button" color="danger" startIcon="close" fullWidth onclick="window.close()">
                        Tutup
                    </x-button>
                </div>
            </div>
        </x-slot:action>
    </x-page-header>

    <div class="row mt-6">
        <div class="col-12 col-lg-6 col-xl-4 mb-6">
            <div class="card shadow-sm position-sticky" style="top: 20px;">
                <div class="card-header">
                    <h3 class="fs-5 my-0">Ringkasan</h3>
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
                            <span class="text-body-secondary">Total Quantity</span>
                            <span>{{ number_format($purchase->totalQuantity, 2) }}</span>
                        </li>
                        
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="text-body-secondary">Grand Harga</span>
                            <span>{{ number_format($purchase->totalPrice, 2) }}</span>
                        </li>
                        
                        <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                            <span class="text-body-secondary">Status Pembayaran</span>
                            <span>{{ strtoupper($purchase->paymentStatus->value) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6 col-xl-8">
            <div class="row mb-6">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h3 class="fs-5 my-0">Produk</h3>
                        </div>
        
                        <div class="card-body">
                            <table id="detailTable" class="table table-hover table-sm table-img w-100 align-middle">
                                <thead>
                                    <tr>
                                        <th class="text-start text-nowrap">Produk</th>
                                        <th class="text-end text-nowrap">Quantity</th>
                                        <th class="text-end text-nowrap">Harga Beli</th>
                                        <th class="text-end text-nowrap">Subtotal</th>
                                    </tr>
                                </thead>
        
                                <tbody>
                                    @foreach ($details as $detail)
                                        <tr>
                                            <td class="text-start text-nowrap">
                                                <div class="d-flex align-items-center justify-content-start">
                                                    <img src="{{ storage_image($detail->product->image) }}" alt="{{ $detail->product->code }}" class="rounded">
                                                    
                                                    <div class="ms-3">
                                                        {{ $detail->product->code }}
                                                        <p class="text-body-secondary text-truncate mb-0" style="width: 150px;">{{ $detail->product->name }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">{{ number_format($detail->quantity, 2) }}</td>
                                            <td class="text-end">{{ number_format($detail->purchasePrice, 2) }}</td>
                                            <td class="text-end">{{ number_format($detail->subtotalPrice, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-auto">
                                    <h3 class="fs-5 my-0">Pembayaran</h3>
                                </div>
                                
                                @if ($access?->create)
                                    <div class="col-12 col-lg-6 col-xl-3 mt-5 mt-lg-0">
                                        <a href="{{ route('transactions.purchases.payments', ['id' => $purchase->id]) }}" class="btn btn-secondary w-100" role="button" target="_blank">
                                            <x-icon class="me-1">add</x-icon>
                                            <span>Tambah</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <dic class="card-body">
                            <table id="paymentTable" class="table table-hover align-middle w-100">
                                <thead>
                                    <tr>
                                        <th class="text-start text-nowrap">Tanggal</th>
                                        <th class="text-start text-nowrap">Metode Pembayaran</th>
                                        <th class="text-start text-nowrap">Nomor Referensi</th>
                                        <th class="text-end text-nowrap">Jumlah</th>
                                        <th class="text-start text-nowrap">Catatan</th>
                                    </tr>
                                </thead>

                                @php
                                    $totalAmount = 0;
                                @endphp

                                <tbody>
                                    @foreach ($payments as $payment)
                                        @php
                                            $totalAmount += $payment->amount;
                                        @endphp

                                        <tr>
                                            <td class="text-start text-nowrap">{{ $payment->paymentDate->format('d M Y') }}</td>
                                            <td class="text-start text-nowrap">{{ strtoupper($payment->paymentMethod->value) }}</td>
                                            <td class="text-start text-nowrap">{{ $payment->referenceNumber }}</td>
                                            <td class="text-end text-nowrap">{{ number_format($payment->amount) }}</td>
                                            <td class="text-start text-nowrap">{{ $payment->notes }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th class="text-start text-nowrap" colspan="3">Kurang Bayar</th>
                                        <th class="text-end text-nowrap">{{ number_format($purchase->totalPrice - $totalAmount) }}</th>
                                        <th class="text-end text-nowrap"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </dic>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        <script>
            const tableOptions = {
                lengthChange: false,
                searching: false,
                info: false,
                layout: {
                    topStart: null,
                    topEnd: null,
                    bottomStart: null,
                    bottomEnd: "paging",
                },
            };

            const detailTable = app.dataTable('#detailTable', tableOptions);
            const paymentTable = app.dataTable('#paymentTable', tableOptions);
        </script>
    </x-slot:script>
</x-layouts.auth>