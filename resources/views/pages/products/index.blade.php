<x-layouts.auth title="Produk" :access="$access">
    <x-page-header title="Produk" icon="data_table">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-4 order-2 order-lg-1">
                    <x-button type="link" hred="{{ route('masters.products.export') }}" startIcon="download" fullWidth>
                        Export Excel
                    </x-button>
                </div>
    
                @if ($access?->create ?? false)
                    <div class="col-12 col-lg-4 order-1 order-lg-2 mb-5 mb-lg-0">
                        <x-button type="link" href="{{ route('masters.products.create') }}" startIcon="add" fullWidth>
                            Tambah
                        </x-button>
                    </div>
                @endif
            </div>
        </x-slot:action>
    </x-page-header>
</x-layouts.auth>