<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\View\View;

class PurchaseReceiptController extends Controller
{
    public function __invoke(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'item', 'user']);

        return view('purchases.receipt', compact('purchase'));
    }
}
