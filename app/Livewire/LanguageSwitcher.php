<?php

namespace App\Livewire;

use Livewire\Component;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class LanguageSwitcher extends Component
{
    public $selectedLocale;

    public function mount()
    {
        $this->selectedLocale = app()->getLocale();
    }

    public function switchLanguage($locale)
    {
        if (!array_key_exists($locale, LaravelLocalization::getSupportedLocales())) {
            return;
        }

        $supportedLocales = LaravelLocalization::getSupportedLocales();
        $script = $supportedLocales[$locale]['script'] ?? 'Latn';
        $direction = in_array($script, ['Arab', 'Hebr', 'Syrc', 'Thaa']) ? 'rtl' : 'ltr';

        // Store locale and direction in session
        session([
            'locale' => $locale,
            'dir' => $direction
        ]);

        // Set application locale
        app()->setLocale($locale);

        // Check if we're in admin panel or other non-localized routes
        $currentPath = request()->path();
        if (
            str_starts_with($currentPath, 'admin') ||
            str_starts_with($currentPath, 'filament') ||
            str_starts_with($currentPath, 'livewire')
        ) {
            // For admin panel, just reload the current page
            return redirect(request()->header('Referer'));
        }

        // For localized routes, get the localized URL
        $url = LaravelLocalization::getLocalizedURL($locale);

        if ($url) {
            return redirect($url);
        }

        // Fallback to home
        return redirect('/');
    }

    public function render()
    {
        return view('livewire.language-switcher', [
            'locales' => LaravelLocalization::getSupportedLocales()
        ]);
    }
}
