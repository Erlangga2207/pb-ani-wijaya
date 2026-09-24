<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Models\User;
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

        // Share SiteSetting helper ke semua view sebagai $settings
        View::composer('*', function ($view) {
            $view->with('setting', function (string $key, $default = null) {
                return SiteSetting::get($key, $default);
            });
        });
    }
}
