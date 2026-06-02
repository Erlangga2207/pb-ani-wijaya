<?php

namespace App\Http\Controllers;

use App\Models\StockTransaction;
use Illuminate\View\View;

class NotaController extends Controller
{
    /**
     * Tampilkan halaman nota untuk transaksi tertentu
     */
    public function show(StockTransaction $transaction): View
    {
        // Eager load relasi supaya tidak ada N+1 query
        $transaction->load(['product', 'supplier', 'buyer', 'user']);

        return view('nota.show', compact('transaction'));
    }
}
