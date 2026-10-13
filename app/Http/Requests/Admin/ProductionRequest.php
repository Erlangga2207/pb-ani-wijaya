<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductionRequest extends FormRequest
{
    // Aturan bisnis (tepat satu beras, total hasil ≤ gabah, stok cukup) dicek di ProductionService.
    public function rules(): array
    {
        return [
            'production_date' => ['required', 'date', 'before_or_equal:today'],
            'input_item_id' => ['required', 'exists:items,id'],
            'input_kg' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'outputs' => ['required', 'array'],
            'outputs.*' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'machine_downtime_minutes' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'production_date' => 'tanggal', 'input_item_id' => 'gabah', 'input_kg' => 'berat gabah', 'outputs' => 'hasil giling',
            'outputs.*' => 'hasil giling', 'machine_downtime_minutes' => 'mesin macet', 'notes' => 'catatan',
        ];
    }
}
