<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Supplier::class);

        $suppliers = Supplier::query()
            ->when($request->query('q'), fn ($q, $term) => $q->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('phone', 'like', "%{$term}%")))
            ->when($request->query('village'), fn ($q, $village) => $q->where('village', $village))
            ->when(in_array($request->query('status'), ['active', 'inactive'], true), fn ($q) => $q->where('is_active', $request->query('status') === 'active'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $villages = Supplier::whereNotNull('village')->distinct()->orderBy('village')->pluck('village');

        return view('admin.suppliers.index', compact('suppliers', 'villages'));
    }

    public function create(): View
    {
        Gate::authorize('create', Supplier::class);

        return view('admin.suppliers.form', ['supplier' => new Supplier]);
    }

    public function store(SupplierRequest $request): RedirectResponse
    {
        Gate::authorize('create', Supplier::class);

        Supplier::create($request->validated());

        return redirect()->route('admin.suppliers.index')->with('sukses', 'Petani ditambahkan.');
    }

    public function edit(Supplier $supplier): View
    {
        Gate::authorize('update', $supplier);

        return view('admin.suppliers.form', compact('supplier'));
    }

    public function update(SupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        Gate::authorize('update', $supplier);

        $supplier->update($request->validated());

        return redirect()->route('admin.suppliers.index')->with('sukses', 'Data petani diperbarui.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($reason = $this->deletionBlockedReason($supplier)) {
            return back()->with('gagal', $reason);
        }

        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('sukses', 'Petani dihapus.');
    }
}
