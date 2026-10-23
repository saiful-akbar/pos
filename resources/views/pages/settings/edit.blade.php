<x-layouts.auth title="Edit Pengaturan">
    <x-page-header title="Edit Pengaturan" icon="settings">
        <x-slot:action>
            <div class="row justify-content-end align-items-center">
                <div class="col-12 col-lg-6 col-xl-3">
                    <a href="{{ route('settings.applications') }}" class="btn btn-light w-100" role="button">
                        <x-icon class="me-1">arrow_back</x-icon>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>
        </x-slot:action>
    </x-page-header>

    <form action="{{ route('settings.applications.update') }}" method="post" name="formUpdate" enctype="multipart/form-data">
        @csrf @method('put')

        <div class="row mt-6 justify-content-center">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h2 class="fs-5 mb-0">Toko</h2>
                    </div>
    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-6">
                                <x-form-floating-input
                                    required
                                    type="text"
                                    name="store_name"
                                    id="store_name"
                                    label="Nama Toko"
                                    placeholder="Nama toko"
                                    error="{{ $errors->has('store_name') }}"
                                    value="{{ old('store_name', $setting->storeName) }}"
                                />
                            </div>
    
                            <div class="col-12 col-lg-6 mb-6">
                                <x-form-floating-input
                                    type="textarea"
                                    name="store_address"
                                    id="store_address"
                                    label="Alamat Toko"
                                    placeholder="Alamat toko"
                                    error="{{ $errors->has('store_address') }}"
                                    value="{{ old('store_address', $setting->storeAddress) }}"
                                />
                            </div>
    
                            <div class="col-12">
                                <x-form-input
                                    type="file"
                                    name="store_logo"
                                    id="store_logo"
                                    label="Logo"
                                    error="{{ $errors->has('store_logo') }}"
                                    accept="image/*"
                                    helperText="Info: Biarkan logo tetap kosong jika tidak ingin merubahnya"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-6 justify-content-center">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h2 class="fs-5 mb-0">Aplikasi</h2>
                    </div>
    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-6">
                                <x-form-floating-input
                                    required
                                    type="text"
                                    name="app_name"
                                    id="app_name"
                                    label="Nama Aplikasi"
                                    placeholder="Nama aplikasi"
                                    error="{{ $errors->has('app_name') }}"
                                    value="{{ old('app_name', $setting?->appName) }}"
                                />
                            </div>
    
                            <div class="col-12 col-lg-6 mb-6">
                                <x-form-floating-input
                                    disabled
                                    type="text"
                                    name="app_version"
                                    id="app_version"
                                    label="Versi"
                                    placeholder="Versi"
                                    value="{{ $setting?->appVersion }}"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <div class="row mt-6 justify-content-center">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h2 class="fs-5 mb-0">Latar belakang</h2>
                    </div>
    
                    <div class="card-body">
                        <x-form-input
                            type="file"
                            name="background_image"
                            id="background_image"
                            error="{{ $errors->has('background_image') }}"
                            helperText="Info: Biarkan gambar latar belakang tetap kosong jika tidak ingin merubahnya"
                        />
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