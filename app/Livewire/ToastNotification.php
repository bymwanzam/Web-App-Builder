<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class ToastNotification extends Component
{
    public $show = false;

    public $type = 'success'; // success or error

    public $message = '';

    public function mount()
    {
        // Check for session flash toast (used when navigating after actions)
        if ($toast = session('toast')) {
            $this->show = true;
            $this->type = $toast['type'] ?? 'success';
            $this->message = $toast['message'] ?? '';
        }
    }

    #[On('show-toast')]
    public function showToast($type = 'success', $message = '')
    {
        $this->show = true;
        $this->type = $type;
        $this->message = $message;

        // Dispatch browser event for animation
        $this->dispatch('toast-shown');
    }

    public function close()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.toast-notification');
    }
}
