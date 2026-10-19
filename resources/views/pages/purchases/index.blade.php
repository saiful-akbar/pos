<x-layouts.auth title="Pembelian" :access="$access">
    <x-page-header title="Pembelian" icon="shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end">
                @if ($access?->create ?? false)
                    <div class="col-12 col-lg-6 col-xl-4">
                        <x-button type="link" href="{{ route('transactions.purchases.carts') }}" startIcon="add" fullWidth>Tambah</x-button>
                    </div>
                @endif
            </div>
        </x-slot:action>
    </x-page-header>
</x-layouts.auth>