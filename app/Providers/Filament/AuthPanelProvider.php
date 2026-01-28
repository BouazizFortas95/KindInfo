<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AuthPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('auth')
            ->path('auth')
            ->login()
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->brandLogo(fn() => view('filament.logo'))
            ->brandLogoHeight('2.5rem')
            ->discoverResources(in: app_path('Filament/Auth/Resources'), for: 'App\Filament\Auth\Resources')
            ->discoverPages(in: app_path('Filament/Auth/Pages'), for: 'App\Filament\Auth\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Auth/Widgets'), for: 'App\Filament\Auth\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\SetLocaleFromSession::class,
            ])->renderHook(
                'panels::global-search.after',
                fn() => \Illuminate\Support\Facades\Blade::render('<livewire:language-switcher />'),
            )
            ->authMiddleware([
                Authenticate::class,
            ])
            ->darkMode(true);
    }
}
