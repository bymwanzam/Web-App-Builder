<?php

namespace App\Livewire\Admin;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AppetizeSettingsForm extends Component
{
    public bool $appetize_enabled = false;

    public ?string $appetize_api_token = null;

    public bool $appetize_auto_upload = true;

    #[Validate('required|integer|min:1|max:30')]
    public int $appetize_retention_days = 7;

    #[Validate('required|integer|min:30|max:7200')]
    public int $appetize_session_timeout = 300;

    public function mount()
    {
        $this->appetize_enabled = (bool) SystemSetting::get('appetize_enabled', false);
        $this->appetize_api_token = SystemSetting::get('appetize_api_token');
        $this->appetize_auto_upload = (bool) SystemSetting::get('appetize_auto_upload', true);
        $this->appetize_retention_days = (int) SystemSetting::get('appetize_retention_days', 7);
        $this->appetize_session_timeout = (int) SystemSetting::get('appetize_session_timeout', 300);

        // Clear sensitive fields in demo mode
        if (config('app.demo')) {
            $this->appetize_api_token = '';
        }
    }

    public function save()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Settings cannot be modified in demo mode.'
            );

            return;
        }

        $this->validate([
            'appetize_api_token' => 'nullable|string|min:20',
            'appetize_retention_days' => 'required|integer|min:1|max:30',
            'appetize_session_timeout' => 'required|integer|min:30|max:7200',
        ]);

        SystemSetting::set('appetize_enabled', $this->appetize_enabled);
        SystemSetting::set('appetize_api_token', $this->appetize_api_token);
        SystemSetting::set('appetize_auto_upload', $this->appetize_auto_upload);
        SystemSetting::set('appetize_retention_days', $this->appetize_retention_days);
        SystemSetting::set('appetize_session_timeout', $this->appetize_session_timeout);

        $this->dispatch('show-toast',
            type: 'success',
            message: 'Appetize settings saved successfully'
        );
    }

    public function testConnection()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Connection tests are disabled in demo mode.'
            );

            return;
        }

        if (empty($this->appetize_api_token)) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Please enter an API token first'
            );

            return;
        }

        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $this->appetize_api_token,
            ])->get('https://api.appetize.io/v1/apps');

            if ($response->successful()) {
                $this->dispatch('show-toast',
                    type: 'success',
                    message: 'Connection successful!'
                );
            } else {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: 'Invalid API token'
                );
            }
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Connection failed: '.$e->getMessage()
            );
        }
    }

    public function render()
    {
        return view('livewire.admin.appetize-settings-form');
    }
}
