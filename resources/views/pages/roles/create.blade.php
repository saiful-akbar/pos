<x-layouts.auth title="Tambah Role">
    <x-page-header title="Tambah Role" icon="data_table">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-4">
                    <a href="{{ route('masters.roles') }}" role="button" class="btn btn-light w-100">
                        <x-icon>arrow_back</x-icon>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>
        </x-slot:action>
    </x-page-header>

    <form action="{{ route('masters.roles.store') }}" method="post" name="create" autocomplete="off">
        @csrf @method('post')

        <div class="row gx-7 mt-7">
            <div class="col-12 col-lg-4 mb-6 mb-lg-0">
                <div class="card shadow-sm position-sticky" style="top: 20px;">
                    <div class="card-header">
                        <h2 class="fs-5 mb-0">Role</h2>
                        <p class="text-body-secondary mt-1 mb-0">Isi nama dan deskripsi role baru.</p>
                    </div>
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-5">
                                <x-form-input
                                    required
                                    type="text"
                                    label="Nama role"
                                    placeholder="Masukan nama role..."
                                    name="role_name"
                                    id="role_name"
                                    error="{{ $errors->has('role_name') }}"
                                    value="{{ old('role_name') }}"
                                />
                            </div>

                            <div class="col-12">
                                <x-form-input
                                    type="textarea"
                                    label="Deskripsi"
                                    placeholder="Masukan deskripsi role..."
                                    name="role_description"
                                    id="role_description"
                                    error="{{ $errors->has('role_description') }}"
                                    value="{{ old('role_description') }}"
                                    rows="5"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <x-button type="submit" startIcon="save" fullWidth>Simpan</x-button>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                @foreach ($modules as $module)
                    <div @class(['row', 'mb-7' => !$loop->last])>
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <div class="row align-items-center justify-content-start">
                                        @isset($module->icon)
                                            <div class="col-auto">
                                                <x-icon class="fs-4">{{ $module->icon }}</x-icon>
                                            </div>
                                        @endisset
            
                                        <div class="col">
                                            <h2 class="fs-5 mb-0">{{ $module->name }}</h2>
                                        </div>
                                    </div>
                                </div>
        
                                <div class="card-body table-responsive">
                                    <table class="table table-sm w-100">
                                        <thead>
                                            <th class="text-nowrap" style="width: 200px">Modul</th>
                                            <th class="text-nowrap" style="width: 100px">Read</th>
                                            <th class="text-nowrap" style="width: 100px">Create</th>
                                            <th class="text-nowrap" style="width: 100px">Update</th>
                                            <th class="text-nowrap" style="width: 100px">Remove</th>
                                            <th class="text-nowrap" style="width: 100px">Delete</th>
                                        </thead>
        
                                        <tbody>
                                            @foreach ($module->details as $detail)
                                                <tr>
                                                    <td class="text-nowrap">{{ $detail->name }}</td>
                                                    <td>
                                                        <div class="form-check form-switch">
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
                                                        <div class="form-check form-switch">
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
                                                        <div class="form-check form-switch">
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
                                                        <div class="form-check form-switch">
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
                                                        <div class="form-check form-switch">
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
            </div>
        </div>
    </form>
</x-layouts.auth>