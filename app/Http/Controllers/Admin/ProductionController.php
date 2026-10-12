<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DocumentStatus;
use App\Enums\ItemType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductionRequest;
use App\Http\Requests\Admin\VoidRequest;
use App\Models\Item;
use App\Models\Production;
use App\Services\ProductionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProductionController extends Controller
{
    // Kolom biaya: tidak di-query untuk staff.
    private const COST_COLUMNS = ['input_cost_per_kg', 'input_total_cost'];

    public function __construct(private ProductionService $productions) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Production::class);

        $query = Production::query()
            ->when($request->query('q'), fn ($q, $term) => $q->where('number', 'like', "%{$term}%"))
            ->when($request->query('from'), fn ($q, $date) => $q->whereDate('production_date', '>=', $date))
            ->when($request->query('until'), fn ($q, $date) => $q->whereDate('production_date', '<=', $date))
            ->when(DocumentStatus::tryFrom((string) $request->query('status')), fn ($q, $status) => $q->where('status', $status));

        $totals = (clone $query)->posted()
            ->selectRaw('COALESCE(SUM(input_kg), 0) AS input_kg, COALESCE(SUM(main_output_kg), 0) AS output_kg')
            ->first();
        // Rata-rata tertimbang: Σ beras ÷ Σ gabah (bukan rata-rata persen biasa).
        $weightedYield = $totals->input_kg > 0 ? round($totals->output_kg / $totals->input_kg * 100, 2) : null;

        return view('admin.productions.index', [
            'productions' => $query->select($this->columns($request))->latest('id')->paginate(15)->withQueryString(),
            'totals' => $totals,
            'weightedYield' => $weightedYield,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Production::class);

        return view('admin.productions.create', [
            'inputs' => Item::ofType(ItemType::Raw)->where('is_active', true)->orderBy('code')->get(['id', 'name', 'stock_kg']),
            'outputs' => Item::ofType(ItemType::Finished, ItemType::Byproduct)
                ->where('is_active', true)
                ->orderByRaw("CASE WHEN type = 'finished' THEN 0 ELSE 1 END")
                ->orderBy('id')
                ->get(['id', 'name', 'type']),
        ]);
    }

    public function store(ProductionRequest $request): RedirectResponse
    {
        Gate::authorize('create', Production::class);

        $production = $this->productions->create($request->validated(), $request->user());

        $redirect = redirect()->route('admin.productions.show', $production)->with('sukses', "Produksi {$production->number} tersimpan.");

        // Rendemen di luar batas normal: tetap tersimpan, hanya diberi peringatan.
        if ($production->isYieldOutOfRange()) {
            $redirect->with('peringatan', sprintf(
                'Rendemen %s%% di luar batas normal (%s–%s%%). Periksa timbangan / kualitas gabah.',
                number_format((float) $production->yield_percent, 2, ',', '.'),
                config('production.yield_min'),
                config('production.yield_max'),
            ));
        }

        return $redirect;
    }

    public function show(Request $request, int $production): View
    {
        $showCost = $request->user()->isOwnerOrAbove();

        $production = Production::select($this->columns($request))
            ->with([
                'inputItem:id,name',
                'user:id,name',
                'voidedBy:id,name',
                'outputs' => fn ($q) => $q->select(array_merge(['id', 'production_id', 'item_id', 'quantity_kg'], $showCost ? ['unit_cost'] : []))->with('item:id,name'),
            ])
            ->findOrFail($production);
        Gate::authorize('view', $production);

        return view('admin.productions.show', compact('production', 'showCost'));
    }

    public function void(VoidRequest $request, Production $production): RedirectResponse
    {
        Gate::authorize('void', $production);

        $this->productions->void($production, $request->validated('reason'), $request->user());

        return redirect()->route('admin.productions.show', $production)->with('sukses', "Produksi {$production->number} dibatalkan.");
    }

    private function columns(Request $request): array
    {
        $columns = ['id', 'number', 'production_date', 'input_item_id', 'input_kg', 'main_output_kg', 'yield_percent', 'shrinkage_kg',
            'machine_downtime_minutes', 'notes', 'status', 'void_reason', 'voided_at', 'voided_by', 'user_id', 'created_at'];

        return $request->user()->isOwnerOrAbove() ? array_merge($columns, self::COST_COLUMNS) : $columns;
    }
}
