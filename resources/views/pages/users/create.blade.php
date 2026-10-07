<x-layouts.auth title="Tambah User">
    <x-page-header title="Tambah User" icon="people">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-4">
                    <a href="{{ route('masters.users') }}" role="button" class="btn btn-light w-100">
                        <x-icon class="me-1">arrow_back</x-icon>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>
        </x-slot:action>
    </x-page-header>

    <form action="{{ route('masters.users.store') }}" method="post" autocomplete="off" enctype="multipart/form-data">
        @csrf @method('post')

        <div class="row mt-7 justify-content-center">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="fs-5 mb-0">User Data</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-5 mb-lg-0">
                                <x-form-input
                                    required
                                    type="text"
                                    name="full_name"
                                    id="full_name"
                                    label="Nama lengkap"
                                    placeholder="Nama lengkap"
                                    error="{{ $errors->has('full_name') }}"
                                    value="{{ old('full_name') }}"
                                />
                            </div>

                            <div class="col-12 col-lg-6">
                                <x-form-select required name="role" id="role" label="Role" error="{{ $errors->has('role') }}">
                                    <option selected disabled>Pilih</option>
                                    
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" @selected(old('role') == $role->id)>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </x-form-select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-7 justify-content-center">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="fs-5 mb-0">Autentikasi</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-5 mb-lg-0">
                                <x-form-input
                                    required
                                    type="text"
                                    name="username"
                                    id="username"
                                    label="Username"
                                    placeholder="Username"
                                    error="{{ $errors->has('username') }}"
                                    value="{{ old('username') }}"
                                />
                            </div>

                            <div class="col-12 col-lg-6">
                                <x-form-input
                                    required
                                    type="password"
                                    name="password"
                                    id="password"
                                    label="Kata sandi"
                                    placeholder="Kata sandi"
                                    error="{{ $errors->has('password') }}"
                                    value="{{ old('password') }}"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-7 justify-content-center">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="fs-5 mb-0">Foto</h3>
                    </div>

                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <input
                                    type="file"
                                    name="photo"
                                    id="photo"
                                    accept="image/*"
                                    @class([
                                        'form-control',
                                        'is-invalid' => $errors->has('photo'),
                                        'border' => !$errors->has('photo'),
                                    ])
                                >

                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row justify-content-end">
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