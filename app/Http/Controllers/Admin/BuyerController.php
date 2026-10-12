<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BuyerType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BuyerRequest;
use App\Models\Buyer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BuyerController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Buyer::class);

        $buyers = Buyer::query()
            ->when($request->query('q'), fn ($q, $term) => $q->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('phone', 'like', "%{$term}%")->orWhere('city', 'like', "%{$term}%")))
            ->when(BuyerType::tryFrom((string) $request->query('type')), fn ($q, $type) => $q->where('type', $type))
            ->when(in_array($request->query('status'), ['active', 'inactive'], true), fn ($q) => $q->where('is_active', $request->query('status') === 'active'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.buyers.index', compact('buyers'));
    }

    public function create(): View
    {
        Gate::authorize('create', Buyer::class);

        return view('admin.buyers.form', ['buyer' => new Buyer]);
    }

    public function store(BuyerRequest $request): RedirectResponse
    {
        Gate::authorize('create', Buyer::class);

        Buyer::create($request->validated());

        return redirect()->route('admin.buyers.index')->with('sukses', 'Pembeli ditambahkan.');
    }

    public function edit(Buyer $buyer): View
    {
        Gate::authorize('update', $buyer);

        return view('admin.buyers.form', compact('buyer'));
    }

    public function update(BuyerRequest $request, Buyer $buyer): RedirectResponse
    {
        Gate::authorize('update', $buyer);

        $buyer->update($request->validated());

        return redirect()->route('admin.buyers.index')->with('sukses', 'Data pembeli diperbarui.');
    }

    public function destroy(Buyer $buyer): RedirectResponse
    {
        if ($reason = $this->deletionBlockedReason($buyer)) {
            return back()->with('gagal', $reason);
        }

        $buyer->delete();

        return redirect()->route('admin.buyers.index')->with('sukses', 'Pembeli dihapus.');
    }
}
