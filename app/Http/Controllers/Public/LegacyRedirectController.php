<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LegacyRedirectController extends Controller
{
    public function show(Request $request, string $legacy): RedirectResponse
    {
        $locale = session('public_locale', 'en');
        $query = $request->getQueryString();

        return redirect('/'.(in_array($locale, ['en', 'sn', 'nd'], true) ? $locale : 'en').'/'.$legacy.($query ? '?'.$query : ''));
    }
}
