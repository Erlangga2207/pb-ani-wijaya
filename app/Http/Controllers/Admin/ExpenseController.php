<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DocumentStatus;
use App\Enums\ExpenseCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExpenseRequest;
use App\Http\Requests\Admin\VoidRequest;
use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

// Keuangan › Biaya Operasional. Owner ke atas (ExpensePolicy). Tanpa edit/hapus, koreksi = void.
class ExpenseController extends Controller
{
    public function __construct(private ExpenseService $expenses) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Expense::class);

        $query = Expense::query()
            ->when($request->query('q'), fn ($q, $term) => $q->where(fn ($q) => $q->where('number', 'like', "%{$term}%")->orWhere('description', 'like', "%{$term}%")))
            ->when($request->query('from'), fn ($q, $date) => $q->whereDate('expense_date', '>=', $date))
            ->when($request->query('until'), fn ($q, $date) => $q->whereDate('expense_date', '<=', $date))
            ->when(ExpenseCategory::tryFrom((string) $request->query('category')), fn ($q, $category) => $q->where('category', $category))
            ->when(DocumentStatus::tryFrom((string) $request->query('status')), fn ($q, $status) => $q->where('status', $status));

        return view('admin.expenses.index', [
            'total' => (float) (clone $query)->posted()->sum('amount'),
            'expenses' => $query->latest('id')->paginate(15)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Expense::class);

        return view('admin.expenses.create');
    }

    public function store(ExpenseRequest $request): RedirectResponse
    {
        Gate::authorize('create', Expense::class);

        $expense = $this->expenses->create($request->validated(), $request->user());

        return redirect()->route('admin.expenses.index')->with('sukses', "Biaya {$expense->number} tercatat.");
    }

    public function show(Expense $expense): View
    {
        Gate::authorize('view', $expense);

        $expense->load(['user:id,name', 'voidedBy:id,name']);

        return view('admin.expenses.show', compact('expense'));
    }

    public function void(VoidRequest $request, Expense $expense): RedirectResponse
    {
        Gate::authorize('void', $expense);

        $this->expenses->void($expense, $request->validated('reason'), $request->user());

        return redirect()->route('admin.expenses.show', $expense)->with('sukses', "Biaya {$expense->number} dibatalkan.");
    }
}
