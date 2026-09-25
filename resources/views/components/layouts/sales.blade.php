<x-layouts.auth title="Penjualan" :access="$access">
    <x-page-header title="Penjualan" icon="shopping_cart">
        <x-slot:action>
            <div class="row justify-content-end">
                @if ($access?->create ?? false)
                    <div class="col-12 col-lg-6 col-xl-4">
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
                    <div class="col-12 col-lg-6 col-xl-4 mt-5 mt-lg-0">
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

        <hr class="my-6">

        <div class="row">
            <div class="col-12">
                <ul class="nav nav-pills nav-fill gx-5">
                    <li class="nav-item">
                        <a href="{{ route('transactions.sales') }}" @class(['nav-link', 'active' => $active === 'sales'])>
                            <x-icon class="me-1">add_shopping_cart</x-icon>
                            <span>Penjualan</span>
                        </a>
                    </li>

                    <li class="nav-item ms-2">
                        <a href="{{ route('transactions.sales.return') }}" @class(['nav-link', 'active' => $active === 'return'])>
                            <x-icon class="me-1">assignment_return</x-icon>
                            <span>Pengembalian</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </x-page-header>

    {{ $slot }}

    @isset($style)
        <x-slot:style>
            {{ $style }}
        </x-slot:style>
    @endisset

    @isset($script)
        <x-slot:script>
            {{ $script }}
        </x-slot:script>
    @endisset
</x-layouts.auth>
