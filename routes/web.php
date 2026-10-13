<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PurchaseReceiptController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleInvoiceController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

// ============================================
// PUBLIC ROUTES (Website untuk pengunjung)
// ============================================
// Didaftarkan dua kali: tanpa prefix (bahasa default, nama route tetap: home, products, ...)
// dan dengan prefix bahasa (/en/produk, nama route: localized.products). Path tetap slug Indonesia.
// Link di Blade publik memakai localized_route('products').
$publicPages = function () {
    Route::get('/', [HomeController::class, 'beranda'])->name('home');
    Route::get('/tentang-kami', [HomeController::class, 'tentangKami'])->name('about');
    Route::get('/produk', [HomeController::class, 'produk'])->name('products');
    Route::get('/galeri', [HomeController::class, 'galeri'])->name('gallery');
    Route::get('/kontak', [HomeController::class, 'kontak'])->name('contact');
};

Route::middleware(SetLocale::class)->group($publicPages);

Route::middleware(SetLocale::class)
    ->prefix('{locale}')
    ->where(['locale' => implode('|', array_slice(array_keys(config('locales')), 1))])
    ->name('localized.')
    ->group($publicPages);

// Satu route POST untuk semua bahasa; bahasa asal dikirim lewat field "locale".
Route::post('/kontak', [HomeController::class, 'submitKontak'])
    ->name('contact.submit')
    ->middleware('throttle:5,1'); // anti-spam: 5 kiriman / menit / IP

Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');

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

// Panel Filament lama (/admin) dihapus di PRD-08 Fase 7: bookmark lama diarahkan permanen ke panel baru.
Route::permanentRedirect('/admin', '/panel');
Route::permanentRedirect('/admin/{any}', '/panel')->where('any', '.*');
