<?php

namespace App\Livewire\Admin;

use App\Models\Language;
use Livewire\Attributes\On;
use Livewire\Component;

class LanguagesGrid extends Component
{
    public $search = '';

    #[On('languageUpdated')]
    public function refreshLanguages()
    {
        // This method will trigger a re-render
    }

    public function render()
    {
        $languages = Language::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%');
            })
            ->get();

        return view('livewire.admin.languages-grid', [
            'languages' => $languages,
        ]);
    }
}
