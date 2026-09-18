<x-layouts.stock-opname title="Stock Opname Harian" active="summary" :access="$access">
    <x-slot:header-actions>
        <div class="row justify-content-end">
            @if ($access?->update)
                <div class="col-6 col-lg-4 col-xl-3">
                    <x-button type="button" start-icon="edit" id="buttonEdit" full-width>
                        Edit
                    </x-button>
                </div>
            @endif

            @if ($access?->create)
                <div class="col-6 col-lg-4 col-xl-3">
                    <x-button type="button" start-icon="upload" id="buttonImport" full-width>
                        Import Excel
                    </x-button>
                </div>
            @endif
        </div>
    </x-slot:header-actions>

    <div class="modal" tabindex="-1" id="modalImport">
        <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-md-down">
            <div class="modal-content border">
                <div class="modal-header">
                    <h5 class="modal-title">Impor Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <x-button type="link" href="{{ route('inventories.dailyStockOpnames.export') }}" start-icon="download" full-width>
                                Unduh Template Excel
                            </x-button>
                        </div>

                        <div class="col-12 my-5">
                            <hr>
                        </div>

                        <div class="col-12 mb-5">
                            <x-form-input
                                required
                                type="text"
                                name="date"
                                id="date"
                                label="Tanggal"
                                placeholder="Tanggal"
                                value="{{ old('date', date('Y-m-d')) }}"
                                data-flatpickr=""
                            />
                        </div>

                        <div class="col-12">
                            <x-form-input
                                required
                                type="file"
                                name="file"
                                id="file"
                                label="Unggah File"
                                placeholder="Unggah File"
                                accept=".xlsx"
                            />
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <div class="row w-100">
                        <div class="col-6">
                            <x-button type="button" color="light" data-bs-dismiss="modal" start-icon="close" full-width>
                                Close
                            </x-button>
                        </div>

                        <div class="col-6">
                            <x-button type="submit" color="secondary" start-icon="save" full-width>
                                Simpan
                            </x-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-6">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="row justify-content-end align-items-center">
                        <div class="col-6 col-lg-2">
                            <x-button type="button" start-icon="filter_list" full-width>Filter</x-button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table id="stockTable" class="table table-sm table-hover align-middle w-100">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Total Item</th>
                                <th>Total Qty</th>
                                <th>Catatan</th>
                                <th class="text-center"><x-icon>settings</x-icon></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        <script>
            class Service {
                constructor() {
                    this.buttonImport = $('#buttonImport');
                    this.modalImport = new bootstrap.Modal('#modalImport', { backdrop: 'static' });
                }
            }

            window.service = new Service();
        </script>

        {{-- Global script --}}
        <script>
            service.buttonImport.click(function(e) {
                e.preventDefault();
                service.modalImport.show();
                console.log('click');
                
            });
        </script>
    </x-slot:script>
</x-layouts.stock-opname>