<?php

namespace App\Livewire;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public $currentLocale;
    public $availableLocales;

    public function mount()
    {
        $this->currentLocale = App::getLocale();
        $this->availableLocales = config('app.available_locales');
    }

    public function switchLanguage($locale)
    {
        if (in_array($locale, $this->availableLocales)) {
            Session::put('locale', $locale);
            App::setLocale($locale);
            $this->currentLocale = $locale;

            // Dispatch browser event to reload page
            $this->dispatch('locale-changed');
        }
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}
