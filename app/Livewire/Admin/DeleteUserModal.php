<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteUserModal extends Component
{
    public $userId;

    public $userName;

    public $showModal = false;

    #[On('delete-user')]
    public function confirmDelete($id, $name)
    {
        $this->userId = $id;
        $this->userName = $name;
        $this->showModal = true;
    }

    public function delete()
    {
        if ($this->userId) {
            // Protect admin account (user ID 1)
            if ((int) $this->userId === 1) {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: config('app.demo')
                        ? 'Demo admin account cannot be deleted.'
                        : 'Admin account cannot be deleted.'
                );
                $this->closeModal();

                return;
            }

            User::findOrFail($this->userId)->delete();
            $this->dispatch('show-toast',
                type: 'success',
                message: 'User deleted successfully!'
            );
            $this->closeModal();
            $this->dispatch('userUpdated');
            $this->dispatch('closeUserForm');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->userId = null;
        $this->userName = null;
    }

    public function render()
    {
        return view('livewire.admin.delete-user-modal');
    }
}
