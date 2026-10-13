<?php

use App\Exceptions\InsufficientStockException;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->prefix('panel')->name('admin.')->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'active' => EnsureUserIsActive::class,
        ]);

        // Halaman internal (panel, nota, bukti, laporan) diarahkan ke login panel.
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Stok kurang saat simpan transaksi di panel: kembali ke form dengan pesan, bukan error 500.
        $exceptions->render(function (InsufficientStockException $e, Request $request) {
            if (! $request->expectsJson()) {
                return back()->withInput()->with('gagal', $e->getMessage());
            }
        });
    })->create();

// Server Hostinger: repo ada di public_html, jadi .env disimpan SATU tingkat di atasnya
// (domains/aniwijaya.com/.env.pb-ani-wijaya) supaya tidak pernah ada di folder web.
// Di laptop file itu tidak ada → tetap memakai .env biasa. key:generate & config:cache ikut memakai file ini.
$serverEnvDir = dirname(__DIR__, 2);

if (is_file($serverEnvDir.'/.env.pb-ani-wijaya')) {
    $app->useEnvironmentPath($serverEnvDir);
    $app->loadEnvironmentFrom('.env.pb-ani-wijaya');
}

return $app;
