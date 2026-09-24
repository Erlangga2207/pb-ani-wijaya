<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\HomeController;

// ============================================
// PUBLIC ROUTES (Website untuk pengunjung)
// ============================================
Route::get('/', [HomeController::class, 'beranda'])->name('home');
Route::get('/tentang-kami', [HomeController::class, 'tentangKami'])->name('about');
Route::get('/produk', [HomeController::class, 'produk'])->name('products');
Route::get('/galeri', [HomeController::class, 'galeri'])->name('gallery');
Route::get('/kontak', [HomeController::class, 'kontak'])->name('contact');
Route::post('/kontak', [HomeController::class, 'submitKontak'])->name('contact.submit');

// ============================================
// AUTHENTICATED ROUTES (Internal)
// ============================================
Route::get('/nota/{transaction}', [NotaController::class, 'show'])
    ->name('nota.show')
    ->middleware(['auth', 'role:super_admin|owner']);
