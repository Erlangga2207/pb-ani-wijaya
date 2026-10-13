<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

// Keuangan › Utang ke Petani & Piutang Pembeli: daftar dokumen belum lunas, urut jatuh tempo. Owner ke atas.
class DebtController extends Controller
{
    public function payables(Request $request): View
    {
        Gate::authorize('view-debts');

        $query = $this->outstanding(Purchase::query(), $request)
            ->when($request->query('q'), fn ($q, $term) => $q->where(fn ($q) => $q->where('number', 'like', "%{$term}%")
                ->orWhereHas('supplier', fn ($s) => $s->where('name', 'like', "%{$term}%"))));

        return view('admin.debts.payables', [
            'remaining' => $this->remainingTotal($query),
            'documents' => $query->with('supplier:id,name,village')->paginate(15)->withQueryString(),
        ]);
    }

    public function receivables(Request $request): View
    {
        Gate::authorize('view-debts');

        $query = $this->outstanding(Sale::query(), $request)
            ->when($request->query('q'), fn ($q, $term) => $q->where(fn ($q) => $q->where('number', 'like', "%{$term}%")
                ->orWhereHas('buyer', fn ($b) => $b->where('name', 'like', "%{$term}%"))));

        return view('admin.debts.receivables', [
            'remaining' => $this->remainingTotal($query),
            'documents' => $query->with('buyer:id,name,city,phone')->paginate(15)->withQueryString(),
        ]);
    }

    private function outstanding(Builder $query, Request $request): Builder
    {
        return $query->posted()
            ->where('payment_status', '!=', PaymentStatus::Paid)
            ->when($request->boolean('overdue'), fn ($q) => $q->whereNotNull('due_date')->where('due_date', '<', today()->toDateString()))
            // Yang belum punya jatuh tempo ditaruh paling bawah.
            ->orderByRaw('due_date IS NULL')
            ->orderBy('due_date')
            ->orderBy('id');
    }

    private function remainingTotal(Builder $query): float
    {
        return round((float) (clone $query)->reorder()->sum('total') - (float) (clone $query)->reorder()->sum('paid_amount'), 2);
    }
}
