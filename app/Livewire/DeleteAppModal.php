<?php

namespace App\Livewire;

use App\Models\App;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteAppModal extends Component
{
    public $showModal = false;

    public $appId = null;

    public $appName = '';

    #[On('delete-app')]
    public function confirmDelete($id)
    {
        $app = App::where('user_id', auth()->id())->findOrFail($id);

        $this->appId = $app->id;
        $this->appName = $app->name;
        $this->showModal = true;
    }

    public function delete()
    {
        $app = App::where('user_id', auth()->id())->findOrFail($this->appId);
        $app->delete();

        $this->showModal = false;
        $this->dispatch('appDeleted');
        $this->reset(['appId', 'appName']);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['appId', 'appName']);
    }

    public function render()
    {
        return view('livewire.delete-app-modal');
    }
}
