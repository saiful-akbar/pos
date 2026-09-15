<x-layouts.auth title="Penjualan" :access="$access">
    <x-page-header title="Penjualan" icon="shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end">
                @if ($access?->create ?? false)
                    <div class="col-12 col-lg-6 col-xl-3">
                        <a href="{{ route('transactions.sales.carts') }}" role="submit" class="btn btn-secondary w-100">
                            <x-icon class="me-1">shopping_cart</x-icon>
                            <span>Kasir POS</span>
                        </a>
                    </div>
                @endif
                
                @if ($access?->update ?? false)
                    <div class="col-12 col-lg-6 col-xl-3">
                        <a href="{{ route('transactions.sales.return.carts') }}" role="submit" class="btn btn-secondary w-100">
                            <x-icon class="me-1">assignment_return</x-icon>
                            <span>Pengembalian</span>
                        </a>
                    </div>
                @endif
            </div>
        </x-slot:action>
    </x-page-header>
</x-layouts.auth>
