<?php

namespace App\Http\Requests\Admin;

use App\Enums\PaymentMethod;
use App\Enums\QuantityUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseRequest extends FormRequest
{
    // Aturan bisnis (total, jatuh tempo wajib bila belum lunas, dll.) dicek ulang di PurchaseService.
    public function rules(): array
    {
        return [
            'supplier_id' => ['nullable', 'required_without:new_supplier_name', 'exists:suppliers,id'],
            'new_supplier_name' => ['nullable', 'string', 'max:255'],
            'new_supplier_village' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['required', 'date', 'before_or_equal:today'],
            'item_id' => ['required', 'exists:items,id'],
            'input_quantity' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'input_unit' => ['required', Rule::enum(QuantityUnit::class)],
            'moisture_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'price_per_kg' => ['required', 'numeric', 'gt:0', 'max:9999999999999.99'],
            'other_cost' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'pay_now' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'payment_method' => ['nullable', Rule::enum(PaymentMethod::class)],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'supplier_id' => 'petani', 'new_supplier_name' => 'nama petani baru', 'purchase_date' => 'tanggal', 'item_id' => 'barang',
            'input_quantity' => 'jumlah', 'input_unit' => 'satuan', 'moisture_percent' => 'kadar air', 'price_per_kg' => 'harga per kg',
            'other_cost' => 'ongkos lain', 'pay_now' => 'bayar sekarang', 'payment_method' => 'metode', 'due_date' => 'jatuh tempo',
            'notes' => 'catatan',
        ];
    }

    public function messages(): array
    {
        return ['supplier_id.required_without' => 'Pilih petani atau isi nama petani baru.'];
    }
}
