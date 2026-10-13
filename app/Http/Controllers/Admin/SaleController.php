<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DocumentStatus;
use App\Enums\ItemType;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaleRequest;
use App\Http\Requests\Admin\VoidRequest;
use App\Models\Buyer;
use App\Models\Item;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function __construct(private SaleService $sales) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Sale::class);

        $query = Sale::query()
            // Staff hanya melihat nota yang dia buat sendiri (sesuai SalePolicy::view).
            ->when(! $request->user()->isOwnerOrAbove(), fn ($q) => $q->where('user_id', $request->user()->id))
            ->when($request->query('q'), fn ($q, $term) => $q->where('number', 'like', "%{$term}%"))
            ->when($request->query('from'), fn ($q, $date) => $q->whereDate('sale_date', '>=', $date))
            ->when($request->query('until'), fn ($q, $date) => $q->whereDate('sale_date', '<=', $date))
            ->when($request->query('buyer'), fn ($q, $id) => $q->where('buyer_id', $id))
            ->when(PaymentStatus::tryFrom((string) $request->query('payment_status')), fn ($q, $status) => $q->where('payment_status', $status))
            ->when(DocumentStatus::tryFrom((string) $request->query('status')), fn ($q, $status) => $q->where('status', $status));

        $total = (float) (clone $query)->posted()->sum('total');

        return view('admin.sales.index', [
            'sales' => $query->select($this->columns($request))->with('buyer:id,name,city')->withSum('items', 'quantity_kg')
                ->latest('id')->paginate(15)->withQueryString(),
            'total' => $total,
            'buyers' => Buyer::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Sale::class);

        return view('admin.sales.create', [
            'buyers' => Buyer::where('is_active', true)->orderBy('name')->get(['id', 'name', 'city']),
            'items' => Item::ofType(ItemType::Finished, ItemType::Byproduct)
                ->where('is_active', true)
                ->where('stock_kg', '>', 0)
                ->orderBy('id')
                ->get(['id', 'name', 'stock_kg', 'weight_per_sack_kg', 'default_price_per_kg']),
        ]);
    }

    public function store(SaleRequest $request): RedirectResponse
    {
        Gate::authorize('create', Sale::class);

        $data = $request->validated();

        $sale = DB::transaction(function () use ($data, $request) {
            // Pembeli baru langsung dari form penjualan (seperti tombol "+" di Filament).
            if (empty($data['buyer_id'])) {
                Gate::authorize('create', Buyer::class);
                $data['buyer_id'] = Buyer::create([
                    'name' => $data['new_buyer_name'],
                    'city' => $data['new_buyer_city'] ?? null,
                ])->id;
            }

            return $this->sales->create($data, $request->user());
        });

        return redirect()->route('admin.sales.show', $sale)->with('sukses', "Penjualan {$sale->number} tersimpan.");
    }

    public function show(Request $request, int $sale): View
    {
        $showCost = $request->user()->isOwnerOrAbove();

        $sale = Sale::select($this->columns($request))
            ->with([
                'buyer:id,name,city,phone',
                'user:id,name',
                'voidedBy:id,name',
                'items' => fn ($q) => $q->select(array_merge(['id', 'sale_id', 'item_id', 'input_quantity', 'input_unit', 'quantity_kg', 'price_per_kg', 'line_total'], $showCost ? ['cogs_per_kg'] : []))->with('item:id,name'),
                'payments' => fn ($q) => $q->with('user:id,name')->orderBy('id'),
            ])
            ->findOrFail($sale);
        Gate::authorize('view', $sale);

        return view('admin.sales.show', compact('sale', 'showCost'));
    }

    public function void(VoidRequest $request, Sale $sale): RedirectResponse
    {
        Gate::authorize('void', $sale);

        $this->sales->void($sale, $request->validated('reason'), $request->user());

        return redirect()->route('admin.sales.show', $sale)->with('sukses', "Penjualan {$sale->number} dibatalkan.");
    }

    // HPP (cogs_total) tidak di-query untuk staff.
    private function columns(Request $request): array
    {
        $columns = ['id', 'number', 'sale_date', 'buyer_id', 'subtotal', 'discount', 'shipping_cost', 'total', 'paid_amount',
            'payment_status', 'due_date', 'notes', 'status', 'void_reason', 'voided_at', 'voided_by', 'user_id', 'created_at'];

        return $request->user()->isOwnerOrAbove() ? array_merge($columns, ['cogs_total']) : $columns;
    }
}
