<?php

namespace App\Http\Requests\Admin;

use App\Enums\BuyerType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BuyerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(BuyerType::class)],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'nama', 'type' => 'tipe', 'phone' => 'no. HP', 'city' => 'kota', 'province' => 'provinsi', 'country' => 'negara', 'address' => 'alamat', 'notes' => 'catatan'];
    }
}
