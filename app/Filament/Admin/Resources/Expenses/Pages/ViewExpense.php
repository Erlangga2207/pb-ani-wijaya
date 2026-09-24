<?php

namespace App\Filament\Admin\Resources\Expenses\Pages;

use App\Filament\Admin\Actions\VoidAction;
use App\Filament\Admin\Resources\Expenses\ExpenseResource;
use App\Models\Expense;
use App\Services\ExpenseService;
use Filament\Resources\Pages\ViewRecord;

class ViewExpense extends ViewRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            VoidAction::make(fn (Expense $record, string $reason) => app(ExpenseService::class)->void($record, $reason, auth()->user())),
        ];
    }
}
