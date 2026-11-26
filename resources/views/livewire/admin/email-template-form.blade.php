@assets
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<style>
    .ql-toolbar.ql-snow {
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
        border-color: rgb(209 213 219);
    }
    .dark .ql-toolbar.ql-snow {
        border-color: rgb(75 85 99);
        background-color: rgb(55 65 81);
    }
    .dark .ql-toolbar .ql-stroke {
        stroke: rgb(209 213 219);
    }
    .dark .ql-toolbar .ql-fill {
        fill: rgb(209 213 219);
    }
    .dark .ql-toolbar .ql-picker {
        color: rgb(209 213 219);
    }
    .dark .ql-toolbar .ql-picker-options {
        background-color: rgb(55 65 81);
    }
    .ql-container.ql-snow {
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
        border-color: rgb(209 213 219);
    }
    .dark .ql-container.ql-snow {
        border-color: rgb(75 85 99);
    }
    .dark .ql-editor {
        color: rgb(243 244 246);
    }
    .dark .ql-editor.ql-blank::before {
        color: rgb(156 163 175);
    }
</style>
@endassets

<div>
    <!-- Modal backdrop -->
    <div x-data="{
            show: @entangle('showModal'),
            quill: null,
            initQuill() {
                this.$nextTick(() => {
                    // Check if Quill is available
                    if (typeof Quill === 'undefined') {
                        console.error('Quill is not loaded');
                        return;
                    }

                    const editorContainer = document.getElementById('quill-editor');
                    if (!editorContainer) return;

                    // Properly destroy existing Quill instance and clean up DOM
                    if (this.quill) {
                        this.quill.off('text-change');
                        this.quill = null;
                    }

                    // Remove any existing toolbar and editor elements
                    const parent = editorContainer.parentElement;
                    const existingToolbar = parent.querySelector('.ql-toolbar');
                    if (existingToolbar) {
                        existingToolbar.remove();
                    }

                    // Reset the editor container
                    editorContainer.className = 'bg-white dark:bg-gray-700 min-h-[300px]';
                    editorContainer.innerHTML = '';

                    // Initialize Quill with snow theme
                    this.quill = new Quill(editorContainer, {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                [{ 'header': [1, 2, 3, false] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'color': [] }, { 'background': [] }],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                ['link'],
                                ['clean']
                            ]
                        }
                    });

                    // Set initial content from Livewire
                    const initialContent = @this.body || '';
                    if (initialContent) {
                        this.quill.root.innerHTML = initialContent;
                    }

                    // Sync changes back to Livewire
                    this.quill.on('text-change', () => {
                        @this.set('body', this.quill.root.innerHTML);
                    });
                });
            },
            insertVariable(variable) {
                if (this.quill) {
                    // Use setTimeout to ensure Quill is ready
                    setTimeout(() => {
                        if (!this.quill) return;

                        // Insert at end of content
                        const length = this.quill.getLength();
                        const insertIndex = length > 0 ? length - 1 : 0;

                        // Insert the variable text
                        const text = '{' + variable + '}';
                        this.quill.insertText(insertIndex, text, 'user');
                    }, 10);
                }
            }
        }"
         x-show="show"
         x-init="$watch('show', value => { if (value) initQuill() })"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="$wire.closeModal()"
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
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative z-10 inline-block w-full max-w-4xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">

                <!-- Modal header -->
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Edit Email Template
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $name }}</p>
                    </div>
                    <button type="button"
                            @click="show = false; $wire.closeModal()"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal body -->
                <form wire:submit="save">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-4">
                        <!-- Main Editor Column -->
                        <div class="lg:col-span-2 space-y-4">
                            <!-- Subject -->
                            <div>
                                <label for="subject" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Subject Line <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       wire:model="subject"
                                       id="subject"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                       placeholder="Email subject...">
                                @error('subject')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Body Editor -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Email Body <span class="text-red-500">*</span>
                                </label>
                                <div wire:ignore>
                                    <div id="quill-editor" class="bg-white dark:bg-gray-700 min-h-[300px]"></div>
                                </div>
                                @error('body')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Variables Sidebar -->
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                                    Available Variables
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                                    Click to insert at cursor position
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($variables as $variable)
                                        <button type="button"
                                                @click="insertVariable('{{ $variable }}')"
                                                class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-100 rounded hover:bg-indigo-200 dark:bg-indigo-900 dark:text-indigo-300 dark:hover:bg-indigo-800 transition-colors">
                                            {{ '{' . $variable . '}' }}
                                        </button>
                                    @endforeach
                                </div>

                                @if(count($variables) === 0)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No variables available for this template.</p>
                                @endif
                            </div>

                            <!-- Tips Section -->
                            <div class="mt-4 space-y-3">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z"/>
                                    </svg>
                                    Quick Tips
                                </h4>

                                <div class="space-y-2 text-xs">
                                    <div class="flex items-start gap-2 p-2 bg-gray-100 dark:bg-gray-700/50 rounded">
                                        <span class="text-indigo-600 dark:text-indigo-400 font-mono">@{{var}}</span>
                                        <span class="text-gray-600 dark:text-gray-400">Variables are replaced with real data when sent</span>
                                    </div>

                                    <div class="flex items-start gap-2 p-2 bg-gray-100 dark:bg-gray-700/50 rounded">
                                        <svg class="w-4 h-4 text-gray-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                        <span class="text-gray-600 dark:text-gray-400">Use the toolbar to format text, add links, and create lists</span>
                                    </div>

                                    <div class="flex items-start gap-2 p-2 bg-gray-100 dark:bg-gray-700/50 rounded">
                                        <svg class="w-4 h-4 text-gray-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                        </svg>
                                        <span class="text-gray-600 dark:text-gray-400">Select text and click the link button to add clickable links</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button"
                                @click="show = false; $wire.closeModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        @if(config('app.demo'))
                            <button type="button" disabled title="Disabled in demo mode"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg dark:bg-blue-600 cursor-not-allowed opacity-50">
                                Save Changes
                            </button>
                        @else
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-hidden focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Save Changes
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
