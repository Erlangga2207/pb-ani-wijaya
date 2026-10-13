<?php

namespace App\Http\Requests\Admin;

use App\Enums\LeadStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Tindak lanjut pesan/calon pembeli oleh admin.
class ContactMessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(LeadStatus::class)],
            'phone' => ['nullable', 'string', 'max:30'],
            'company' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return ['phone' => 'no. HP', 'company' => 'perusahaan', 'country' => 'negara', 'admin_notes' => 'catatan internal'];
    }
}
