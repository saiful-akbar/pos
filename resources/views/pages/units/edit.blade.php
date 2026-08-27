<x-layouts.auth title="Edit Unit">
    <x-page-header title="Edit Unit" icon="data_table">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-4">
                    <a href="{{ route('master.units') }}" class="btn btn-light w-100" role="button">
                        <x-icon class="me-1">arrow_back</x-icon>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>
        </x-slot:action>
    </x-page-header>

    <form action="{{ route('master.units.update', ['id' => $unit->id]) }}" method="post" autocomplete="off">
        @csrf @method('put')

        <div class="row mt-7">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="fs-5">Form</h3>
                        <p class="text-body-secondary">Form dengan tanda * wajin diisi</p>
                    </div>
    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-6">
                                <x-form-floating-input
                                    required
                                    type="text"
                                    name="code"
                                    id="code"
                                    label="Kode"
                                    placeholder="Kode"
                                    error="{{ $errors->has('code') }}"
                                    value="{{ old('code', $unit->code) }}"
                                />
                            </div>
    
                            <div class="col-12 col-lg-6 mb-6">
                                <x-form-floating-input
                                    required
                                    type="text"
                                    name="name"
                                    id="name"
                                    label="Nama (deskripsi)"
                                    placeholder="Nama (deskripsi)"
                                    error="{{ $errors->has('name') }}"
                                    value="{{ old('name', $unit->name) }}"
                                />
                            </div>
    
                            <div class="col-12 col-lg-6">
                                <x-form-floating-select required name="status" id="status" error="{{ $errors->has('status') }}" label="Status">
                                    <option value="1" @selected(old('status', $unit->isActive) == true)>Aktif</option>
                                    <option value="0" @selected(old('status', $unit->isActive) == false)>Tidak aktif</option>
                                </x-form-floating-select>
                            </div>
                        </div>
                    </div>
    
                    <div class="card-footer d-flex justify-content-end">
                        <button type="reset" class="btn btn-light me-2">
                            <x-icon class="me-1">close</x-icon>
                            <span>Reset</span>
                        </button>
    
                        <button type="submit" class="btn btn-secondary">
                            <x-icon class="me-1">save</x-icon>
                            <span>Simpan</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-layouts.auth>