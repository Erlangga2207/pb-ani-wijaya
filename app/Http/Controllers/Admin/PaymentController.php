<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentRequest;
use App\Http\Requests\Admin\VoidRequest;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Sale;
use App\Services\PaymentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

// Pembayaran tidak punya menu sendiri: dicatat dari detail pembelian/penjualan atau halaman Utang/Piutang.
class PaymentController extends Controller
{
    public function __construct(private PaymentService $payments) {}

    public function storeForPurchase(PaymentRequest $request, Purchase $purchase): RedirectResponse
    {
        return $this->record($request, $purchase);
    }

    public function storeForSale(PaymentRequest $request, Sale $sale): RedirectResponse
    {
        return $this->record($request, $sale);
    }

    public function void(VoidRequest $request, Payment $payment): RedirectResponse
    {
        Gate::authorize('void', $payment);

        $this->payments->void($payment, $request->validated('reason'), $request->user());

        return back()->with('sukses', "Pembayaran {$payment->number} dibatalkan.");
    }

    private function record(PaymentRequest $request, Model $payable): RedirectResponse
    {
        Gate::authorize('view', $payable);
        Gate::authorize('create', Payment::class);

        $payment = $this->payments->record($payable, $request->validated(), $request->user());

        return back()->with('sukses', "Pembayaran {$payment->number} tercatat.");
    }
}
