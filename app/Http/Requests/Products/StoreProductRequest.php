<?php

declare(strict_types=1);

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'category' => ['required', 'exists:categories,id'],
            'unit' => ['required', 'exists:units,id'],
            'code' => ['required', 'string', 'max:50', 'unique:products,code'],
            'barcode' => ['required', 'string', 'max:50', 'unique:products,barcode'],
            'name' => ['nullable', 'string', 'max:500'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'minimum_stock' => ['nullable', 'numeric', 'min:0'],
            'current_stock' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'image' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:1024'],
        ];
    }

    /**
     * Merubah nama atribut untuk pesan error.
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'category' => 'kategori',
            'unit' => 'unit',
            'code' => 'kode',
            'barcode' => 'barcode',
            'name' => 'nama (deskripsi)',
            'selling_price' => 'harga jual',
            'discount' => 'diskon',
            'minimum_stock' => 'stok minimal',
            'current_stock' => 'stok',
            'is_active' => 'status',
            'image' => 'gambar',
        ];
    }
}
