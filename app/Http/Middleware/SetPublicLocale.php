<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetPublicLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $param = $request->route('locale');
        if (is_string($param) && in_array($param, ['en', 'sn', 'nd'], true)) {
            $request->session()->put('public_locale', $param);
            app()->setLocale($param);

            return $next($request);
        }
        $locale = $request->session()->get('public_locale', config('app.locale'));
        app()->setLocale(in_array($locale, ['en', 'sn', 'nd'], true) ? $locale : 'en');

        return $next($request);
    }
}
