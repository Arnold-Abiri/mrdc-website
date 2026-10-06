<?php

namespace App\Http\Controllers\Public;

use Illuminate\Http\Request;

class LocaleController extends \App\Http\Controllers\Controller
{
    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate(['locale' => ['required', 'in:en,sn,nd']]);
            $request->session()->put('public_locale', $data['locale']);
            $referer = (string) $request->headers->get('referer', '');
            $path = $referer !== '' ? (string) parse_url($referer, PHP_URL_PATH) : '/';
            if (str_starts_with($path, '/admin')) {
                return back();
            }
            $segments = explode('/', trim($path, '/'));
            $first = $segments[0];
            if (in_array($first, ['en', 'sn', 'nd'], true)) {
                $segments[0] = $data['locale'];
            } else {
                array_unshift($segments, $data['locale']);
            }
            $target = '/'.implode('/', array_filter($segments, fn ($segment): bool => $segment !== ''));

            return redirect($target === '/' ? '/'.$data['locale'] : $target);
    }

}