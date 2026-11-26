<?php

namespace App\Livewire\Admin;

use App\Models\EmailTemplate;
use Livewire\Attributes\On;
use Livewire\Component;

class EmailTemplateForm extends Component
{
    public $showModal = false;

    public $templateId = null;

    public $name = '';

    public $slug = '';

    public $subject = '';

    public $body = '';

    public $variables = [];

    public $category = '';

    #[On('edit-template')]
    public function edit($id)
    {
        $template = EmailTemplate::find($id);

        if ($template) {
            $this->templateId = $template->id;
            $this->name = $template->name;
            $this->slug = $template->slug;
            $this->subject = $template->subject;
            $this->body = $template->body;
            $this->variables = $template->variables ?? [];
            $this->category = $template->category;
            $this->showModal = true;
        }
    }

    public function save()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'This cannot be modified in demo mode.'
            );

            return;
        }

        $this->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $template = EmailTemplate::find($this->templateId);

        if ($template) {
            $template->update([
                'subject' => $this->subject,
                'body' => $this->body,
            ]);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Email template updated successfully!'
            );

            $this->dispatch('templateUpdated');
            $this->closeModal();
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['templateId', 'name', 'slug', 'subject', 'body', 'variables', 'category']);
    }

    public function render()
    {
        return view('livewire.admin.email-template-form');
    }
}
