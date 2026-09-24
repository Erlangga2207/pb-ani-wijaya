<?php

namespace App\Models;

use App\Enums\ExpenseCategory;
use App\Enums\PaymentMethod;
use App\Models\Concerns\IsDocument;
use Illuminate\Database\Eloquent\Model;

// Biaya operasional (gaji, listrik, solar, dll). Tidak bisa diedit/dihapus, hanya void.
class Expense extends Model
{
    use IsDocument;

    protected $guarded = ['id'];

    protected $casts = [
        'expense_date' => 'date',
        'category' => ExpenseCategory::class,
        'method' => PaymentMethod::class,
        'amount' => 'decimal:2',
    ];
}
