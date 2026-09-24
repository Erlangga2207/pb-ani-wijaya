<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\View\View;

class SaleInvoiceController extends Controller
{
    public function show(Sale $sale): View
    {
        $sale->load(['buyer', 'items.item', 'user']);

        return view('sales.invoice', compact('sale'));
    }
}
