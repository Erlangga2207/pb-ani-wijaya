<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Header noindex untuk seluruh /panel. Meta noindex saja tidak cukup: halaman yang diblok
// robots.txt tidak dibaca crawler, dan respons non-HTML (redirect, unduhan) tidak punya <meta>.
class NoIndex
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }
}
