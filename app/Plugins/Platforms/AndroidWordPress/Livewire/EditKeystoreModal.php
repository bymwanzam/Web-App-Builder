<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

use App\Models\Keystore;
use Livewire\Attributes\On;
use Livewire\Component;

class EditKeystoreModal extends Component
{
    public $showModal = false;

    public $keystoreId = null;

    public $keystore_name = '';

    public $keystore_alias = '';

    public $keystore_password = '';

    public $update_password = false;

    #[On('edit-keystore')]
    public function openModal($id)
    {
        try {
            $keystore = Keystore::where('user_id', auth()->id())->findOrFail($id);

            $this->keystoreId = $keystore->id;
            $this->keystore_name = $keystore->name;
            $this->keystore_alias = $keystore->alias ?? '';
            $this->update_password = false;
            $this->keystore_password = '';

            $this->showModal = true;
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to load keystore.'
            );
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['keystoreId', 'keystore_name', 'keystore_alias', 'keystore_password', 'update_password']);
    }

    protected function rules()
    {
        $rules = [
            'keystore_name' => ['required', 'string', 'max:255'],
            'keystore_alias' => ['required', 'string', 'max:255'],
        ];

        // Only validate password if user wants to update it
        if ($this->update_password) {
            $rules['keystore_password'] = ['required', 'string', 'min:6'];
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'keystore_name.required' => 'Please provide a name for the keystore.',
            'keystore_alias.required' => 'Keystore alias is required.',
            'keystore_password.required' => 'Keystore password is required.',
            'keystore_password.min' => 'Keystore password must be at least 6 characters.',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            $keystore = Keystore::where('user_id', auth()->id())->findOrFail($this->keystoreId);

            $keystore->name = $this->keystore_name;
            $keystore->alias = $this->keystore_alias;

            // Only update password if user checked the update_password checkbox
            if ($this->update_password) {
                $keystore->setPassword($this->keystore_password);
            }

            $keystore->save();

            $this->closeModal();

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Keystore updated successfully!'
            );

            $this->dispatch('keystoreUpdated');

        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to update keystore.'
            );
        }
    }

    public function render()
    {
        return view('plugins.android-wordpress::livewire.edit-keystore-modal');
    }
}
