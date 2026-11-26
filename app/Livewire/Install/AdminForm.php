<?php

namespace App\Livewire\Install;

use App\Services\InstallerService;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class AdminForm extends Component
{
    public $name = '';

    public $email = '';

    public $password = '';

    public $password_confirmation = '';

    public $site_name = 'Appy';

    public $purchase_code = '';

    public function save(InstallerService $installer)
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'site_name' => ['required', 'string', 'max:255'],
            'purchase_code' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $installer->createAdmin($validated);

            // Use regular redirect to force full page reload with new config
            return redirect()->route('install.completed');
        } catch (\Exception $e) {
            \Log::error('Admin creation failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            $this->addError('error', 'Installation failed: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.install.admin-form');
    }
}
