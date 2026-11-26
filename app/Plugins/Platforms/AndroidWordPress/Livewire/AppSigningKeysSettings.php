<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

use App\Models\App;
use App\Models\Keystore;
use Livewire\Attributes\On;
use Livewire\Component;

class AppSigningKeysSettings extends Component
{
    public App $app;

    #[On('keystore-deleted')]
    public function handleDeletion()
    {
        // Check if deleted keystore was set as default for this app
        // and clear it if necessary
        $this->app->refresh();

        $config = $this->app->androidWordPressConfig;
        if ($config && $config->keystore_id && ! Keystore::find($config->keystore_id)) {
            $config->keystore_id = null;
            $config->save();
        }
    }

    #[On('keystoreUpdated')]
    public function handleUpdate()
    {
        // Refresh component when keystore is updated
        $this->app->refresh();
    }

    public function setAsDefault($type, $id)
    {
        try {
            if ($type === 'android') {
                $config = $this->app->androidWordPressConfig;
                if ($config) {
                    $config->keystore_id = $id;
                    $config->save();
                }
            }

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Default signing key updated successfully!'
            );

        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to update default signing key.'
            );
        }
    }

    #[On('keystoreGenerated')]
    public function handleGenerated($keystoreId = null)
    {
        // Same logic as keystoreUploaded - auto-set as default if no default exists
        $this->refresh($keystoreId);
    }

    #[On('keystoreUploaded')]
    public function refresh($keystoreId = null)
    {
        // Auto-set as default if no default keystore exists
        $config = $this->app->androidWordPressConfig;
        if ($keystoreId && $config && ! $config->keystore_id) {
            try {
                $config->keystore_id = $keystoreId;
                $config->save();

                $this->dispatch('show-toast',
                    type: 'success',
                    message: 'Keystore uploaded and set as default!'
                );
            } catch (\Exception $e) {
                $this->dispatch('show-toast',
                    type: 'success',
                    message: 'Keystore uploaded successfully!'
                );
            }
        } else {
            // Refresh component without auto-setting
            $this->dispatch('show-toast',
                type: 'success',
                message: 'Keystore uploaded successfully!'
            );
        }
    }

    public function render()
    {
        $keystores = Keystore::where('user_id', auth()->id())->latest()->get();

        return view('plugins.android-wordpress::livewire.app-signing-keys-settings', [
            'keystores' => $keystores,
        ]);
    }
}
