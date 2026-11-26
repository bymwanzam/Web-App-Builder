<?php

namespace App\Livewire\Admin;

use App\Models\Language;
use Livewire\Attributes\On;
use Livewire\Component;

class LanguageForm extends Component
{
    public $languageId;

    public $name;

    public $code;

    public $flag;

    public $status = 'active';

    public $showModal = false;

    #[On('create-language')]
    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    #[On('edit-language')]
    public function edit($id)
    {
        $this->resetForm();
        $language = Language::findOrFail($id);

        $this->languageId = $language->id;
        $this->name = $language->name;
        $this->code = $language->code;
        $this->flag = $language->flag;
        $this->status = $language->status;
        $this->showModal = true;
    }

    #[On('closeLanguageForm')]
    public function closeFromDelete()
    {
        $this->closeModal();
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

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:languages,code,'.$this->languageId,
            'flag' => 'required|string|max:10',
            'status' => 'required|in:active,inactive',
        ]);

        if ($this->languageId) {
            // Update existing language
            $language = Language::findOrFail($this->languageId);
            $language->update([
                'name' => $this->name,
                'code' => $this->code,
                'flag' => $this->flag,
                'status' => $this->status,
            ]);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Language updated successfully!'
            );
        } else {
            // Create new language
            $language = Language::create([
                'name' => $this->name,
                'code' => $this->code,
                'flag' => $this->flag,
                'status' => $this->status,
            ]);

            // Create translation files for the new language
            $language->createTranslationFiles();

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Language created successfully!'
            );
        }

        $this->closeModal();
        $this->dispatch('languageUpdated');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->languageId = null;
        $this->name = '';
        $this->code = '';
        $this->flag = '';
        $this->status = 'active';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.language-form');
    }
}
