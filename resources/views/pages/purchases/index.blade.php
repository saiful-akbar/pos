<x-layouts.auth title="Pembelian">
    <x-page-header title="Pembelian" icon="add_shopping_cart">
        @if ($access?->create)
            <x-slot:action>
                <div class="row justify-content-end align-items-center w-100">
                    <div class="col-12 col-lg-6 col-xl-3">
                        <a href="{{ route('transactions.purchases.create') }}" class="btn btn-secondary w-100" role="button">
                            <x-icon class="me-1">add</x-icon>
                            <span>Tambah Pembelian</span>
                        </a>
                    </div>
                </div>
            </x-slot:action>
        @endif
    </x-page-header>

    <div class="row mt-7">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="purchaseTable" class="table table-hover w-100"></table>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        <script>
            const purchaseTable = app.dataTable('#purchaseTable', {
                serverSide: true,
                ajax: "{{ route('transactions.purchases') }}",
                columns: [
                    {
                        title: 'Tanggal',
                        name: 'purchases.purchase_date',
                        data: 'purchases.purchase_date',
                        className: 'text-nowrap',
                    },
                    {
                        title: 'No. Invoice',
                        name: 'purchases.invoice_number',
                        data: 'purchases.invoice_number',
                        className: 'text-nowrap',
                    },
                    {
                        title: 'Pemasok',
                        name: 'suppliers.name',
                        data: 'suppliers.name',
                        className: 'text-nowrap',
                    },
                    {
                        title: 'Dibuat Oleh',
                        name: 'users.name',
                        data: 'users.name',
                        className: 'text-nowrap',
                    },
                    {
                        title: 'Subtotal',
                        name: 'users.name',
                        data: 'users.name',
                        className: 'text-nowrap',
                    },
                ],
            });
        </script>
    </x-slot:script>
</x-layouts.auth>