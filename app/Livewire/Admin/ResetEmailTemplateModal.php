<?php

namespace App\Livewire\Admin;

use App\Models\EmailTemplate;
use Livewire\Attributes\On;
use Livewire\Component;

class ResetEmailTemplateModal extends Component
{
    public $templateId;

    public $templateName;

    public $showModal = false;

    #[On('reset-template')]
    public function confirmReset($id, $name)
    {
        $this->templateId = $id;
        $this->templateName = $name;
        $this->showModal = true;
    }

    public function resetTemplate()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'This cannot be modified in demo mode.'
            );
            $this->closeModal();

            return;
        }

        if ($this->templateId) {
            $template = EmailTemplate::find($this->templateId);
            if ($template && $template->resetToDefault()) {
                $this->dispatch('show-toast',
                    type: 'success',
                    message: 'Template reset to default successfully!'
                );
                $this->dispatch('templateUpdated');
            } else {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: 'Failed to reset template.'
                );
            }
            $this->closeModal();
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->templateId = null;
        $this->templateName = null;
    }

    public function render()
    {
        return view('livewire.admin.reset-email-template-modal');
    }
}
