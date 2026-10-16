<x-layouts.auth title="Tambah Produk">
    <x-page-header title="Tambah Produk" icon="add_box">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-4">
                    <x-button type="link" href="{{ route('masters.products') }}" color="light" startIcon="arrow_back" fullWidth>
                        Kembali
                    </x-button>
                </div>
            </div>
        </x-slot:action>
    </x-page-header>

    <form action="{{ route('masters.products.update', ['productId' => $product->id]) }}" method="post" autocomplete="off" enctype="multipart/form-data">
        @csrf @method('patch')

        <div class="row justify-content-center mt-7">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h2 class="fs-5 mb-0">Produk</h2>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-5">
                                <x-form-select
                                    required
                                    name="category_id"
                                    id="categoryId"
                                    label="Kategori"
                                    value="{{ old('category_id', $product->category->id) }}"
                                    error="{{ $errors->has('category_id') }}"
                                >
                                    <option selected disabled>Pilih</option>
                                    
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected($category->id == old('category_id', $product->category->id))>
                                            {{ $category->name }} ({{ $category->code }})
                                        </option>
                                    @endforeach
                                </x-form-select>
                            </div>

                            <div class="col-12 col-lg-6 mb-5">
                                <x-form-input
                                    required
                                    type="text"
                                    name="code"
                                    id="code"
                                    label="Kode"
                                    placeholder="Masukan kode..."
                                    value="{{ old('code', $product->code) }}"
                                    error="{{ $errors->has('code') }}"
                                />
                            </div>

                            <div class="col-12 col-lg-6 mb-5 mb-lg-0">
                                <x-form-input
                                    type="text"
                                    name="name"
                                    id="name"
                                    label="Nama (Deskripsi)"
                                    placeholder="Masukan nama atau deskripsi..."
                                    value="{{ old('name', $product->name) }}"
                                    error="{{ $errors->has('name') }}"
                                />
                            </div>

                            <div class="col-12 col-lg-6">
                                <x-form-select
                                    name="is_active"
                                    id="isActive"
                                    label="Status"
                                    value="{{ old('is_active', $product->isActive) }}"
                                    error="{{ $errors->has('is_active') }}"
                                >
                                    <option value="1" @selected($product->isActive)>Aktif</option>
                                    <option value="0" @selected(!$product->isActive)>Tidak Aktif</option>
                                </x-form-select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mt-7">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h2 class="fs-5 mb-0">Stok</h2>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-5">
                                <x-form-input
                                    type="number"
                                    name="minimum_stock"
                                    id="minimum_stock"
                                    label="Stok minimum"
                                    placeholder="Masukan stok minimum..."
                                    value="{{ old('minimum_stock', $product->minimumStock) }}"
                                    error="{{ $errors->has('minimum_stock') }}"
                                    min="0"
                                    step="0.01"
                                />
                            </div>

                            <div class="col-12 col-lg-6 mb-5">
                                <x-form-input
                                    type="number"
                                    name="current_stock"
                                    id="current_stock"
                                    label="Stok tersedia"
                                    placeholder="Masukan ketersediaan stok..."
                                    value="{{ old('current_stock', $product->currentStock) }}"
                                    error="{{ $errors->has('current_stock') }}"
                                    min="0"
                                    step="0.01"
                                />
                            </div>

                            <div class="col-12 col-lg-6">
                                <x-form-input
                                    type="number"
                                    name="discount"
                                    id="discount"
                                    label="Diskon %"
                                    placeholder="Masukan diskon..."
                                    value="{{ old('discount', $product->discount) }}"
                                    error="{{ $errors->has('discount') }}"
                                    min="0"
                                    step="0.01"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mt-7">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h2 class="fs-5 mb-0">Gambar</h2>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <x-form-input
                                    type="file"
                                    name="image"
                                    id="image"
                                    label="Unggah gambar"
                                    placeholder="Unggah gambar produk..."
                                    value="{{ old('image') }}"
                                    error="{{ $errors->has('image') }}"
                                    accept=".jpg,.jpeg,.png,.webp"
                                    helperText="Jangan unggah gambar jika anda tidak ingin merubahnya"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row justify-content-end">
                            <div class="col-6 col-lg-4 col-xl-2">
                                <x-button type="reset" color="light" startIcon="close" fullWidth>Reset</x-button>
                            </div>

                            <div class="col-6 col-lg-4 col-xl-2">
                                <x-button type="submit" color="secondary" startIcon="save" fullWidth>Simpan</x-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-layouts.auth>