<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Home'))->name('home');

Route::get('/sitemap.xml', function () {
    $url = htmlspecialchars(route('home'), ENT_XML1, 'UTF-8');

    return response('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>'.$url.'</loc></url></urlset>', 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
})->name('sitemap');
