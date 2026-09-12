<x-layouts.receipt title="Struk Penjualan {{ $sale->invoiceNumber }}">
    <div class="row justify-content-center align-items-start">
        <div class="col-12 col-md-6 col-lg-5 col-xl-4">
            <div class="row mb-6">
                <div class="col-6">
                    <button type="button" class="btn btn-secondary w-100 shadow-sm" id="print">
                        <x-icon class="me-1">print</x-icon>
                        <span>Cetak (Ctrl + P)</span>
                    </button>
                </div>

                <div class="col-6">
                    <button type="button" class="btn btn-dark w-100 shadow-sm" id="close">
                        <x-icon class="me-1">close</x-icon>
                        <span>Tutup</span>
                    </button>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm" id="print-area">
                        <div class="card-body">
                            <div class="row justify-content-center mb-7">
                                <div class="col-12 text-center mb-2">
                                    <img src="{{ logo(setting()?->storeLogo) }}" alt="Logo" class="rounded" width="100" height="100" >
                                </div>
        
                                <div class="col-12 text-center">
                                    <h3 class="fs-3 fw-bold mb-1">{{ setting()?->storeName ?? config('app.name') }}</h3>
                                    <p class="fs-sm">{{ setting()?->storeAddress }}</p>
                                </div>
                            </div>
                            
                            <div class="row justify-content-between">
                                <div class="col-auto">
                                    <div class="fs-sm">{{ $sale->createdAt->format('d M Y') }}</div>
                                    <div class="fs-sm">{{ $sale->createdAt->format('H:i:s') }}</div>
                                </div>
                                <div class="col-auto text-end">
                                    <div class="fs-sm">{{ $sale->invoiceNumber }}</div>
                                    <div class="fs-sm">Kasir: {{ $sale->user->fullName }}</div>
                                </div>
                            </div>
        
                            <hr class="my-5">
        
                            <div class="row">
                                <div class="col-12">
                                    <div class="fs-6 fw-bold m-0">Detail Pembelian:</div>
                                </div>
                            </div>
        
                            @foreach ($sale->details as $detail)
                                <div class="row justify-content-between align-items-start mt-3">
                                    <div class="col-8">
                                        <div class="text-start text-body-primary fw-bold fs-sm text-truncate" style="max-width: auto;">
                                            <span>{{ $detail->product->code }}</span>
                                            @isset($detail->product->name)
                                                <span> - {{ $detail->product->name }}</span>
                                            @endisset
                                        </div>
                                        
                                        <div class="text-body-primary fs-sm">
                                            <span>{{ $detail->quantity }} {{ $detail->product->unit->code }} X Rp {{ number_format($detail->sellingPrice) }}</span>
                                            <span>{{ $detail->discount > 0 ? "$detail->discount%" : '' }}</span>
                                        </div>
                                    </div>
        
                                    <div class="col-4 text-end">
                                        <div class="text-body-primary fs-sm">Rp {{ number_format($detail->subtotal) }}</div>
                                    </div>
                                </div>
                            @endforeach
        
                            <hr class="my-5">
        
                            <div class="row justify-content-between">
                                <div class="col-auto fs-6 fw-bold">Total</div>
                                <div class="col-auto fs-6 fw-bold">Rp {{ number_format($sale->totalAmount) }}</div>
                            </div>
        
                            <hr class="my-5">
        
                            <div class="row justify-content-between mb-3">
                                <div class="col-12 fs-6 fw-bold m-0">Pembayaran:</div>
                            </div>
        
                            <div class="row justify-content-between">
                                <div class="col fw-bold">{{ $sale->paymentMethod->value }}</div>
                                <div class="col-auto fw-bold">Rp {{ number_format($sale->paidAmount) }}</div>
                            </div>
        
                            <div class="row justify-content-between">
                                <div class="col fw-bold fs-sm">Kembalian</div>
                                <div class="col-auto fw-bold fs-sm">Rp {{ number_format($sale->changeAmount) }}</div>
                            </div>
        
                            <div class="row justify-content-center mt-7">
                                <div class="col-auto rounded p-3 bg-white">
                                    {!! $barcode !!}
                                </div>
                            </div>
        
                            <div class="row justify-content-center mt-5">
                                <div class="col-12 text-center fw-bold">
                                    Terima kasih atas kunjungan anda
                                </div>
        
                                <div class="col text-center fs-sm text-body-secondary">
                                    Produk yang sudah dibeli tidak dapat ditukar atau dikembalikan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot:style>
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }
                
                #print-area,
                #print-area * {
                    visibility: visible;
                    background: white;
                    box-shadow: none !important;
                }

                #print-area {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    border: none;
                }

                #print-area hr {
                    color: #000 !important;
                }
            }

        </style>
    </x-slot:style>

    <x-slot:script>
        <script>
            $('#print').click(function (e) { 
                e.preventDefault();
                window.print()
            });

            $('#close').click(function (e) { 
                e.preventDefault();
                window.close()
            });
        </script>
    </x-slot:script>
</x-layouts.receipt>