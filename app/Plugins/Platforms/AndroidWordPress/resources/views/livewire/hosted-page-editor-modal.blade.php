<div x-data="{
    show: @entangle('showModal'),
    htmlEditor: null,
    cssEditor: null,
    jsEditor: null,
    editorsInitialized: false,

    initEditors() {
        if (this.editorsInitialized) return;
        if (typeof ace === 'undefined') {
            setTimeout(() => this.initEditors(), 100);
            return;
        }

        const htmlEl = document.getElementById('html-editor-hosted');
        const cssEl = document.getElementById('css-editor-hosted');
        const jsEl = document.getElementById('js-editor-hosted');

        if (!htmlEl || !cssEl || !jsEl) {
            setTimeout(() => this.initEditors(), 100);
            return;
        }

        const isDarkMode = document.documentElement.classList.contains('dark');
        const theme = isDarkMode ? 'ace/theme/tomorrow_night' : 'ace/theme/github';

        // HTML Editor
        this.htmlEditor = ace.edit('html-editor-hosted');
        this.htmlEditor.setTheme(theme);
        this.htmlEditor.session.setMode('ace/mode/html');
        this.htmlEditor.setValue(this.$wire.get('html_content') || '', -1);
        this.htmlEditor.setOptions({
            fontSize: '14px',
            showPrintMargin: false,
            enableBasicAutocompletion: true,
            tabSize: 2,
            useSoftTabs: true,
            wrap: true
        });
        this.htmlEditor.session.on('change', () => {
            this.$wire.set('html_content', this.htmlEditor.getValue());
        });

        // CSS Editor
        this.cssEditor = ace.edit('css-editor-hosted');
        this.cssEditor.setTheme(theme);
        this.cssEditor.session.setMode('ace/mode/css');
        this.cssEditor.setValue(this.$wire.get('css_content') || '', -1);
        this.cssEditor.setOptions({
            fontSize: '14px',
            showPrintMargin: false,
            enableBasicAutocompletion: true,
            tabSize: 2,
            useSoftTabs: true,
            wrap: true
        });
        this.cssEditor.session.on('change', () => {
            this.$wire.set('css_content', this.cssEditor.getValue());
        });

        // JS Editor
        this.jsEditor = ace.edit('js-editor-hosted');
        this.jsEditor.setTheme(theme);
        this.jsEditor.session.setMode('ace/mode/javascript');
        this.jsEditor.setValue(this.$wire.get('js_content') || '', -1);
        this.jsEditor.setOptions({
            fontSize: '14px',
            showPrintMargin: false,
            enableBasicAutocompletion: true,
            tabSize: 2,
            useSoftTabs: true,
            wrap: true
        });
        this.jsEditor.session.on('change', () => {
            this.$wire.set('js_content', this.jsEditor.getValue());
        });

        this.editorsInitialized = true;
    },

    destroyEditors() {
        if (this.htmlEditor) { this.htmlEditor.destroy(); this.htmlEditor = null; }
        if (this.cssEditor) { this.cssEditor.destroy(); this.cssEditor = null; }
        if (this.jsEditor) { this.jsEditor.destroy(); this.jsEditor = null; }
        this.editorsInitialized = false;
    },

    updateEditorContent() {
        setTimeout(() => {
            if (this.htmlEditor) this.htmlEditor.setValue(this.$wire.get('html_content') || '', -1);
            if (this.cssEditor) this.cssEditor.setValue(this.$wire.get('css_content') || '', -1);
            if (this.jsEditor) this.jsEditor.setValue(this.$wire.get('js_content') || '', -1);
        }, 50);
    }
}"
x-on:template-applied.window="updateEditorContent()"
x-init="
    $watch('show', value => {
        if (value) {
            $nextTick(() => { destroyEditors(); initEditors(); });
        }
    });
">
    <div x-show="show" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">

            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-gray-500/75 dark:bg-gray-900/75"
                     @click="$wire.closeModal()"
                     aria-hidden="true"></div>

                <!-- Center modal -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div class="relative z-10 inline-block w-full max-w-4xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">

            <!-- Header -->
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 flex items-center justify-between border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="modal-title">
                    {{ $isEditing ? 'Edit Page' : 'Create Page' }}
                </h3>
                <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form wire:submit.prevent="save">
                <div class="px-4 py-4 sm:px-6 space-y-4">
                    <!-- Title & Slug Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="page_title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="page_title"
                                   wire:model.live="title"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('title') border-red-500 @enderror"
                                   placeholder="Page title">
                            @error('title')
                                <small class="text-red-600 dark:text-red-400">{{ $message }}</small>
                            @enderror
                        </div>
                        <div>
                            <label for="page_slug" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Slug <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="page_slug"
                                   wire:model="slug"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('slug') border-red-500 @enderror"
                                   placeholder="page-slug">
                            @error('slug')
                                <small class="text-red-600 dark:text-red-400">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Template Selector (only when creating) -->
                    @if(!$isEditing)
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Start from template
                            </label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($this->templates as $key => $template)
                                    <button type="button"
                                            wire:click="applyTemplate('{{ $key }}')"
                                            class="px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors
                                                {{ $selectedTemplate === $key
                                                    ? 'bg-blue-100 border-blue-500 text-blue-700 dark:bg-blue-900/30 dark:border-blue-500 dark:text-blue-400'
                                                    : 'bg-gray-50 border-gray-300 text-gray-700 hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                                        {{ $template['name'] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Code Editor Tabs -->
                    <div x-data="{ activeTab: @entangle('activeTab') }">
                        <!-- Tab Buttons -->
                        <div class="flex border-b border-gray-200 dark:border-gray-600 mb-4">
                            <button type="button"
                                    @click="activeTab = 'html'"
                                    :class="activeTab === 'html' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                                    class="py-2 px-4 text-sm font-medium border-b-2 -mb-px transition-colors">
                                HTML <span class="text-red-500">*</span>
                            </button>
                            <button type="button"
                                    @click="activeTab = 'css'"
                                    :class="activeTab === 'css' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                                    class="py-2 px-4 text-sm font-medium border-b-2 -mb-px transition-colors">
                                CSS
                            </button>
                            <button type="button"
                                    @click="activeTab = 'js'"
                                    :class="activeTab === 'js' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'"
                                    class="py-2 px-4 text-sm font-medium border-b-2 -mb-px transition-colors">
                                JavaScript
                            </button>
                        </div>

                        <!-- Editor Panels -->
                        <div class="relative">
                            <!-- HTML Editor -->
                            <div x-show="activeTab === 'html'" wire:ignore>
                                <div id="html-editor-hosted" style="height: 300px; width: 100%; border: 1px solid #d1d5db; border-radius: 0.5rem; overflow: hidden;" class="dark:border-gray-600"></div>
                                @error('html_content')
                                    <small class="text-red-600 dark:text-red-400">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- CSS Editor -->
                            <div x-show="activeTab === 'css'" x-cloak wire:ignore>
                                <div id="css-editor-hosted" style="height: 300px; width: 100%; border: 1px solid #d1d5db; border-radius: 0.5rem; overflow: hidden;" class="dark:border-gray-600"></div>
                                @error('css_content')
                                    <small class="text-red-600 dark:text-red-400">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- JS Editor -->
                            <div x-show="activeTab === 'js'" x-cloak wire:ignore>
                                <div id="js-editor-hosted" style="height: 300px; width: 100%; border: 1px solid #d1d5db; border-radius: 0.5rem; overflow: hidden;" class="dark:border-gray-600"></div>
                                @error('js_content')
                                    <small class="text-red-600 dark:text-red-400">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status Toggle -->
                    <div class="flex items-center justify-between pt-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white">Status</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Only published pages are accessible in the app</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                   x-on:change="$wire.set('status', $el.checked ? 'published' : 'draft')"
                                   x-bind:checked="$wire.status === 'published'"
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300"
                                  x-text="$wire.status === 'published' ? 'Published' : 'Draft'"></span>
                        </label>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 flex justify-end gap-3 border-t border-gray-200 dark:border-gray-600">
                    <button type="button"
                            wire:click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500 dark:hover:bg-gray-500 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-hidden focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 transition-colors">
                        {{ $isEditing ? 'Save Changes' : 'Create Page' }}
                    </button>
                </div>
            </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load Ace Editor Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/ace.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/ext-language_tools.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/mode-html.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/mode-css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/mode-javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/theme-github.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/theme-tomorrow_night.min.js"></script>
