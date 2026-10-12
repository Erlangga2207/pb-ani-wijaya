<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

// Kartu stok: read-only, semua mutasi dari StockService.
class StockMovementController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', StockMovement::class);

        $showCost = $request->user()->isOwnerOrAbove();

        $movements = StockMovement::query()
            ->select(array_merge(['id', 'item_id', 'movement_date', 'direction', 'quantity_kg', 'balance_after_kg', 'source_type', 'source_id', 'description', 'user_id'], $showCost ? ['unit_cost'] : []))
            ->when($request->query('q'), fn ($q, $term) => $q->where('description', 'like', "%{$term}%"))
            ->when($request->query('item'), fn ($q, $id) => $q->where('item_id', $id))
            ->when(in_array($request->query('direction'), ['in', 'out'], true), fn ($q) => $q->where('direction', $request->query('direction')))
            ->when($request->query('from'), fn ($q, $date) => $q->whereDate('movement_date', '>=', $date))
            ->when($request->query('until'), fn ($q, $date) => $q->whereDate('movement_date', '<=', $date))
            ->with(['item:id,name', 'source', 'user:id,name'])
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.stock-movements.index', [
            'movements' => $movements,
            'showCost' => $showCost,
            'items' => Item::orderBy('code')->pluck('name', 'id'),
        ]);
    }
}
