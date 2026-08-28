<x-layouts.auth title="Edit Produk">
    <x-page-header title="Edit Produk" icon="data_table">
        <x-slot:action>
            <div class="row justify-content-end">
                <div class="col-12 col-lg-4">
                    <a href="{{ route('master.products') }}" class="btn btn-light w-100" role="button">
                        <x-icon class="me-1">arrow_back</x-icon>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>
        </x-slot:action>
    </x-page-header>

    <form action="{{ route('master.products.update', ['id' => $product->id]) }}" method="post" autocomplete="off">
        @csrf @method('put')

        <div class="row mt-7">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="fs-5">Data Produk</h3>
                        <p class="text-body-secondary">Isikan data kode, barcode dan nama produk.</p>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-5">
                                <x-form-floating-input
                                    required
                                    type="text"
                                    name="code"
                                    id="code"
                                    label="Kode Produk"
                                    placeholder="Kode Produk"
                                    error="{{ $errors->has('code') }}"
                                    value="{{ old('code', $product->code) }}"
                                />
                            </div>

                            <div class="col-12 col-lg-6 mb-5">
                                <x-form-floating-input
                                    required
                                    type="text"
                                    name="barcode"
                                    id="barcode"
                                    label="Barcode"
                                    placeholder="Barcode"
                                    error="{{ $errors->has('barcode') }}"
                                    value="{{ old('barcode', $product->barcode) }}"
                                />
                            </div>

                            <div class="col-12 col-lg-6 mb-5 mb-lg-0">
                                <x-form-floating-input
                                    type="text"
                                    name="name"
                                    id="name"
                                    label="Nama (Deskripsi)"
                                    placeholder="Nama (Deskripsi)"
                                    error="{{ $errors->has('name') }}"
                                    value="{{ old('name', $product->name) }}"
                                />
                            </div>

                            <div class="col-12 col-lg-6">
                                <x-form-floating-select required name="is_active" id="is_active" label="Status" error="{{ $errors->has('is_active') }}">
                                    <option value="1" @selected(old('is_active', $product->isActive) == 1)>Aktif</option>
                                    <option value="0" @selected(old('is_active', $product->isActive) == 0)>Tidak Aktif</option>
                                </x-form-floating-select>
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
                        <h3 class="fs-5">Kategori & Unit</h3>
                        <p class="text-body-secondary">Pilih kategori dan jenis unit produk.</p>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-5 mb-lg-0">
                                <x-form-floating-select required name="category" id="category" label="Kategori" error="{{ $errors->has('category') }}">
                                    <option disabled selected>Pilih kategori</option>

                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category', $product->category->id) == $category->id)>
                                            {{ $category->code }} - {{ $category->name }}
                                        </option>
                                    @endforeach
                                </x-form-floating-select>
                            </div>

                            <div class="col-12 col-lg-6">
                                <x-form-floating-select required name="unit" id="unit" label="Unit" error="{{ $errors->has('unit') }}">
                                    <option disabled selected>Pilih unit</option>

                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}" @selected(old('unit', $product->unit->id) == $unit->id)>
                                            {{ $unit->code }} - {{ $unit->name }}
                                        </option>
                                    @endforeach
                                </x-form-floating-select>
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
                        <h3 class="fs-5">Harga</h3>
                        <p class="text-body-secondary">Masukan harga beli dan harga jual produk.</p>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-5">
                                <x-form-floating-input
                                    type="number"
                                    min="0"
                                    name="purchase_price"
                                    id="purchase_price"
                                    label="Harga Beli"
                                    placeholder="Harga Beli"
                                    error="{{ $errors->has('purchase_price') }}"
                                    value="{{ old('purchase_price', $product->purchasePrice) }}"
                                    class="no-spinners"
                                />
                            </div>

                            <div class="col-12 col-lg-6 mb-5">
                                <x-form-floating-input
                                    type="number"
                                    min="0"
                                    name="selling_price"
                                    id="selling_price"
                                    label="Harga Jual"
                                    placeholder="Harga Jual"
                                    error="{{ $errors->has('selling_price') }}"
                                    value="{{ old('selling_price', $product->sellingPrice) }}"
                                    class="no-spinners"
                                />
                            </div>

                            <div class="col-12 col-lg-6">
                                <x-form-floating-input
                                    type="number"
                                    min="0"
                                    max="100"
                                    name="discount"
                                    id="discount"
                                    label="Diskon (%)"
                                    placeholder="Diskon (%)"
                                    error="{{ $errors->has('discount') }}"
                                    value="{{ old('discount', $product->discount) }}"
                                    class="no-spinners"
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
                        <h3 class="fs-5">Stok</h3>
                        <p class="text-body-secondary">Masukan stok produk.</p>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-6 mb-5 mb-lg-0">
                                <x-form-floating-input
                                    type="number"
                                    min="0"
                                    name="current_stock"
                                    id="current_stock"
                                    label="Stok Tersedia"
                                    placeholder="Stok Tersedia"
                                    error="{{ $errors->has('current_stock') }}"
                                    value="{{ old('current_stock', $product->currentStock) }}"
                                    class="no-spinners"
                                />
                            </div>

                            <div class="col-12 col-lg-6">
                                <x-form-floating-input
                                    type="number"
                                    min="0"
                                    name="minimum_stock"
                                    id="minimum_stock"
                                    label="Stok Minimum"
                                    placeholder="Stok Minimum"
                                    error="{{ $errors->has('minimum_stock') }}"
                                    value="{{ old('minimum_stock', $product->minimumStock) }}"
                                    class="no-spinners"
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