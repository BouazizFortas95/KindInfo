<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('locale')) {
            $locale = session()->get('locale');

            // Check if the locale is supported
            if (array_key_exists($locale, LaravelLocalization::getSupportedLocales())) {
                App::setLocale($locale);
                LaravelLocalization::setLocale($locale);

                // Handle RTL/LTR for Filament
                $direction = session()->get('dir', 'ltr');
                // You might need to set a config or view share depending on how you use direction, 
                // but usually App::setLocale is enough for Filament if configured correctly, 
                // or we use the 'dir' session in the layout.
            }
        }

        return $next($request);
    }
}
