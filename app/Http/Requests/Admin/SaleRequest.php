<?php

namespace App\Http\Requests\Admin;

use App\Enums\PaymentMethod;
use App\Enums\QuantityUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaleRequest extends FormRequest
{
    // Aturan bisnis (stok cukup, diskon ≤ subtotal, jatuh tempo bila belum lunas) dicek di SaleService.
    public function rules(): array
    {
        return [
            'buyer_id' => ['nullable', 'required_without:new_buyer_name', 'exists:buyers,id'],
            'new_buyer_name' => ['nullable', 'string', 'max:255'],
            'new_buyer_city' => ['nullable', 'string', 'max:255'],
            'sale_date' => ['required', 'date', 'before_or_equal:today'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'distinct', 'exists:items,id'],
            'items.*.input_quantity' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'items.*.input_unit' => ['required', Rule::enum(QuantityUnit::class)],
            'items.*.price_per_kg' => ['required', 'numeric', 'gt:0', 'max:9999999999999.99'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'pay_now' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'payment_method' => ['nullable', Rule::enum(PaymentMethod::class)],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'buyer_id' => 'pembeli', 'new_buyer_name' => 'nama pembeli baru', 'sale_date' => 'tanggal', 'items' => 'barang',
            'items.*.item_id' => 'barang', 'items.*.input_quantity' => 'jumlah', 'items.*.input_unit' => 'satuan',
            'items.*.price_per_kg' => 'harga per kg', 'discount' => 'diskon', 'shipping_cost' => 'ongkir', 'pay_now' => 'bayar sekarang',
            'payment_method' => 'metode', 'due_date' => 'jatuh tempo', 'notes' => 'catatan',
        ];
    }

    public function messages(): array
    {
        return [
            'buyer_id.required_without' => 'Pilih pembeli atau isi nama pembeli baru.',
            'items.*.item_id.distinct' => 'Barang tidak boleh dobel dalam satu nota.',
        ];
    }
}
