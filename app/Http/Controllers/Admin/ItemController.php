<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ItemType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ItemRequest;
use App\Models\Item;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ItemController extends Controller
{
    // Kolom biaya (HPP rata-rata) tidak di-query untuk staff.
    private const COST_COLUMNS = ['avg_cost_per_kg'];

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Item::class);

        $items = Item::query()
            ->select($this->visibleColumns($request))
            ->when($request->query('q'), fn ($q, $term) => $q->where(fn ($q) => $q->where('code', 'like', "%{$term}%")->orWhere('name', 'like', "%{$term}%")))
            ->when(ItemType::tryFrom((string) $request->query('type')), fn ($q, $type) => $q->where('type', $type))
            ->orderBy('code')
            ->paginate(15)
            ->withQueryString();

        return view('admin.items.index', [
            'items' => $items,
            'showCost' => $request->user()->isOwnerOrAbove(),
        ]);
    }

    public function show(Request $request, int $item): View
    {
        $item = Item::select($this->visibleColumns($request))->with('product')->findOrFail($item);
        Gate::authorize('view', $item);

        $showCost = $request->user()->isOwnerOrAbove();
        $movements = $item->stockMovements()
            ->select(array_merge(['id', 'movement_date', 'direction', 'quantity_kg', 'balance_after_kg', 'source_type', 'source_id', 'description', 'user_id'], $showCost ? ['unit_cost'] : []))
            ->with(['source', 'user:id,name'])
            ->latest('movement_date')
            ->latest('id')
            ->paginate(15);

        return view('admin.items.show', compact('item', 'movements', 'showCost'));
    }

    public function create(): View
    {
        Gate::authorize('create', Item::class);

        return $this->form(new Item(['weight_per_sack_kg' => 25, 'is_active' => true]));
    }

    public function store(ItemRequest $request): RedirectResponse
    {
        Gate::authorize('create', Item::class);

        $item = Item::create($request->validated());

        return redirect()->route('admin.items.show', $item)->with('sukses', 'Barang ditambahkan.');
    }

    public function edit(Item $item): View
    {
        Gate::authorize('update', $item);

        return $this->form($item);
    }

    public function update(ItemRequest $request, Item $item): RedirectResponse
    {
        Gate::authorize('update', $item);

        $item->update($request->validated());

        return redirect()->route('admin.items.show', $item)->with('sukses', 'Barang diperbarui.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        if ($reason = $this->deletionBlockedReason($item)) {
            return back()->with('gagal', $reason);
        }

        $item->delete();

        return redirect()->route('admin.items.index')->with('sukses', 'Barang dihapus.');
    }

    private function form(Item $item): View
    {
        return view('admin.items.form', [
            'item' => $item,
            'products' => Product::orderBy('id')->get()->pluck('name', 'id'), // name = JSON terjemahan, ambil lewat model
        ]);
    }

    private function visibleColumns(Request $request): array
    {
        $columns = ['id', 'code', 'name', 'type', 'product_id', 'weight_per_sack_kg', 'stock_kg', 'min_stock_kg', 'default_price_per_kg', 'is_active'];

        return $request->user()->isOwnerOrAbove() ? array_merge($columns, self::COST_COLUMNS) : $columns;
    }
}
