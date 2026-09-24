<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\PaymentStatus;
use App\Filament\Admin\Resources\Purchases\PurchaseResource;
use App\Filament\Admin\Resources\Sales\SaleResource;
use App\Models\Purchase;
use App\Models\Sale;
use Filament\Widgets\Widget;

// Piutang & utang yang lewat jatuh tempo (5 teratas masing-masing).
class OverdueWidget extends Widget
{
    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.admin.widgets.overdue';

    public static function canView(): bool
    {
        return auth()->user()->isOwnerOrAbove();
    }

    protected function getViewData(): array
    {
        $overdue = fn ($model) => $model::query()
            ->posted()
            ->where('payment_status', '!=', PaymentStatus::Paid)
            ->whereNotNull('due_date')
            ->where('due_date', '<', today()->toDateString())
            ->orderBy('due_date')
            ->limit(5);

        return [
            'receivables' => $overdue(Sale::class)->with('buyer')->get()
                ->map(fn (Sale $s) => ['url' => SaleResource::getUrl('view', ['record' => $s]), 'number' => $s->number, 'name' => $s->buyer->name, 'due' => $s->due_date, 'amount' => $s->remainingAmount()]),
            'payables' => $overdue(Purchase::class)->with('supplier')->get()
                ->map(fn (Purchase $p) => ['url' => PurchaseResource::getUrl('view', ['record' => $p]), 'number' => $p->number, 'name' => $p->supplier->name, 'due' => $p->due_date, 'amount' => $p->remainingAmount()]),
        ];
    }
}
