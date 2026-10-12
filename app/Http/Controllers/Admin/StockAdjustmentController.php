<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DocumentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StockAdjustmentRequest;
use App\Http\Requests\Admin\VoidRequest;
use App\Models\Item;
use App\Models\StockAdjustment;
use App\Services\StockAdjustmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class StockAdjustmentController extends Controller
{
    public function __construct(private StockAdjustmentService $adjustments) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', StockAdjustment::class);

        $adjustments = StockAdjustment::query()
            ->when($request->query('q'), fn ($q, $term) => $q->where('number', 'like', "%{$term}%"))
            ->when($request->query('item'), fn ($q, $id) => $q->where('item_id', $id))
            ->when(DocumentStatus::tryFrom((string) $request->query('status')), fn ($q, $status) => $q->where('status', $status))
            ->with('item:id,name')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.stock-adjustments.index', [
            'adjustments' => $adjustments,
            'items' => Item::orderBy('code')->pluck('name', 'id'),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', StockAdjustment::class);

        return view('admin.stock-adjustments.create', [
            'items' => Item::where('is_active', true)->orderBy('code')->get(['id', 'name', 'stock_kg']),
        ]);
    }

    public function store(StockAdjustmentRequest $request): RedirectResponse
    {
        Gate::authorize('create', StockAdjustment::class);

        $adjustment = $this->adjustments->create($request->validated(), $request->user());

        return redirect()->route('admin.stock-adjustments.show', $adjustment)->with('sukses', "Stock opname {$adjustment->number} tersimpan.");
    }

    public function show(StockAdjustment $stockAdjustment): View
    {
        Gate::authorize('view', $stockAdjustment);

        $stockAdjustment->load(['item:id,name', 'user:id,name', 'voidedBy:id,name']);

        return view('admin.stock-adjustments.show', ['adjustment' => $stockAdjustment]);
    }

    public function void(VoidRequest $request, StockAdjustment $stockAdjustment): RedirectResponse
    {
        Gate::authorize('void', $stockAdjustment);

        $this->adjustments->void($stockAdjustment, $request->validated('reason'), $request->user());

        return redirect()->route('admin.stock-adjustments.show', $stockAdjustment)->with('sukses', "Stock opname {$stockAdjustment->number} dibatalkan.");
    }
}
