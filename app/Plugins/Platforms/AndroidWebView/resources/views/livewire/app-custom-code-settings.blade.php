<div>
<!-- Upgrade Banner for users without custom code access -->
@if(!$canEdit)
    <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 dark:bg-yellow-900/20 dark:border-yellow-600">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400 dark:text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">
                    Custom Code Access Required
                </h3>
                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-400">
                    <p>Your current plan does not include custom CSS/JS injection. You can view your existing code but cannot edit or save changes.</p>
                    <p class="mt-2">
                        <a href="{{ route('user.billing') }}" wire:navigate class="font-medium underline hover:text-yellow-600 dark:hover:text-yellow-300">
                            Upgrade your plan
                        </a> to enable this feature.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif

<form wire:submit.prevent="save">

    <!-- Custom CSS Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Custom CSS</h3>
                <p class="settings-card-description">Inject custom styles into your app's webview</p>
            </div>
        </div>

        <div>
            <label for="custom_css" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                CSS Code
            </label>
            <div wire:ignore>
                <div id="css-editor" style="height: 400px; width: 100%; border: 1px solid #d1d5db; border-radius: 0.5rem; overflow: hidden;" class="dark:border-gray-600"></div>
            </div>
            @error('custom_css')
                <small class="helper-text text-red-600 dark:text-red-400">{{ $message }}</small>
            @else
                <small class="helper-text">Maximum 100KB. CSS will be injected into the webview.</small>
            @enderror
        </div>
    </div>

    <!-- Custom JavaScript Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Custom JavaScript</h3>
                <p class="settings-card-description">Inject custom scripts into your app's webview</p>
            </div>
        </div>

        <div>
            <label for="custom_js" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                JavaScript Code
            </label>
            <div wire:ignore>
                <div id="js-editor" style="height: 400px; width: 100%; border: 1px solid #d1d5db; border-radius: 0.5rem; overflow: hidden;" class="dark:border-gray-600"></div>
            </div>
            @error('custom_js')
                <small class="helper-text text-red-600 dark:text-red-400">{{ $message }}</small>
            @else
                <small class="helper-text">Maximum 100KB. JavaScript will be executed in the webview.</small>
            @enderror
        </div>
    </div>

    <!-- Save Button -->
    <div class="flex items-center justify-between pt-4">
        @if($canEdit)
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-hidden focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-3 text-center dark:bg-blue-600 dark:hover:bg-blue-700 transition-all">
                {{ __('Save') }}
            </button>
        @else
            <button type="button" disabled class="text-white bg-gray-400 cursor-not-allowed font-medium rounded-lg text-sm px-6 py-3 text-center dark:bg-gray-600 flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                {{ __('Save (Locked)') }}
            </button>
        @endif
    </div>

</form>

<!-- Load Ace Editor Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/ace.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/ext-language_tools.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/mode-css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/mode-javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/theme-github.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.2/theme-tomorrow_night.min.js"></script>

<div wire:ignore>
    <script>
    // Wait for parent element to become visible, then initialize editors
    function waitForVisibilityAndInit() {
        const parentSection = document.querySelector('[x-show="activeSection === \'custom-code\'"]');

        if (!parentSection) {
            // If parent doesn't exist yet, wait a bit and retry
            setTimeout(waitForVisibilityAndInit, 100);
            return;
        }

        // Check if section is visible or listen for when it becomes visible
        const initEditors = () => {
            const cssEditorElement = document.getElementById('css-editor');
            const jsEditorElement = document.getElementById('js-editor');

            // Check if editors already initialized
            if (!cssEditorElement || !jsEditorElement ||
                cssEditorElement.classList.contains('ace_editor')) {
                return;
            }

            // Check if parent is visible
            if (parentSection.style.display === 'none' ||
                window.getComputedStyle(parentSection).display === 'none') {
                return; // Not visible yet
            }

            // Initialize editors now that section is visible
            const isDarkMode = document.documentElement.classList.contains('dark');

            // Placeholders
            const cssPlaceholder = `/* Add your custom CSS here */
/* Example: */
body {
  font-family: 'Inter', sans-serif;
}

.custom-button {
  background-color: #3b82f6;
  color: white;
  padding: 10px 20px;
  border-radius: 8px;
}`;

            const jsPlaceholder = `// Add your custom JavaScript here
// Example:
document.addEventListener('DOMContentLoaded', function() {
  console.log('Custom script loaded!');

  // Your code here
});`;

            // Initialize CSS Editor
            const cssEditor = ace.edit("css-editor");
            cssEditor.setTheme(isDarkMode ? "ace/theme/tomorrow_night" : "ace/theme/github");
            cssEditor.session.setMode("ace/mode/css");

            // Get values from Livewire component
            const cssValue = @this.get('custom_css') || '';
            if (cssValue) {
                cssEditor.setValue(cssValue, -1);
            } else {
                cssEditor.setValue(cssPlaceholder, -1);
                cssEditor.session.selection.clearSelection();
            }

            cssEditor.setOptions({
                fontSize: "14px",
                showPrintMargin: false,
                enableBasicAutocompletion: true,
                enableLiveAutocompletion: false,
                tabSize: 2,
                useSoftTabs: true,
                wrap: true,
                readOnly: @json(!$canEdit)
            });

            // Initialize JavaScript Editor
            const jsEditor = ace.edit("js-editor");
            jsEditor.setTheme(isDarkMode ? "ace/theme/tomorrow_night" : "ace/theme/github");
            jsEditor.session.setMode("ace/mode/javascript");

            const jsValue = @this.get('custom_js') || '';
            if (jsValue) {
                jsEditor.setValue(jsValue, -1);
            } else {
                jsEditor.setValue(jsPlaceholder, -1);
                jsEditor.session.selection.clearSelection();
            }

            jsEditor.setOptions({
                fontSize: "14px",
                showPrintMargin: false,
                enableBasicAutocompletion: true,
                enableLiveAutocompletion: false,
                tabSize: 2,
                useSoftTabs: true,
                wrap: true,
                readOnly: @json(!$canEdit)
            });

            // Track if editors have real content
            let cssHasContent = !!cssValue;
            let jsHasContent = !!jsValue;

            // Sync with Livewire
            cssEditor.session.on('change', function() {
                const content = cssEditor.getValue();
                if (content && content !== cssPlaceholder) {
                    cssHasContent = true;
                    @this.set('custom_css', content);
                } else if (!content || content === cssPlaceholder) {
                    cssHasContent = false;
                    @this.set('custom_css', '');
                }
            });

            jsEditor.session.on('change', function() {
                const content = jsEditor.getValue();
                if (content && content !== jsPlaceholder) {
                    jsHasContent = true;
                    @this.set('custom_js', content);
                } else if (!content || content === jsPlaceholder) {
                    jsHasContent = false;
                    @this.set('custom_js', '');
                }
            });

            // Clear placeholder on focus if empty
            cssEditor.on('focus', function() {
                if (!cssHasContent && cssEditor.getValue() === cssPlaceholder) {
                    cssEditor.setValue('', -1);
                }
            });

            cssEditor.on('blur', function() {
                const content = cssEditor.getValue().trim();
                if (!content) {
                    cssHasContent = false;
                    cssEditor.setValue(cssPlaceholder, -1);
                    cssEditor.session.selection.clearSelection();
                }
            });

            jsEditor.on('focus', function() {
                if (!jsHasContent && jsEditor.getValue() === jsPlaceholder) {
                    jsEditor.setValue('', -1);
                }
            });

            jsEditor.on('blur', function() {
                const content = jsEditor.getValue().trim();
                if (!content) {
                    jsHasContent = false;
                    jsEditor.setValue(jsPlaceholder, -1);
                    jsEditor.session.selection.clearSelection();
                }
            });

            // Handle dark mode changes
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        const isDark = document.documentElement.classList.contains('dark');
                        const theme = isDark ? "ace/theme/tomorrow_night" : "ace/theme/github";
                        cssEditor.setTheme(theme);
                        jsEditor.setTheme(theme);
                    }
                });
            });

            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });
        };

        // Listen for section-changed event from Alpine.js
        window.addEventListener('section-changed', (event) => {
            if (event.detail && event.detail[0] === 'custom-code') {
                // Section just became active, wait a tiny bit for Alpine to update DOM
                setTimeout(initEditors, 50);
            }
        });

        // Also try to init immediately in case we landed directly on this tab
        setTimeout(initEditors, 100);
    }

    // Run on both page load and Livewire navigation
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', waitForVisibilityAndInit);
    } else {
        waitForVisibilityAndInit();
    }

    document.addEventListener('livewire:navigated', waitForVisibilityAndInit);
    </script>
</div>
</div>
