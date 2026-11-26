<aside id="settings-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0">
    <div class="h-full px-3 py-4 bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700 flex flex-col">

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto pb-4">
            <!-- App Header -->
            <div class="flex items-center p-3 mb-4 bg-gray-50 rounded-lg dark:bg-gray-700">
                @if($app->androidWordPressConfig?->app_icon)
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg overflow-hidden">
                        <img src="{{ Storage::url($app->androidWordPressConfig->app_icon) }}" alt="{{ $app->name }}" class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18.5A2.493 2.493 0 0 1 7.51 20H7.5a2.468 2.468 0 0 1-2.4-3.154 2.98 2.98 0 0 1-.85-5.274 2.468 2.468 0 0 1 .92-3.182 2.477 2.477 0 0 1 1.876-3.344 2.5 2.5 0 0 1 3.41-1.856A2.5 2.5 0 0 1 12 5.5m0 13v-13m0 13a2.493 2.493 0 0 0 4.49 1.5h.01a2.468 2.468 0 0 0 2.403-3.154 2.98 2.98 0 0 0 .847-5.274 2.468 2.468 0 0 0-.921-3.182 2.477 2.477 0 0 0-1.875-3.344A2.5 2.5 0 0 0 14.5 3 2.5 2.5 0 0 0 12 5.5m-8 5a2.5 2.5 0 0 1 3.48-2.3m-.28 8.551a3 3 0 0 1-2.953-5.185M20 10.5a2.5 2.5 0 0 0-3.481-2.3m.28 8.551a3 3 0 0 0 2.954-5.185"/>
                        </svg>
                    </div>
                @endif
                <div class="ml-3 min-w-0 flex-1">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $app->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Str::after($app->androidWordPressConfig->site_url ?? '', '://') }}</p>
                </div>
            </div>

            <!-- App Settings Section -->
            <div class="mb-4">
                <p class="mb-2 px-3 text-xs font-semibold text-gray-400 uppercase dark:text-gray-500">App settings</p>
                <ul class="space-y-1">
                    <!-- General -->
                    <li>
                        <button wire:click="switchSection('general')" type="button"
                                class="flex items-center p-2 w-full rounded-lg group {{ $activeSection === 'general' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                            </svg>
                            <span class="ms-3 text-sm font-medium">General</span>
                        </button>
                    </li>

                    <!-- Design -->
                    <li>
                        <button wire:click="switchSection('design')" type="button"
                                class="flex items-center p-2 w-full rounded-lg group {{ $activeSection === 'design' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 {{ $activeSection !== 'design' ? 'text-gray-500 dark:text-gray-400' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ms-3 text-sm font-medium">Design</span>
                        </button>
                    </li>

                    <!-- Content (WordPress-specific) -->
                    <li>
                        <button wire:click="switchSection('content')" type="button"
                                class="flex items-center p-2 w-full rounded-lg group {{ $activeSection === 'content' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 {{ $activeSection !== 'content' ? 'text-gray-500 dark:text-gray-400' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ms-3 text-sm font-medium">Content</span>
                        </button>
                    </li>

                    <!-- Features (WordPress-specific) -->
                    <li>
                        <button wire:click="switchSection('features')" type="button"
                                class="flex items-center p-2 w-full rounded-lg group {{ $activeSection === 'features' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 {{ $activeSection !== 'features' ? 'text-gray-500 dark:text-gray-400' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ms-3 text-sm font-medium">Features</span>
                        </button>
                    </li>

                    <!-- Icon -->
                    <li>
                        <button wire:click="switchSection('icon')" type="button"
                                class="flex items-center p-2 w-full rounded-lg group {{ $activeSection === 'icon' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 {{ $activeSection !== 'icon' ? 'text-gray-500 dark:text-gray-400' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm3 2h6v4H7V5zm8 8v2h1v-2h-1zm-2-2H7v4h6v-4zm2 0h1V9h-1v2zm1-4V5h-1v2h1zM5 5v2H4V5h1zm0 4H4v2h1V9zm-1 4h1v2H4v-2z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ms-3 text-sm font-medium">Icon</span>
                        </button>
                    </li>

                    <!-- Splashscreen -->
                    <li>
                        <button wire:click="switchSection('splashscreen')" type="button"
                                class="flex items-center p-2 w-full rounded-lg group {{ $activeSection === 'splashscreen' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 {{ $activeSection !== 'splashscreen' ? 'text-gray-500 dark:text-gray-400' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ms-3 text-sm font-medium">Splashscreen</span>
                        </button>
                    </li>

                    <!-- Navigation (hidden when layout_template is list) -->
                    @if($this->shouldShowNavigation())
                        <li>
                            <button wire:click="switchSection('navigation')" type="button"
                                    class="flex items-center p-2 w-full rounded-lg group {{ $activeSection === 'navigation' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                <svg class="w-5 h-5 {{ $activeSection !== 'navigation' ? 'text-gray-500 dark:text-gray-400' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                                <span class="ms-3 text-sm font-medium">Navigation</span>
                            </button>
                        </li>
                    @endif

                    <!-- Drawer (only show when layout is app_bar_drawer) -->
                    @if($app->androidWordPressConfig->layout_template === 'app_bar_drawer')
                        <li>
                            <button wire:click="switchSection('drawer')" type="button"
                                    class="flex items-center p-2 w-full rounded-lg group {{ $activeSection === 'drawer' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                <svg class="w-5 h-5 {{ $activeSection !== 'drawer' ? 'text-gray-500 dark:text-gray-400' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span class="ms-3 text-sm font-medium">Drawer</span>
                            </button>
                        </li>
                    @endif

                    <!-- Hosted Pages -->
                    <li>
                        <button wire:click="switchSection('hosted-pages')" type="button"
                                class="flex items-center p-2 w-full rounded-lg group {{ $activeSection === 'hosted-pages' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 {{ $activeSection !== 'hosted-pages' ? 'text-gray-500 dark:text-gray-400' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ms-3 text-sm font-medium">Hosted Pages</span>
                        </button>
                    </li>

                    <!-- Notifications -->
                    <li>
                        <button wire:click="switchSection('notifications')" type="button"
                                class="flex items-center p-2 w-full rounded-lg group {{ $activeSection === 'notifications' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 {{ $activeSection !== 'notifications' ? 'text-gray-500 dark:text-gray-400' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="ms-3 text-sm font-medium">Notifications</span>
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Deployment Section -->
            <div class="mb-4">
                <p class="mb-2 px-3 text-xs font-semibold text-gray-400 uppercase dark:text-gray-500">Deployment</p>
                <ul class="space-y-1">
                    <!-- Builds -->
                    <li>
                        <button wire:click="switchSection('builds')" type="button"
                                class="flex items-center p-2 w-full rounded-lg group {{ $activeSection === 'builds' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 {{ $activeSection !== 'builds' ? 'text-gray-500 dark:text-gray-400' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                            <span class="ms-3 text-sm font-medium">Builds</span>
                        </button>
                    </li>

                    <!-- Signing -->
                    <li>
                        <button wire:click="switchSection('signing')" type="button"
                                class="flex items-center p-2 w-full rounded-lg group {{ $activeSection === 'signing' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 {{ $activeSection !== 'signing' ? 'text-gray-500 dark:text-gray-400' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ms-3 text-sm font-medium">Signing</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Actions (Always at bottom) -->
        <div class="pt-4 flex items-center justify-between">
            <!-- Dark Mode Toggle -->
            <div wire:ignore>
                <button id="theme-toggle-settings" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-hidden focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                    <svg id="theme-toggle-dark-icon-settings" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                    </svg>
                    <svg id="theme-toggle-light-icon-settings" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/>
                    </svg>
                </button>
            </div>

            <!-- Back Button -->
            <a href="{{ route('user.apps') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-hidden focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5 transition-colors" title="Back to Apps">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
        </div>
    </div>
</aside>
