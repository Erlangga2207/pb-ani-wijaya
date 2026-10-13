<?php

namespace App\Http\Requests\Admin;

use App\Enums\ExpenseCategory;
use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'expense_date' => ['required', 'date', 'before_or_equal:today'],
            'category' => ['required', Rule::enum(ExpenseCategory::class)],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:9999999999999.99'],
            'method' => ['required', Rule::enum(PaymentMethod::class)],
        ];
    }

    public function attributes(): array
    {
        return ['expense_date' => 'tanggal', 'category' => 'kategori', 'description' => 'keterangan', 'amount' => 'jumlah', 'method' => 'metode'];
    }
}
