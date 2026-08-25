<x-layouts.auth title="Tambah Role">
    <x-page-header title="Tambah Role" icon="data_table">
        <x-slot:action>
            <a href="{{ route('master.roles') }}" role="button" class="btn btn-light w-100">
                <x-icon>arrow_back</x-icon>
                <span>Kembali</span>
            </a>
        </x-slot:action>
    </x-page-header>

    <form action="{{ route('master.roles.store') }}" method="post" name="create" autocomplete="off">
        @csrf @method('post')

        <div class="row mt-7">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="fs-5 mb-1">Role</h3>
                        <p class="text-body-secondary m-0">Isi nama dan deskripsi role baru.</p>
                    </div>
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-5 mb-lg-0">
                                <x-form-floating-input
                                    type="text"
                                    label="Nama role"
                                    placeholder="Nama role"
                                    name="role_name"
                                    id="role_name"
                                    error="{{ $errors->has('role_name') }}"
                                    value="{{ old('role_name') }}"
                                />
                            </div>

                            <div class="col-12 col-lg-6">
                                <x-form-floating-input
                                    type="text"
                                    label="Deskripsi"
                                    placeholder="Deskripsi"
                                    name="role_description"
                                    id="role_description"
                                    error="{{ $errors->has('role_description') }}"
                                    value="{{ old('role_description') }}"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-7">
            <div class="col12">
                <h2 class="fs-4">Akses Modul</h2>
                <hr>
            </div>
        </div>

        @foreach ($modules as $module)
            <div class="row mt-7">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                @isset($module->icon)
                                    <div class="col-auto">
                                        <x-icon class="fs-4">{{ $module->icon }}</x-icon>
                                    </div>
                                @endisset
    
                                <div class="col">
                                    <h3 class="fs-6 m-0">{{ $module->name }}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="card-body table-responsive">
                            <table class="table table-sm w-100">
                                <thead>
                                    <th class="text-nowrap">Nama modul</th>
                                    <th class="text-nowrap">Read</th>
                                    <th class="text-nowrap">Create</th>
                                    <th class="text-nowrap">Update</th>
                                    <th class="text-nowrap">Remove</th>
                                    <th class="text-nowrap">Delete</th>
                                </thead>

                                <tbody>
                                    @foreach ($module->details as $detail)
                                        <tr>
                                            <td class="text-nowrap">{{ $detail->name }}</td>
                                            <td>
                                                <div class="form-check">
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input"
                                                        value="1"
                                                        id="{{ $detail->id }}-read"
                                                        name="modules[{{ $detail->id }}][read]"
                                                        @checked(old("modules.{$detail->id}.read"))
                                                    >

                                                    <label class="form-check-label" for="{{ $detail->id }}-read"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input"
                                                        value="1"
                                                        id="{{ $detail->id }}-create"
                                                        name="modules[{{ $detail->id }}][create]"
                                                        @checked(old("modules.{$detail->id}.create"))
                                                    >

                                                    <label class="form-check-label" for="{{ $detail->id }}-create"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input"
                                                        value="1"
                                                        id="{{ $detail->id }}-update"
                                                        name="modules[{{ $detail->id }}][update]"
                                                        @checked(old("modules.{$detail->id}.update"))
                                                    >

                                                    <label class="form-check-label" for="{{ $detail->id }}-update"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input"
                                                        value="1"
                                                        id="{{ $detail->id }}-remove"
                                                        name="modules[{{ $detail->id }}][remove]"
                                                        @checked(old("modules.{$detail->id}.remove"))
                                                    >

                                                    <label class="form-check-label" for="{{ $detail->id }}-remove"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input"
                                                        value="1"
                                                        id="{{ $detail->id }}-delete"
                                                        name="modules[{{ $detail->id }}][delete]"
                                                        @checked(old("modules.{$detail->id}.delete"))
                                                    >

                                                    <label class="form-check-label" for="{{ $detail->id }}-delete"></label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="row mt-7 justify-content-center">
            <div class="col-12 col-lg-8">
                <button type="submit" class="btn btn-secondary w-100">
                    <x-icon class="me-1">save</x-icon>
                    <span>Simpan</span>
                </button>
            </div>
        </div>
    </form>
</x-layouts.auth>