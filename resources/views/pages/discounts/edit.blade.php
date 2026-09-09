<x-layouts.auth title="Edit Diskon">
    <x-page-header title="Edit Diskon" icon="data_table">
        <x-slot:action>
            <div class="row justify-content-end align-items-center">
                <div class="col-12 col-lg-6 col-xl-3">
                    <a href="{{ route('master.discounts') }}" class="btn btn-light w-100" role="button">
                        <x-icon class="me-1">arrow_back</x-icon>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>
        </x-slot:action>
    </x-page-header>

    <form action="{{ route('master.discounts.update', ['id' => $discount->id]) }}" method="post" autocomplete="off">
        @csrf @method('patch')

        <div class="row mt-6 justify-content-center">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="fs-5 mb-1">Form edit diskon</h3>
                        <p class="text-body-secondary m-0">Form dengan tanda * wajib diisi</p>
                    </div>
    
                    <div class="card-header">
                        <div class="row align-items-start">
                            <div class="col-12 col-xl-6 mb-6">
                                <x-form-floating-input
                                    type="number"
                                    name="discount"
                                    id="discount"
                                    label="Diskon"
                                    min="0"
                                    value="{{ old('discount', $discount->discount) }}"
                                    error="{{ $errors->has('discount') }}"
                                    required
                                />
                            </div>

                            <div class="col-12 col-xl-6 mb-6">
                                <x-form-floating-input
                                    type="text"
                                    name="name"
                                    id="name"
                                    label="Nama atau Deskripsi"
                                    value="{{ old('name', $discount->name) }}"
                                    error="{{ $errors->has('name') }}"
                                    required
                                />
                            </div>

                            <div class="col-12 col-xl-6 mb-6 mb-xl-0">
                                <x-form-floating-input
                                    type="text"
                                    name="effective_date"
                                    id="effective_date"
                                    label="Tanggal efektif"
                                    value="{{ old('effective_date', $discount->effectiveDate->format('Y-m-d')) }}"
                                    error="{{ $errors->has('effective_date') }}"
                                    data-flatpickr=""
                                    required
                                />
                            </div>

                            <div class="col-12 col-xl-6">
                                <x-form-floating-input
                                    type="text"
                                    name="expiration_date"
                                    id="expiration_date"
                                    label="Tanggal Kadaluarsa"
                                    value="{{ old('expiration_date', $discount->expirationDate?->format('Y-m-d')) }}"
                                    error="{{ $errors->has('expiration_date') }}"
                                    data-flatpickr=""
                                />
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