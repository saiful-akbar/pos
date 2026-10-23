<x-layouts.auth title="Edit User">
    <x-page-header title="Edit User" icon="people">
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

    <form action="{{ route('masters.users.update', ['id' => $user->id]) }}" method="post" autocomplete="off" enctype="multipart/form-data">
        @csrf @method('put')

        <div class="row mt-7 justify-content-center">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h2 class="fs-5 mb-0">User Data</h2>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-5">
                                <x-form-input
                                    required
                                    type="text"
                                    name="full_name"
                                    id="full_name"
                                    label="Nama lengkap"
                                    placeholder="Nama lengkap"
                                    error="{{ $errors->has('full_name') }}"
                                    value="{{ old('full_name', $user->username) }}"
                                />
                            </div>

                            <div class="col-12 col-lg-6 mb-5">
                                <x-form-select required name="role" id="role" label="Role" error="{{ $errors->has('role') }}">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" @selected(old('role', $user->role->id) == $role->id)>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </x-form-select>
                            </div>

                            <div class="col-12 col-lg-6">
                                <x-form-select required name="status" id="status" label="Status" error="{{ $errors->has('status') }}">
                                    <option value="1" @selected(old('status', $user->isActive) == 1)>Aktif</option>
                                    <option value="0" @selected(old('status', $user->isActive) == 0)>Tidak Aktif</option>
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
                        <h2 class="fs-5 mb-0">Autentikasi</h2>
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
                                    value="{{ old('username', $user->username) }}"
                                />
                            </div>

                            <div class="col-12 col-lg-6">
                                <x-form-input
                                    type="password"
                                    name="password"
                                    id="password"
                                    label="Kata sandi"
                                    placeholder="Kata sandi"
                                    error="{{ $errors->has('password') }}"
                                    value="{{ old('password') }}"
                                    helper-text="NB: kosongkan kata sandi jika tidak ingin merubahnya"
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
                        <h2 class="fs-5 mb-0">Foto</h2>
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

                                <div class="form-text">NB: Kosongkan foto jika tidak ingin merubahnya.</div>

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