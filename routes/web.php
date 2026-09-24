<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PurchaseReceiptController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleInvoiceController;
use Illuminate\Support\Facades\Route;

// ============================================
// PUBLIC ROUTES (Website untuk pengunjung)
// ============================================
Route::get('/', [HomeController::class, 'beranda'])->name('home');
Route::get('/tentang-kami', [HomeController::class, 'tentangKami'])->name('about');
Route::get('/produk', [HomeController::class, 'produk'])->name('products');
Route::get('/galeri', [HomeController::class, 'galeri'])->name('gallery');
Route::get('/kontak', [HomeController::class, 'kontak'])->name('contact');
Route::post('/kontak', [HomeController::class, 'submitKontak'])
    ->name('contact.submit')
    ->middleware('throttle:5,1'); // anti-spam: 5 kiriman / menit / IP

// ============================================
// AUTHENTICATED ROUTES (Internal)
// ============================================
Route::get('/nota/{sale}', [SaleInvoiceController::class, 'show'])
    ->name('sales.invoice')
    ->middleware(['auth', 'can:view,sale']);

Route::get('/pembelian/{purchase}/bukti', PurchaseReceiptController::class)
    ->name('purchases.receipt')
    ->middleware(['auth', 'can:view,purchase']);

Route::middleware(['auth', 'can:view-report,tab'])->prefix('laporan/{tab}')->group(function () {
    Route::get('/cetak', [ReportController::class, 'print'])->name('reports.print');
    Route::get('/csv', [ReportController::class, 'csv'])->name('reports.csv');
});
