<?php

namespace App\Http\Requests\Admin;

use App\Enums\ItemType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:10', Rule::unique('items', 'code')->ignore($this->route('item'))],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(ItemType::class)],
            'product_id' => ['nullable', 'exists:products,id'],
            'weight_per_sack_kg' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'min_stock_kg' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'default_price_per_kg' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'kode',
            'name' => 'nama barang',
            'type' => 'tipe',
            'product_id' => 'produk katalog',
            'weight_per_sack_kg' => 'berat per karung',
            'min_stock_kg' => 'batas minimum stok',
            'default_price_per_kg' => 'harga pasar',
        ];
    }
}
