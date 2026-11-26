<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $app->name }} &middot; Settings &middot; {{ config('app.name', 'Appy') }}</title>

    <!-- Dark Mode Script (must be before body renders) -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <!-- Preloader -->
    <x-preloader />

    <!-- Mobile Sidebar Toggle -->
    <button data-drawer-target="settings-sidebar" data-drawer-toggle="settings-sidebar" aria-controls="settings-sidebar" type="button"
            class="fixed top-5 left-3 z-30 inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-hidden focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
        <span class="sr-only">Open sidebar</span>
        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
        </svg>
    </button>

    <div class="flex min-h-screen bg-gray-50 dark:bg-gray-900" data-app-editor>
        <!-- Left Sidebar: Settings Navigation -->
        <livewire:plugins.android-webview.app-sidebar :app="$app" />

        <!-- Center: Main Content Area -->
        <main class="flex-1 p-4 pt-16 sm:pt-4 sm:ml-64 xl:mr-80">
            <div class="max-w-3xl mx-auto"
                 x-data="{
                     activeSection: window.location.hash ? window.location.hash.substring(1) : 'general',
                     init() {
                         // Watch for hash changes
                         window.addEventListener('hashchange', () => {
                             if (window.location.hash) {
                                 this.activeSection = window.location.hash.substring(1);
                             }
                         });
                     }
                 }"
                 x-on:section-changed.window="activeSection = $event.detail[0]; window.location.hash = activeSection">

                <!-- General Section -->
                <div x-show="activeSection === 'general'" x-cloak>
                    <div class="mb-6 ml-12 sm:ml-0">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">General</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Basic information and configuration settings</p>
                    </div>
                    <livewire:plugins.android-webview.app-general-settings :app="$app" />
                </div>

                <!-- Design Section -->
                <div x-show="activeSection === 'design'" x-cloak>
                    <div class="mb-6 ml-12 sm:ml-0">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Design</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Customize layout, colors, and theme settings</p>
                    </div>
                    <livewire:plugins.android-webview.app-design-settings :app="$app" />
                </div>

                <!-- Icon Section -->
                <div x-show="activeSection === 'icon'" x-cloak>
                    <div class="mb-6 ml-12 sm:ml-0">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Icon</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Upload app icon for Android</p>
                    </div>
                    <livewire:plugins.android-webview.app-icon-settings :app="$app" />
                </div>

                <!-- Splashscreen Section -->
                <div x-show="activeSection === 'splashscreen'" x-cloak>
                    <div class="mb-6 ml-12 sm:ml-0">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Splashscreen</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure startup screen appearance and animation</p>
                    </div>
                    <livewire:plugins.android-webview.app-splashscreen-settings :app="$app" />
                </div>

                <!-- Navigation Section -->
                <div x-show="activeSection === 'navigation'" x-cloak>
                    <div class="mb-6 ml-12 sm:ml-0">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Navigation</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage navigation items and app bar buttons</p>
                    </div>
                    <livewire:plugins.android-webview.app-navigation-settings :app="$app" />
                </div>

                <!-- Drawer Section -->
                <div x-show="activeSection === 'drawer'" x-cloak>
                    <div class="mb-6 ml-12 sm:ml-0">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Drawer Settings</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Customize drawer menu appearance and branding</p>
                    </div>
                    <livewire:plugins.android-webview.app-drawer-settings :app="$app" />
                </div>

                <!-- WebView Section ⭐ NEW WEBVIEW-SPECIFIC TAB -->
                <div x-show="activeSection === 'webview'" x-cloak>
                    <div class="mb-6 ml-12 sm:ml-0">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">WebView Settings</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure WebView behavior and features</p>
                    </div>
                    <livewire:plugins.android-webview.app-web-view-settings :app="$app" />
                </div>

                <!-- Permissions Section -->
                <div x-show="activeSection === 'permissions'" x-cloak>
                    <div class="mb-6 ml-12 sm:ml-0">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Permissions</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure device permissions your app will request</p>
                    </div>
                    <livewire:plugins.android-webview.app-permissions-settings :app="$app" />
                </div>

                <!-- Custom Code Section -->
                <div x-show="activeSection === 'custom-code'" x-cloak>
                    <div class="mb-6 ml-12 sm:ml-0">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Custom Code</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Inject custom CSS and JavaScript into your app</p>
                    </div>
                    <livewire:plugins.android-webview.app-custom-code-settings :app="$app" />
                </div>

                <!-- Hosted Pages Section -->
                @php
                    $hostedPagesCount = $app->hostedPages()->count();
                    $maxHostedPages = auth()->user()->plan?->getMaxHostedPagesPerApp();
                @endphp
                <div x-show="activeSection === 'hosted-pages'" x-cloak>
                    <div class="flex items-center justify-between gap-3 mb-6 ml-12 sm:ml-0">
                        <div class="min-w-0 flex-1">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Hosted Pages</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                <span class="hidden sm:inline">Create custom HTML pages hosted by us for your app • </span>Pages: <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $hostedPagesCount }}@if($maxHostedPages !== null)/{{ $maxHostedPages }}@endif</span>
                            </p>
                        </div>
                        <button @click="$dispatch('add-hosted-page')"
                                type="button"
                                class="flex-shrink-0 p-2.5 sm:px-5 sm:py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-hidden focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 sm:inline sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="hidden sm:inline">Add Page</span>
                        </button>
                    </div>
                    <livewire:plugins.android-webview.hosted-pages-settings :app="$app" />
                </div>

                <!-- Notifications Section -->
                <div x-show="activeSection === 'notifications'" x-cloak>
                    <div class="mb-6 ml-12 sm:ml-0">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Notifications</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure Firebase Cloud Messaging for notifications</p>
                    </div>
                    <livewire:plugins.android-webview.app-notifications-settings :app="$app" />
                </div>

                <!-- Builds Section -->
                <div x-show="activeSection === 'builds'" x-cloak>
                    <div class="flex items-center justify-between gap-3 mb-6 ml-12 sm:ml-0">
                        <div class="min-w-0 flex-1">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Builds</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                <span class="hidden sm:inline">Generate Android APK and AAB files for deployment • </span>Credits: <span class="font-semibold text-blue-600 dark:text-blue-400">{{ auth()->user()->remaining_credits }}</span>
                            </p>
                        </div>
                        <button @click="$dispatch('openBuildModal')"
                                type="button"
                                class="flex-shrink-0 p-2.5 sm:px-5 sm:py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-hidden focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 sm:inline sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="hidden sm:inline">New Build</span>
                        </button>
                    </div>
                    <livewire:dynamic-component :is="$platform->getBuildTableComponent()" :app="$app" />
                    <livewire:dynamic-component :is="$platform->getBuildModalComponent()" :app="$app" />
                    <livewire:dynamic-component :is="$platform->getDeleteBuildModalComponent()" />
                    <livewire:dynamic-component :is="$platform->getBuildLogsModalComponent()" />
                    <livewire:dynamic-component :is="$platform->getBuildDownloadModalComponent()" />
                </div>

                <!-- Signing Section -->
                <div x-show="activeSection === 'signing'" x-cloak>
                    <div class="flex items-center justify-between gap-3 mb-6 ml-12 sm:ml-0">
                        <div class="min-w-0 flex-1">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Signing</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage signing keys for app distribution</p>
                        </div>
                    </div>
                    <livewire:plugins.android-webview.app-signing-keys-settings :app="$app" />
                    <livewire:plugins.android-webview.signing-key-upload-modal wire:key="signing-key-upload-modal" />
                </div>

            </div>
        </main>

        <!-- Right: Mobile Preview Panel (Optional - some platforms don't have preview) -->
        @if($platform->getPreviewPanelComponent())
            <livewire:dynamic-component :is="$platform->getPreviewPanelComponent()" :app="$app" />
        @endif
    </div>

    <!-- Toast Notifications -->
    <livewire:toast-notification />

    <!-- AI Chat Widget -->
    @if(\App\Models\SystemSetting::get('ai_chat_enabled', false) && auth()->user()->canUseAiChat())
        <livewire:user.ai-chat-widget :app="$app" />
    @endif

    <!-- Delete Icon Modal -->
    <livewire:plugins.android-webview.delete-icon-modal :app="$app" />

    <!-- Delete Keystore Modal -->
    <livewire:plugins.android-webview.delete-keystore-modal />

    <!-- Edit Keystore Modal -->
    <livewire:plugins.android-webview.edit-keystore-modal />

    <!-- Delete Splashscreen Modals -->
    <livewire:plugins.android-webview.delete-splashscreen-image-modal :app="$app" />
    <livewire:plugins.android-webview.delete-splashscreen-logo-modal :app="$app" />

    <!-- Delete Drawer Modals -->
    <livewire:plugins.android-webview.delete-drawer-background-modal :app="$app" />
    <livewire:plugins.android-webview.delete-drawer-logo-modal :app="$app" />
    <livewire:plugins.android-webview.delete-drawer-logo-light-modal :app="$app" />
    <livewire:plugins.android-webview.delete-drawer-logo-dark-modal :app="$app" />

    <!-- Hosted Pages Modals -->
    <livewire:plugins.android-webview.hosted-page-editor-modal :app="$app" />
    <livewire:plugins.android-webview.delete-hosted-page-modal />

    <!-- Additional Scripts from Components -->
    @stack('scripts')

    <script>
        // Initialize active section from URL hash on page load
        function initializeActiveSection() {
            if (window.location.hash) {
                const section = window.location.hash.substring(1);
                // Dispatch event to sync Alpine.js and Livewire components
                window.dispatchEvent(new CustomEvent('section-changed', { detail: [section] }));
            }
        }

        // Run on both initial page load and Livewire navigation
        document.addEventListener('DOMContentLoaded', initializeActiveSection);
        document.addEventListener('livewire:navigated', initializeActiveSection);

        // Manage inert attribute for drawer accessibility (mobile only)
        document.addEventListener('DOMContentLoaded', function() {
            const settingsSidebar = document.getElementById('settings-sidebar');
            if (settingsSidebar) {
                // Function to check if we're on mobile (less than 640px - Tailwind's 'sm' breakpoint)
                const isMobile = () => window.innerWidth < 640;

                // Set initial inert state: only on mobile when hidden
                if (isMobile()) {
                    settingsSidebar.inert = true;
                } else {
                    settingsSidebar.inert = false; // Always interactive on desktop
                }

                // Listen for drawer visibility changes (only matters on mobile)
                settingsSidebar.addEventListener('hidden.drawer', function() {
                    if (isMobile()) {
                        settingsSidebar.inert = true;
                    }
                });

                settingsSidebar.addEventListener('shown.drawer', function() {
                    settingsSidebar.inert = false;
                });

                // Handle window resize
                window.addEventListener('resize', function() {
                    if (!isMobile()) {
                        // On desktop, sidebar is always visible, so never inert
                        settingsSidebar.inert = false;
                    }
                });

                // Close drawer when clicking navigation buttons on mobile
                const navButtons = settingsSidebar.querySelectorAll('button[wire\\:click^="switchSection"]');
                navButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        if (isMobile()) {
                            // Hide the drawer
                            settingsSidebar.classList.add('-translate-x-full');
                            settingsSidebar.setAttribute('aria-hidden', 'true');

                            // Remove backdrop
                            const backdrop = document.querySelector('[drawer-backdrop]');
                            if (backdrop) {
                                backdrop.remove();
                            }

                            // Trigger hidden event
                            settingsSidebar.dispatchEvent(new Event('hidden.drawer'));
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
