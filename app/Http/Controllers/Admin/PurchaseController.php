<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DocumentStatus;
use App\Enums\ItemType;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PurchaseRequest;
use App\Http\Requests\Admin\VoidRequest;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function __construct(private PurchaseService $purchases) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Purchase::class);

        $query = Purchase::query()
            ->when($request->query('q'), fn ($q, $term) => $q->where('number', 'like', "%{$term}%"))
            ->when($request->query('from'), fn ($q, $date) => $q->whereDate('purchase_date', '>=', $date))
            ->when($request->query('until'), fn ($q, $date) => $q->whereDate('purchase_date', '<=', $date))
            ->when($request->query('supplier'), fn ($q, $id) => $q->where('supplier_id', $id))
            ->when($request->query('village'), fn ($q, $village) => $q->whereHas('supplier', fn ($s) => $s->where('village', $village)))
            ->when(PaymentStatus::tryFrom((string) $request->query('payment_status')), fn ($q, $status) => $q->where('payment_status', $status))
            ->when(DocumentStatus::tryFrom((string) $request->query('status')), fn ($q, $status) => $q->where('status', $status));

        // Ringkasan footer: hanya dokumen tercatat (bukan batal), sesuai filter.
        $totals = (clone $query)->posted()->selectRaw('COALESCE(SUM(quantity_kg), 0) AS kg, COALESCE(SUM(total), 0) AS total')->first();

        return view('admin.purchases.index', [
            'purchases' => $query->with('supplier')->latest('id')->paginate(15)->withQueryString(),
            'totals' => $totals,
            'suppliers' => Supplier::orderBy('name')->pluck('name', 'id'),
            'villages' => Supplier::whereNotNull('village')->distinct()->orderBy('village')->pluck('village'),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Purchase::class);

        return view('admin.purchases.create', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name', 'village']),
            'items' => Item::ofType(ItemType::Raw)->where('is_active', true)->orderBy('code')->get(['id', 'code', 'name', 'weight_per_sack_kg', 'default_price_per_kg']),
        ]);
    }

    public function store(PurchaseRequest $request): RedirectResponse
    {
        Gate::authorize('create', Purchase::class);

        $data = $request->validated();

        $purchase = DB::transaction(function () use ($data, $request) {
            // Petani baru langsung dari form pembelian (seperti tombol "+" di Filament).
            if (empty($data['supplier_id'])) {
                Gate::authorize('create', Supplier::class);
                $data['supplier_id'] = Supplier::create([
                    'name' => $data['new_supplier_name'],
                    'village' => $data['new_supplier_village'] ?? null,
                ])->id;
            }

            return $this->purchases->create($data, $request->user());
        });

        return redirect()->route('admin.purchases.show', $purchase)->with('sukses', "Pembelian {$purchase->number} tersimpan.");
    }

    public function show(Purchase $purchase): View
    {
        Gate::authorize('view', $purchase);

        $purchase->load(['supplier', 'item', 'user', 'voidedBy', 'payments' => fn ($q) => $q->with('user')->orderBy('id')]);

        return view('admin.purchases.show', compact('purchase'));
    }

    public function void(VoidRequest $request, Purchase $purchase): RedirectResponse
    {
        Gate::authorize('void', $purchase);

        $this->purchases->void($purchase, $request->validated('reason'), $request->user());

        return redirect()->route('admin.purchases.show', $purchase)->with('sukses', "Pembelian {$purchase->number} dibatalkan.");
    }
}
