<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // User nonaktif / tanpa role tidak lolos policy apa pun (termasuk route cetak di luar panel).
        Gate::before(function (User $user) {
            return $user->is_active && $user->hasAnyRole(User::ROLES) ? null : false;
        });

        // Tab laporan keuangan hanya owner ke atas; staff hanya Stok & Rendemen.
        Gate::define('view-report', fn (User $user, string $tab) => array_key_exists($tab, ReportService::TABS)
            && ($user->isOwnerOrAbove() || in_array($tab, ReportService::STAFF_TABS, true)));

        // Share SiteSetting helper ke semua view sebagai $settings
        View::composer('*', function ($view) {
            $view->with('setting', function (string $key, $default = null) {
                return SiteSetting::get($key, $default);
            });
        });
    }
}
