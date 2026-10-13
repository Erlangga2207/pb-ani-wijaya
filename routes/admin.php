<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BuyerController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DebtController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductionController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\StockAdjustmentController;
use App\Http\Controllers\Admin\StockMovementController;
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

    // Transaksi: tanpa edit/hapus. Koreksi = void (alasan wajib). Otorisasi per aksi lewat Policy di controller.
    Route::resource('pembelian', PurchaseController::class)->only(['index', 'create', 'store', 'show'])->names('purchases')->parameters(['pembelian' => 'purchase']);
    Route::post('pembelian/{purchase}/batal', [PurchaseController::class, 'void'])->name('purchases.void');
    Route::post('pembelian/{purchase}/pembayaran', [PaymentController::class, 'storeForPurchase'])->name('purchases.payments.store');

    Route::resource('produksi', ProductionController::class)->only(['index', 'create', 'store', 'show'])->names('productions')->parameters(['produksi' => 'production']);
    Route::post('produksi/{production}/batal', [ProductionController::class, 'void'])->name('productions.void');

    Route::resource('penjualan', SaleController::class)->only(['index', 'create', 'store', 'show'])->names('sales')->parameters(['penjualan' => 'sale']);
    Route::post('penjualan/{sale}/batal', [SaleController::class, 'void'])->name('sales.void');
    Route::post('penjualan/{sale}/pembayaran', [PaymentController::class, 'storeForSale'])->name('sales.payments.store');

    Route::post('pembayaran/{payment}/batal', [PaymentController::class, 'void'])->name('payments.void');

    // Stok
    Route::get('kartu-stok', [StockMovementController::class, 'index'])->name('stock-movements.index');
    Route::resource('opname', StockAdjustmentController::class)->only(['index', 'create', 'store', 'show'])->names('stock-adjustments')->parameters(['opname' => 'stockAdjustment']);
    Route::post('opname/{stockAdjustment}/batal', [StockAdjustmentController::class, 'void'])->name('stock-adjustments.void');

    // Keuangan
    Route::get('utang', [DebtController::class, 'payables'])->name('payables.index');
    Route::get('piutang', [DebtController::class, 'receivables'])->name('receivables.index');
    Route::resource('biaya', ExpenseController::class)->only(['index', 'create', 'store', 'show'])->names('expenses')->parameters(['biaya' => 'expense']);
    Route::post('biaya/{expense}/batal', [ExpenseController::class, 'void'])->name('expenses.void');
    // Cetak & CSV memakai route yang sudah ada: reports.print / reports.csv (routes/web.php).
    Route::get('laporan', ReportController::class)->name('reports.index');

    // Master Data (otorisasi per aksi lewat Policy di controller)
    Route::resource('barang', ItemController::class)->names('items')->parameters(['barang' => 'item']);
    Route::resource('petani', SupplierController::class)->except('show')->names('suppliers')->parameters(['petani' => 'supplier']);
    Route::resource('pembeli', BuyerController::class)->except('show')->names('buyers')->parameters(['pembeli' => 'buyer']);

    // Website (owner ke atas). Teks yang bisa diterjemahkan memakai tab bahasa.
    Route::middleware('can:manage-website')->group(function () {
        Route::resource('galeri', GalleryController::class)->except('show')->names('galleries')->parameters(['galeri' => 'gallery']);
        Route::resource('katalog', ProductController::class)->except('show')->names('products')->parameters(['katalog' => 'product']);
        Route::get('pengaturan-website', [SiteSettingController::class, 'edit'])->name('site-settings.edit');
        Route::put('pengaturan-website', [SiteSettingController::class, 'update'])->name('site-settings.update');
        Route::resource('pesan', ContactMessageController::class)->only(['index', 'show', 'update', 'destroy'])->names('contact-messages')->parameters(['pesan' => 'contactMessage']);
        Route::post('pesan/{contactMessage}/jadikan-pembeli', [ContactMessageController::class, 'convert'])->name('contact-messages.convert');
        Route::post('pesan/{contactMessage}/spam', [ContactMessageController::class, 'spam'])->name('contact-messages.spam');
    });

    // Pengaturan
    Route::resource('pengguna', UserController::class)->except('show')->names('users')->parameters(['pengguna' => 'user']);
});
