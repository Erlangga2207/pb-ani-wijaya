<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Enums\ExpenseCategory;
use App\Enums\PaymentMethod;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExpenseService
{
    public function __construct(private DocumentNumberService $numbers) {}

    /**
     * @param  array{expense_date:string, category:string|ExpenseCategory, description:string, amount:float, method?:string|PaymentMethod}  $data
     */
    public function create(array $data, User $user): Expense
    {
        $date = Carbon::parse($data['expense_date']);
        $amount = round((float) $data['amount'], 2);

        if ($date->isAfter(today())) {
            throw ValidationException::withMessages(['expense_date' => 'Tanggal biaya tidak boleh di masa depan.']);
        }
        if ($amount <= 0) {
            throw ValidationException::withMessages(['amount' => 'Jumlah biaya harus lebih dari 0.']);
        }

        return DB::transaction(fn () => Expense::create([
            'number' => $this->numbers->next('BO', $date),
            'expense_date' => $date,
            'category' => $data['category'],
            'description' => $data['description'],
            'amount' => $amount,
            'method' => $data['method'] ?? PaymentMethod::Cash,
            'status' => DocumentStatus::Posted,
            'user_id' => $user->id,
        ]));
    }

    public function void(Expense $expense, string $reason, User $user): void
    {
        DB::transaction(fn () => $expense->lockForVoid($reason)->markVoid($reason, $user));
    }
}
