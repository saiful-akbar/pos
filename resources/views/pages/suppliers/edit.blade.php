<x-layouts.auth title="Edit Pemasok">
    <x-page-header title="Edit Pemasok" icon="data_table">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-4">
                    <a href="{{ route('master.suppliers') }}" class="btn btn-light w-100" role="button">
                        <x-icon class="me-1">arrow_back</x-icon>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>
        </x-slot:action>
    </x-page-header>

    <form action="{{ route('master.suppliers.update', ['id' => $supplier->id]) }}" method="post" autocomplete="off">
        @csrf @method('put')

        <div class="row mt-7">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="fs-5">Form</h3>
                        <p class="text-body-secondary">Form yang ditandai * wajib diisi</p>
                    </div>
    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-6">
                                <x-form-floating-input
                                    required
                                    type="text"
                                    name="name"
                                    id="name"
                                    label="Nama"
                                    placeholder="Nama"
                                    error="{{ $errors->has('name') }}"
                                    value="{{ old('name', $supplier->name) }}"
                                />
                            </div>
    
                            <div class="col-12 col-lg-6 mb-6">
                                <x-form-floating-input
                                    type="text"
                                    name="phone_number"
                                    id="phone_number"
                                    label="Nomor Telepon"
                                    placeholder="Nomor Telepon"
                                    error="{{ $errors->has('phone_number') }}"
                                    value="{{ old('phone_number', $supplier?->phoneNumber) }}"
                                />
                            </div>
    
                            <div class="col-12">
                                <x-form-floating-input
                                    type="textarea"
                                    name="address"
                                    id="address"
                                    label="Alamat"
                                    placeholder="Alamat"
                                    error="{{ $errors->has('address') }}"
                                    value="{{ old('address', $supplier?->address) }}"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-7">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="fs-5">Catatan & Status</h3>
                        <p class="text-body-secondary">Tambahkan catatan dan status pemasok.</p>
                    </div>
    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-6 mb-lg-0">
                                <x-form-floating-input
                                    type="textarea"
                                    name="notes"
                                    id="notes"
                                    label="Catatan"
                                    placeholder="Catatan"
                                    error="{{ $errors->has('notes') }}"
                                    value="{{ old('notes', $supplier?->notes) }}"
                                />
                            </div>

                            <div class="col-12 col-lg-6 mb-6 mb-lg-0">
                                <x-form-floating-select name="status" id="status" error="{{ $errors->has('status') }}" label="Status">
                                    <option value="1" @selected(old('status', $supplier->isActive) == 1)>Aktif</option>
                                    <option value="0" @selected(old('status', $supplier->isActive) == 0)>Tidak aktif</option>
                                </x-form-floating-select>
                            </div>
                        </div>
                    </div>
    
                    <div class="card-footer">
                        <div class="row justify-content-end align-items-center">
                            <div class="col-6 col-lg-3 col-xl-2">
                                <button type="reset" class="btn btn-light w-100">
                                    <x-icon class="me-1">close</x-icon>
                                    <span>Reset</span>
                                </button>
                            </div>

                            <div class="col-6 col-lg-3 col-xl-2">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <x-icon class="me-1">save</x-icon>
                                    <span>Simpan</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-layouts.auth>