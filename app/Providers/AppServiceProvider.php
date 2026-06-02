<?php

namespace App\Providers;

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
    // Share SiteSetting helper ke semua view sebagai $settings
    \Illuminate\Support\Facades\View::composer('*', function ($view) {
        $view->with('setting', function (string $key, $default = null) {
            return \App\Models\SiteSetting::get($key, $default);
        });
    });
}
}
