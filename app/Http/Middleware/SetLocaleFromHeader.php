<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = ['en', 'ar'];
        $lang = $request->header('lang', 'en');

        if (!in_array($lang, $supportedLocales)) {
            $lang = 'en';
        }

        app()->setLocale($lang);

        return $next($request);
    }
}
