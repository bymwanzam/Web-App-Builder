<?php

namespace App\Livewire\User;

use App\Models\AppNotification;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteNotificationModal extends Component
{
    public $showModal = false;

    public $notificationId = null;

    public $notificationTitle = '';

    #[On('delete-notification')]
    public function confirmDelete($id)
    {
        $notification = AppNotification::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($notification) {
            $this->notificationId = $notification->id;
            $this->notificationTitle = $notification->title;
            $this->showModal = true;
        }
    }

    public function delete()
    {
        $notification = AppNotification::where('id', $this->notificationId)
            ->where('user_id', auth()->id())
            ->first();

        if ($notification) {
            // Only allow deleting scheduled or pending notifications
            if (in_array($notification->status, ['scheduled', 'pending'])) {
                $notification->delete();
                session()->flash('message', 'Notification cancelled successfully.');
            } else {
                session()->flash('error', 'Cannot delete notifications that have already been sent.');
            }
        }

        $this->showModal = false;
        $this->dispatch('notificationDeleted');
    }

    public function render()
    {
        return view('livewire.user.delete-notification-modal');
    }
}
