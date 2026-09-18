<x-layouts.auth title="{{ $title }}">
    <x-page-header title="{{ $title }}" icon="inventory">
        @isset($headerActions)
            <x-slot:action>{{ $headerActions }}</x-slot:action>
        @endisset

        <hr class="my-6">

        <div class="row">
            <div class="col-12">
                <ul class="nav nav-pills nav-fill gx-5">
                    <li class="nav-item">
                        <a href="{{ route('inventories.dailyStockOpnames') }}" @class(['nav-link', 'active' => $active === 'summary'])>
                            <x-icon class="me-1">summarize</x-icon>
                            <span>Summary</span>
                        </a>
                    </li>

                    <li class="nav-item ms-2">
                        <a href="{{ route('inventories.dailyStockOpnames.detail') }}" @class(['nav-link', 'active' => $active === 'detail'])>
                            <x-icon class="me-1">fact_check</x-icon>
                            <span>Detail per produk</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </x-page-header>

    {{ $slot }}

    @isset($style)
        <x-slot:style>{{ $style }}</x-slot:style>
    @endisset

    @isset($script)
        <x-slot:script>{{ $script }}</x-slot:script>
    @endisset
</x-layouts.auth>