<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StockAdjustmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'adjustment_date' => ['required', 'date', 'before_or_equal:today'],
            'item_id' => ['required', 'exists:items,id'],
            'physical_kg' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return ['adjustment_date' => 'tanggal', 'item_id' => 'barang', 'physical_kg' => 'stok fisik', 'reason' => 'alasan'];
    }
}
