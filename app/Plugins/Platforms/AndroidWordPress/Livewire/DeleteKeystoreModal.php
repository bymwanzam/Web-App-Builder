<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

use App\Models\Keystore;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteKeystoreModal extends Component
{
    public $showModal = false;

    public $keystoreId = null;

    public $keystoreName = '';

    #[On('delete-keystore')]
    public function openModal($id, $name)
    {
        $this->keystoreId = $id;
        $this->keystoreName = $name;
        $this->showModal = true;
    }

    public function confirmDelete()
    {
        try {
            $keystore = Keystore::where('user_id', auth()->id())->findOrFail($this->keystoreId);
            $keystore->delete();

            $this->showModal = false;
            $this->reset(['keystoreId', 'keystoreName']);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Keystore deleted successfully!'
            );

            $this->dispatch('keystore-deleted');

        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to delete keystore.'
            );
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['keystoreId', 'keystoreName']);
    }

    public function render()
    {
        return view('plugins.android-wordpress::livewire.delete-keystore-modal');
    }
}
