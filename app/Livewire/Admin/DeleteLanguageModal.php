<?php

namespace App\Livewire\Admin;

use App\Models\Language;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteLanguageModal extends Component
{
    public $languageId;

    public $languageName;

    public $showModal = false;

    #[On('delete-language')]
    public function confirmDelete($id, $name)
    {
        $this->languageId = $id;
        $this->languageName = $name;
        $this->showModal = true;
    }

    public function delete()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Deletion is disabled in demo mode.'
            );
            $this->closeModal();

            return;
        }

        if ($this->languageId) {
            Language::findOrFail($this->languageId)->delete();
            $this->dispatch('show-toast',
                type: 'success',
                message: 'Language deleted successfully!'
            );
            $this->closeModal();
            $this->dispatch('languageUpdated');
            $this->dispatch('closeLanguageForm');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->languageId = null;
        $this->languageName = null;
    }

    public function render()
    {
        return view('livewire.admin.delete-language-modal');
    }
}
