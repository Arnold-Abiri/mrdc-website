<?php

namespace App\Http\Controllers\Public;

use Illuminate\Http\Request;

class LegacyRedirectController extends \App\Http\Controllers\Controller
{
    public function show(Request $request, string $legacy): \Illuminate\Http\RedirectResponse
    {
        $locale = session('public_locale', 'en');
            $query = $request->getQueryString();

            return redirect('/'.(in_array($locale, ['en', 'sn', 'nd'], true) ? $locale : 'en').'/'.$legacy.($query ? '?'.$query : ''));
    }

}