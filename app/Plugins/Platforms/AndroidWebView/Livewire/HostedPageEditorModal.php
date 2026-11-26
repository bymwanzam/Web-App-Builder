<?php

namespace App\Plugins\Platforms\AndroidWebView\Livewire;

use App\Models\App;
use App\Plugins\Platforms\AndroidWebView\PageTemplates;
use App\Plugins\Shared\Models\AppPage;
use Livewire\Attributes\On;
use Livewire\Component;

class HostedPageEditorModal extends Component
{
    public App $app;

    public $showModal = false;

    public $pageId = null;

    public $isEditing = false;

    public $title = '';

    public $slug = '';

    public $html_content = '';

    public $css_content = '';

    public $js_content = '';

    public $status = 'draft';

    public $activeTab = 'html';

    public $selectedTemplate = '';

    protected function rules()
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'html_content' => ['required', 'string', 'max:512000'], // 500KB
            'css_content' => ['nullable', 'string', 'max:102400'], // 100KB
            'js_content' => ['nullable', 'string', 'max:102400'], // 100KB
            'status' => ['required', 'in:draft,published'],
        ];
    }

    protected function messages()
    {
        return [
            'slug.regex' => 'Slug can only contain lowercase letters, numbers, and hyphens.',
        ];
    }

    #[On('create-hosted-page')]
    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;

        // Dispatch event for JavaScript to initialize editors
        $this->dispatch('hosted-page-modal-opened', mode: 'create');
    }

    #[On('edit-hosted-page')]
    public function edit($id)
    {
        $page = AppPage::find($id);

        if (! $page || $page->app_id !== $this->app->id) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Page not found.'
            );

            return;
        }

        $this->pageId = $page->id;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->html_content = $page->html_content;
        $this->css_content = $page->css_content ?? '';
        $this->js_content = $page->js_content ?? '';
        $this->status = $page->status;
        $this->activeTab = 'html';
        $this->isEditing = true;
        $this->showModal = true;

        // Dispatch event for JavaScript to initialize editors
        $this->dispatch('hosted-page-modal-opened', mode: 'edit');
    }

    public function updatedTitle($value)
    {
        // Auto-generate slug from title when creating (not editing)
        if (! $this->isEditing && $value) {
            $this->slug = AppPage::generateSlug($value, $this->app->id, $this->pageId);
        }
    }

    public function applyTemplate($templateKey)
    {
        $template = PageTemplates::getTemplate($templateKey);

        if (! $template) {
            return;
        }

        $this->html_content = $template['html'];
        $this->css_content = $template['css'];
        $this->js_content = $template['js'];

        $this->dispatch('template-applied', template: $templateKey);
    }

    public function save()
    {
        // Check permission
        $user = auth()->user();
        if (! $user->canUseHostedPages()) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Your plan does not include hosted pages. Please upgrade your plan.'
            );

            return;
        }

        $validated = $this->validate();

        // Check for duplicate slug
        if (AppPage::slugExists($validated['slug'], $this->app->id, $this->pageId)) {
            $this->addError('slug', 'This slug is already in use. Please choose a different one.');

            return;
        }

        try {
            if ($this->isEditing) {
                $page = AppPage::find($this->pageId);
                $page->update($validated);
                $message = 'Page updated successfully!';
            } else {
                // Check page limit
                $plan = $user->plan;
                $maxPages = $plan?->getMaxHostedPagesPerApp();
                $currentCount = $this->app->hostedPages()->count();

                if ($maxPages !== null && $currentCount >= $maxPages) {
                    $this->dispatch('show-toast',
                        type: 'error',
                        message: "You've reached the maximum of {$maxPages} pages for your plan."
                    );

                    return;
                }

                $validated['app_id'] = $this->app->id;
                $validated['sort_order'] = $currentCount;
                AppPage::create($validated);
                $message = 'Page created successfully!';
            }

            $this->dispatch('show-toast',
                type: 'success',
                message: $message
            );

            $this->dispatch('hostedPageSaved');
            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to save page. Please try again.'
            );
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    protected function resetForm()
    {
        $this->pageId = null;
        $this->title = '';
        $this->slug = '';
        $this->html_content = '';
        $this->css_content = '';
        $this->js_content = '';
        $this->status = 'draft';
        $this->activeTab = 'html';
        $this->selectedTemplate = '';
        $this->resetValidation();
    }

    public function getTemplatesProperty()
    {
        return PageTemplates::getTemplateOptions();
    }

    public function render()
    {
        return view('plugins.android-webview::livewire.hosted-page-editor-modal');
    }
}
