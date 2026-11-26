<?php

namespace App\Plugins\Platforms\AndroidWebView\Livewire;

use App\Models\App;
use App\Plugins\Shared\Models\AppPage;
use Livewire\Attributes\On;
use Livewire\Component;

class HostedPagesSettings extends Component
{
    public App $app;

    public $pages = [];

    public $canEdit = false;

    public $maxPages = null;

    public $canAddMore = true;

    public function mount()
    {
        $this->loadPages();
        $this->checkPermissions();
    }

    protected function loadPages()
    {
        $this->pages = $this->app->hostedPages()
            ->ordered()
            ->get()
            ->toArray();
    }

    protected function checkPermissions()
    {
        $user = auth()->user();
        $plan = $user->plan;

        $this->canEdit = $user->canUseHostedPages();
        $this->maxPages = $plan?->getMaxHostedPagesPerApp();

        // Check if can add more pages
        if ($this->canEdit && $this->maxPages !== null) {
            $this->canAddMore = count($this->pages) < $this->maxPages;
        } else {
            $this->canAddMore = $this->canEdit;
        }
    }

    public function createPage()
    {
        if (! $this->canEdit) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Your plan does not include hosted pages. Please upgrade your plan.'
            );

            return;
        }

        if (! $this->canAddMore) {
            $this->dispatch('show-toast',
                type: 'error',
                message: "You've reached the maximum of {$this->maxPages} pages for your plan."
            );

            return;
        }

        $this->dispatch('create-hosted-page');
    }

    public function editPage($pageId)
    {
        $this->dispatch('edit-hosted-page', id: $pageId);
    }

    public function deletePage($pageId)
    {
        $page = AppPage::find($pageId);

        if (! $page) {
            return;
        }

        $this->dispatch('delete-hosted-page', id: $pageId, title: $page->title);
    }

    #[On('hostedPageSaved')]
    public function refreshPages()
    {
        $this->loadPages();
        $this->checkPermissions();
    }

    #[On('hostedPageDeleted')]
    public function onPageDeleted()
    {
        $this->loadPages();
        $this->checkPermissions();
    }

    public function render()
    {
        return view('plugins.android-webview::livewire.hosted-pages-settings');
    }
}
