<x-layouts.auth title="Penjualan" :access="$access">
    <x-page-header title="Penjualan" icon="shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end">
                @if ($access?->create ?? false)
                    <div class="col-12 col-lg-6 col-xl-4">
                        <x-button
                            type="link"
                            href="{{ route('transactions.sales.carts') }}"
                            color="secondary"
                            startIcon="add_shopping_cart"
                            fullWidth
                        >
                            Mulai Transaksi
                        </x-button>
                    </div>
                @endif
            </div>
        </x-slot:action>
    </x-page-header>
</x-layouts.auth>