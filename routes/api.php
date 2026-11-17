<?php

use App\Http\Controllers\Api\ApiDocumentationController;
use App\Http\Controllers\Api\EnquiryController;
use App\Http\Controllers\Api\PublicContentController;
use App\Http\Middleware\AddSecurityHeaders;
use App\Support\PublicApiCatalog;
use Illuminate\Support\Facades\Route;

Route::middleware(AddSecurityHeaders::class)->group(function (): void {
    Route::get('documentation', [ApiDocumentationController::class, 'index'])->name('api.documentation');
    Route::get('openapi.json', [ApiDocumentationController::class, 'specification'])->name('api.openapi');

    Route::prefix('v1')->middleware('throttle:60,1')->group(function (): void {
        foreach (['enquiries', 'feedback'] as $type) {
            Route::post($type, [EnquiryController::class, 'store'])->defaults('submission_type', $type)->middleware('throttle:5,1,api-submissions:')->name('api.v1.'.$type.'.store');
            Route::post($type.'/track', [EnquiryController::class, 'track'])->middleware('throttle:10,1,api-tracking:')->name('api.v1.'.$type.'.track');
        }
        $resources = implode('|', array_keys(PublicApiCatalog::all()));
        Route::get('{resource}', [PublicContentController::class, 'index'])->where('resource', $resources)->name('api.v1.index');
        Route::get('{resource}/{identifier}', [PublicContentController::class, 'show'])->where('resource', $resources)->name('api.v1.show');
    });
});
