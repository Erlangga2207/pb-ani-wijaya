<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BuyerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Didaftarkan di bootstrap/app.php: prefix /panel, nama admin.*, middleware web.

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->middleware('throttle:5,1')->name('login.attempt');
});

Route::middleware(['auth', 'active', 'role:super_admin|owner|staff'])->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/', DashboardController::class)->name('dashboard');

    // Master Data (otorisasi per aksi lewat Policy di controller)
    Route::resource('barang', ItemController::class)->names('items')->parameters(['barang' => 'item']);
    Route::resource('petani', SupplierController::class)->except('show')->names('suppliers')->parameters(['petani' => 'supplier']);
    Route::resource('pembeli', BuyerController::class)->except('show')->names('buyers')->parameters(['pembeli' => 'buyer']);

    // Pengaturan
    Route::resource('pengguna', UserController::class)->except('show')->names('users')->parameters(['pengguna' => 'user']);
});
