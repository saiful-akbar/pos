<?php

declare(strict_types=1);

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class UpdateProductRequest extends FormRequest
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
            'category_id' => ['required', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:50', "unique:products,code,{$this->productId},id"],
            'name' => ['nullable', 'string', 'max:200'],
            'image' => ['nullable', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'minimum_stock' => ['nullable', 'numeric', 'min:0'],
            'current_stock' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    /**
     * Merubah nama attribut untuk pesan error.
     *
     * @return void
     */
    #[Override]
    public function attributes()
    {
        return [
            'category_id' => 'kategori',
            'code' => 'kode',
            'name' => 'nama atau deskripsi',
            'image' => 'gambar atau foto',
            'minimum_stock' => 'stok minimum',
            'current_stock' => 'stok tersedia',
            'discount' => 'diskon',
            'is_active' => 'status keaktifan',
        ];
    }
}
