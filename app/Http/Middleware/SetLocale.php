<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

// Bahasa halaman publik diambil dari prefix URL (/en/...). Tanpa prefix = bahasa default.
// Sengaja tidak membaca bahasa browser: crawler Google bisa salah mengindeks.
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale') ?? array_key_first(config('locales'));

        abort_unless(array_key_exists($locale, config('locales')), 404);

        app()->setLocale($locale);
        Carbon::setLocale($locale);

        // Parameter {locale} tidak diteruskan ke method controller.
        $request->route()->forgetParameter('locale');

        return $next($request);
    }
}
