<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function rules(): array
    {
        $isCreate = ! $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'password' => [$isCreate ? 'required' : 'nullable', 'string', 'min:8', 'confirmed'],
            // Hanya role yang boleh diberikan oleh user yang sedang login (owner tidak bisa membuat super_admin).
            'role' => ['required', Rule::in($this->user()->assignableRoles())],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'nama', 'password' => 'password', 'role' => 'role'];
    }

    public function messages(): array
    {
        return ['role.in' => 'Role ini tidak boleh Anda berikan.'];
    }

    public static function roleLabels(): array
    {
        return ['super_admin' => 'Super Admin', 'owner' => 'Pemilik (Owner)', 'staff' => 'Staf'];
    }
}
