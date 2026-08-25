<x-layouts.auth title="Role">
    <div class="row mb-7">
        <div class="col-12">
            <x-page-header title="Role" icon="data_table">
                <x-slot:action>
                    <a role="button" href="{{ route('master.roles.create') }}" class="btn btn-secondary w-100">
                        <x-icon class="me-1" class="fs-6">add</x-icon>
                        <span>Tambah</span>
                    </a>
                </x-slot:action>
            </x-page-header>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <table id="roleTable" class="table table-hover w-100"></table>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        <script>
            const table = app.dataTable('#roleTable', {
                serverside: true,
                ajax: "{{ route('master.roles') }}",
                columns: [
                    {
                        title: 'Nama',
                        name: 'name',
                        data: 'name',
                    },
                    {
                        title: 'Deskripsi',
                        name: 'description',
                        data: 'description',
                    },
                    {
                        title: 'Dibuat',
                        name: 'created_at',
                        data: 'created_at',
                        searchable: false,
                        render: (data) => app.timestampForHuman(data)
                    },
                    {
                        title: 'Diedit',
                        name: 'updated_at',
                        data: 'updated_at',
                        searchable: false,
                        render: (data) => app.timestampForHuman(data)
                    },
                ]
            });
        </script>
    </x-slot:script>
</x-layouts.auth>