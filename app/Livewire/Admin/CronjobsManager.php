<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\On;
use Livewire\Component;

class CronjobsManager extends Component
{
    public $activeTab = 'jobs';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    #[On('jobExecuted')]
    public function switchToLogsTab()
    {
        $this->activeTab = 'logs';
    }

    public function render()
    {
        return view('livewire.admin.cronjobs-manager');
    }
}
