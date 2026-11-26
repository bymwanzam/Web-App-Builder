<?php

namespace App\Livewire;

use Livewire\Component;

class CookieConsentBanner extends Component
{
    public bool $show = true;

    public bool $showPreferences = false;

    public bool $analytics = false;

    public bool $marketing = false;

    public function mount(): void
    {
        // Check if consent cookie exists
        $consentCookie = request()->cookie('cookie_consent');

        if ($consentCookie) {
            $this->show = false;
            $preferences = json_decode($consentCookie, true);
            $this->analytics = $preferences['analytics'] ?? false;
            $this->marketing = $preferences['marketing'] ?? false;
        }
    }

    public function acceptAll(): void
    {
        $this->saveConsent([
            'essential' => true,
            'analytics' => true,
            'marketing' => true,
        ]);
    }

    public function acceptEssential(): void
    {
        $this->saveConsent([
            'essential' => true,
            'analytics' => false,
            'marketing' => false,
        ]);
    }

    public function savePreferences(): void
    {
        $this->saveConsent([
            'essential' => true,
            'analytics' => $this->analytics,
            'marketing' => $this->marketing,
        ]);
    }

    protected function saveConsent(array $preferences): void
    {
        $this->show = false;
        $this->dispatch('cookie-consent-saved', preferences: $preferences);
    }

    public function render()
    {
        return view('livewire.cookie-consent-banner');
    }
}
