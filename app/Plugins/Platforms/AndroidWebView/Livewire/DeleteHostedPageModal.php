<?php

namespace App\Plugins\Platforms\AndroidWebView\Livewire;

use App\Plugins\Shared\Models\AppPage;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteHostedPageModal extends Component
{
    public $showModal = false;

    public $pageId = null;

    public $pageTitle = '';

    #[On('delete-hosted-page')]
    public function confirmDelete($id, $title)
    {
        $this->pageId = $id;
        $this->pageTitle = $title;
        $this->showModal = true;
    }

    public function delete()
    {
        if (! $this->pageId) {
            return;
        }

        try {
            $page = AppPage::find($this->pageId);

            if ($page) {
                $page->delete();

                $this->dispatch('show-toast',
                    type: 'success',
                    message: 'Page deleted successfully!'
                );

                $this->dispatch('hostedPageDeleted');
            }

        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to delete page. Please try again.'
            );
        }

        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->pageId = null;
        $this->pageTitle = '';
    }

    public function render()
    {
        return view('plugins.android-webview::livewire.delete-hosted-page-modal');
    }
}
