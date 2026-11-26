<?php

namespace App\Livewire\User;

use App\Models\UserConsent;
use Livewire\Component;

class ConsentSettings extends Component
{
    public bool $marketing = false;

    public bool $thirdParty = false;

    public bool $aiServices = false;

    public function mount(): void
    {
        $user = auth()->user();

        // Load existing consents
        $consents = UserConsent::where('user_id', $user->id)
            ->whereIn('consent_type', ['marketing', 'third_party', 'ai_services'])
            ->get()
            ->keyBy('consent_type');

        $this->marketing = $consents->get('marketing')?->consented ?? false;
        $this->thirdParty = $consents->get('third_party')?->consented ?? false;
        $this->aiServices = $consents->get('ai_services')?->consented ?? false;
    }

    public function saveConsents(): void
    {
        $user = auth()->user();

        UserConsent::recordConsent($user, 'marketing', $this->marketing);
        UserConsent::recordConsent($user, 'third_party', $this->thirdParty);
        UserConsent::recordConsent($user, 'ai_services', $this->aiServices);

        $this->dispatch('toast', type: 'success', message: 'Consent preferences saved.');
    }

    public function render()
    {
        $user = auth()->user();

        $consents = UserConsent::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('livewire.user.consent-settings', [
            'consents' => $consents,
        ]);
    }
}
