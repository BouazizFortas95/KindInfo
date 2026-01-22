<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// Localized routes group
Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localizationRoutes', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {

    // Home page
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    // Add your other localized routes here

});

// Non-localized routes (if any)
// Route::get('/non-localized', function() {
//     return 'This route is not localized';
// });