<x-layouts.auth title="Penjualan" :access="$access">
    <x-page-header title="Penjualan" icon="shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end">
                @if ($access?->create ?? false)
                    <div class="col-12 col-lg-5 col-xl-3">
                        <x-button
                            type="link"
                            href="{{ route('transactions.sales.carts') }}"
                            start-icon="shopping_cart"
                            full-width    
                        >
                            Kasir POS
                        </x-button>
                    </div>
                @endif
                
                @if ($access?->update ?? false)
                    <div class="col-12 col-lg-5 col-xl-3 mt-5 mt-lg-0">
                        <x-button
                            type="link"
                            href="{{ route('transactions.sales.return.carts') }}"
                            color="danger"
                            start-icon="assignment_return"
                            full-width
                        >
                            Pengembalian
                        </x-button>
                    </div>
                @endif
            </div>
        </x-slot:action>
    </x-page-header>
</x-layouts.auth>
