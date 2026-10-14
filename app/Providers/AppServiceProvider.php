<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\Translatable\Facades\Translatable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Produksi: semua URL (canonical, hreflang, sitemap, nota, redirect) memakai APP_URL + https,
        // bukan host/skema request (mis. www. atau http di belakang proxy).
        if ($this->app->environment('production')) {
            URL::forceRootUrl(config('app.url'));
            URL::forceScheme('https');
        }

        // User nonaktif / tanpa role tidak lolos policy apa pun (termasuk route cetak di luar panel).
        Gate::before(function (User $user) {
            return $user->is_active && $user->hasAnyRole(User::ROLES) ? null : false;
        });

        // Tab laporan keuangan hanya owner ke atas; staff hanya Stok & Rendemen.
        Gate::define('view-report', fn (User $user, string $tab) => array_key_exists($tab, ReportService::TABS)
            && ($user->isOwnerOrAbove() || in_array($tab, ReportService::STAFF_TABS, true)));

        // Konten dinamis (produk, galeri): terjemahan kosong → bahasa default website, bukan app.fallback_locale.
        Translatable::fallback(fallbackLocale: array_key_first(config('locales')));

        // Keuangan › Utang ke Petani & Piutang Pembeli (PRD-08: grup Keuangan owner ke atas).
        Gate::define('view-debts', fn (User $user) => $user->isOwnerOrAbove());

        // Website › Galeri, Katalog, Pengaturan Website, Pesan (owner ke atas, sama dengan Policy masing-masing).
        Gate::define('manage-website', fn (User $user) => $user->isOwnerOrAbove());

        // Share SiteSetting helper ke semua view sebagai $settings
        View::composer('*', function ($view) {
            $view->with('setting', function (string $key, $default = null) {
                return SiteSetting::get($key, $default);
            });
        });

        $this->bootAdminPanel();
    }

    // Panel Blade /panel: komponen <x-admin::...>, menu per role, format angka.
    private function bootAdminPanel(): void
    {
        Blade::anonymousComponentPath(resource_path('views/admin/components'), 'admin');
        Paginator::useBootstrapFive();

        // Menu dari config/admin-menu.php, disaring per role & route yang sudah ada. Grup kosong dibuang.
        View::composer('admin.layouts.app', function ($view) {
            $isOwner = auth()->user()->isOwnerOrAbove();

            $menu = collect(config('admin-menu'))
                ->map(function (array $group) use ($isOwner) {
                    $group['item'] = collect($group['item'])
                        ->filter(fn (array $item) => Route::has($item['route']) && (($item['role'] ?? null) !== 'owner' || $isOwner))
                        ->values();

                    return $group;
                })
                ->filter(fn (array $group) => $group['item']->isNotEmpty());

            $view->with('adminMenu', $menu);
        });

        // @rupiah(158000) → Rp 158.000
        Blade::directive('rupiah', fn ($value) => "<?php echo 'Rp '.number_format((float) ($value), 0, ',', '.'); ?>");

        // @angka(1250.50) → 1.250,5 ; @angka(36.00) → 36
        Blade::directive('angka', fn ($value) => "<?php echo rtrim(rtrim(number_format((float) ($value), 2, ',', '.'), '0'), ','); ?>");
    }
}
