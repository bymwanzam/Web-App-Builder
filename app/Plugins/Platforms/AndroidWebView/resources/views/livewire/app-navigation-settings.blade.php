<form wire:submit.prevent="save">

    <!-- Bottom Navigation Bar - Only show when layout is app_bar_tabs -->
    @if($app->androidWebViewConfig->layout_template === 'app_bar_tabs')
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-icon">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="settings-card-title">Bottom Navigation Bar</h3>
                    <p class="settings-card-description">Configure bottom navigation tab items (max 8 items)</p>
                </div>
            <button type="button" wire:click="addNavigationItem"
                    @php
                        $regularTabsCount = count(array_filter($navigation_items, fn($item) => !isset($item['is_home']) || !$item['is_home']));
                    @endphp
                    @if($regularTabsCount >= 8) disabled @endif
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed dark:disabled:bg-gray-600 transition-colors">
                + Add Item
            </button>
        </div>

        @if(count($navigation_items) === 0)
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400 mb-4">No navigation items configured</p>
                <button type="button" wire:click="addNavigationItem"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add your first navigation item
                </button>
            </div>
        @else
            <div class="space-y-4">
                @foreach($navigation_items as $index => $item)
                    @php
                        $isHome = isset($item['is_home']) && $item['is_home'];
                    @endphp
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border {{ $isHome ? 'border-blue-500 dark:border-blue-400' : 'border-gray-200 dark:border-gray-600' }} rounded-lg {{ $isHome ? 'ring-2 ring-blue-500/20 dark:ring-blue-400/20' : '' }}">
                        <!-- Home Tab Badge -->
                        @if($isHome)
                            <div class="mb-3 inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-medium rounded-full">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                                <span>Home Tab - Synced to Main URL</span>
                            </div>
                        @endif
                        <div class="flex items-start gap-3">
                            <!-- Reorder Buttons -->
                            <div class="flex flex-col gap-1">
                                <button type="button" wire:click="moveUp({{ $index }})"
                                        @if($index === 0) disabled @endif
                                        class="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed dark:text-gray-500 dark:hover:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                </button>
                                <button type="button" wire:click="moveDown({{ $index }})"
                                        @if($index === count($navigation_items) - 1) disabled @endif
                                        class="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed dark:text-gray-500 dark:hover:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Form Fields -->
                            <div class="flex-1 space-y-4">
                                <!-- Label -->
                                <div>
                                    <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Label</label>
                                    <input type="text" wire:model="navigation_items.{{ $index }}.label"
                                           placeholder="Home"
                                           maxlength="50"
                                           class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                    @error('navigation_items.' . $index . '.label')
                                        <small class="text-red-600 dark:text-red-400 text-xs">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Action Type (hidden for home tab) -->
                                @if(!$isHome)
                                    <div>
                                        <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Action Type</label>
                                        <div class="flex flex-wrap gap-3">
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" wire:model.live="navigation_items.{{ $index }}.action_type" value="internal"
                                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                                <span class="ml-2 text-xs text-gray-900 dark:text-gray-300">Internal Link</span>
                                            </label>
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" wire:model.live="navigation_items.{{ $index }}.action_type" value="external"
                                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                                <span class="ml-2 text-xs text-gray-900 dark:text-gray-300">External Link</span>
                                            </label>
                                            @if(auth()->user()->canUseHostedPages() && $this->getAvailableHostedPages()->count() > 0)
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="radio" wire:model.live="navigation_items.{{ $index }}.action_type" value="hosted_page"
                                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                                    <span class="ml-2 text-xs text-gray-900 dark:text-gray-300">Hosted Page</span>
                                                </label>
                                            @endif
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" wire:model.live="navigation_items.{{ $index }}.action_type" value="share"
                                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                                <span class="ml-2 text-xs text-gray-900 dark:text-gray-300">Share</span>
                                            </label>
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" wire:model.live="navigation_items.{{ $index }}.action_type" value="email"
                                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                                <span class="ml-2 text-xs text-gray-900 dark:text-gray-300">Send Email</span>
                                            </label>
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" wire:model.live="navigation_items.{{ $index }}.action_type" value="call"
                                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                                <span class="ml-2 text-xs text-gray-900 dark:text-gray-300">Make a Call</span>
                                            </label>
                                        </div>
                                    </div>
                                @endif

                                <!-- URL Field (locked for home tab, editable for others) -->
                                @if($isHome)
                                    <div>
                                        <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">URL</label>
                                        <div class="flex items-center gap-2 px-3 py-2.5 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                            </svg>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-medium text-blue-900 dark:text-blue-100 truncate">{{ $app->androidWebViewConfig->url }}</p>
                                                <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">Automatically synced from General Settings</p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    @php
                                        $actionType = $item['action_type'] ?? 'internal';
                                        $showValue = !in_array($actionType, ['share']);
                                    @endphp
                                    @if($showValue)
                                        <div>
                                            <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">
                                                @if($actionType === 'email')
                                                    Email Address
                                                @elseif($actionType === 'call')
                                                    Phone Number
                                                @elseif($actionType === 'hosted_page')
                                                    Hosted Page
                                                @else
                                                    URL
                                                @endif
                                            </label>
                                            @if($actionType === 'hosted_page')
                                                <select wire:model="navigation_items.{{ $index }}.value"
                                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                                    <option value="">Select a page</option>
                                                    @foreach($this->getAvailableHostedPages() as $page)
                                                        <option value="{{ $page->id }}">{{ $page->title }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input
                                                    type="{{ $actionType === 'email' ? 'email' : ($actionType === 'call' ? 'tel' : 'url') }}"
                                                    wire:model="navigation_items.{{ $index }}.value"
                                                    placeholder="{{ $actionType === 'email' ? 'contact@example.com' : ($actionType === 'call' ? '+1234567890' : 'https://example.com') }}"
                                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                            @endif
                                            @error('navigation_items.' . $index . '.value')
                                                <small class="text-red-600 dark:text-red-400 text-xs">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    @endif
                                @endif

                                <!-- Icon -->
                                <div x-data="iconSelector($wire, 'navigation_items', {{ $index }})">
                                    <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Icon</label>
                                    <button type="button" @click="open = true"
                                            class="flex items-center gap-3 px-4 py-2.5 w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg hover:bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:hover:bg-gray-700">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" x-html="getIconSvg(selectedIcon || '{{ $item['icon'] ?? 'home' }}')"></svg>
                                        <span class="flex-1 text-left" x-text="icons[selectedIcon || '{{ $item['icon'] ?? 'home' }}']"></span>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <!-- Icon Selector Modal -->
                                    <div x-show="open" x-cloak @click.away="open = false"
                                         class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                        <div class="flex items-center justify-center min-h-screen px-4">
                                            <div class="fixed inset-0 bg-gray-500/75 transition-opacity"></div>
                                            <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full p-6 shadow-xl">
                                                <div class="flex items-center justify-between mb-4">
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Select Icon</h3>
                                                    <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </div>

                                                <!-- Search -->
                                                <input type="text" x-model="searchTerm" placeholder="Search icons..."
                                                       class="w-full px-4 py-2 mb-4 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">

                                                <!-- Icon Grid -->
                                                <div class="grid grid-cols-5 gap-3 max-h-96 overflow-y-auto">
                                                    <template x-for="[key, name] in filteredIcons" :key="key">
                                                        <button type="button" @click="selectIcon(key)"
                                                                class="flex flex-col items-center justify-center p-4 border-2 rounded-lg transition-all hover:bg-gray-50 dark:hover:bg-gray-700"
                                                                :class="selectedIcon === key ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600'">
                                                            <svg class="w-8 h-8 mb-2 text-gray-700 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20" x-html="getIconSvg(key)"></svg>
                                                            <span class="text-xs text-gray-600 dark:text-gray-400 text-center" x-text="name"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Remove Button (disabled for home tab) -->
                            <button type="button" wire:click="removeNavigationItem({{ $index }})"
                                    @if($isHome) disabled title="Home tab cannot be deleted" @endif
                                    class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg dark:text-red-400 dark:hover:bg-red-900/20 transition-colors {{ $isHome ? 'opacity-30 cursor-not-allowed hover:bg-transparent dark:hover:bg-transparent' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach

                @php
                    $regularTabsCount = count(array_filter($navigation_items, fn($item) => !isset($item['is_home']) || !$item['is_home']));
                @endphp
                @if($regularTabsCount >= 8)
                    <p class="text-sm text-amber-600 dark:text-amber-400 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Maximum of 8 navigation items reached (excluding Home tab)
                    </p>
                @endif
            </div>
        @endif
        </div>
    @endif

    <!-- App Bar Buttons - Show when layout is not blank -->
    @if($app->androidWebViewConfig->layout_template !== 'blank')
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-icon">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11 4a1 1 0 10-2 0v4a1 1 0 102 0V7zm-3 1a1 1 0 10-2 0v3a1 1 0 102 0V8zM8 9a1 1 0 00-2 0v2a1 1 0 102 0V9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="settings-card-title">App Bar Buttons</h3>
                    <p class="settings-card-description">Add action buttons to top right of app bar (max 3 buttons)</p>
                </div>
                <button type="button" wire:click="addAppBarButton"
                        @if(count($app_bar_buttons) >= 3) disabled @endif
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed dark:disabled:bg-gray-600 transition-colors">
                    + Add Button
                </button>
            </div>

            @if(count($app_bar_buttons) === 0)
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">No app bar buttons configured</p>
                    <button type="button" wire:click="addAppBarButton"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add your first app bar button
                    </button>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($app_bar_buttons as $index => $item)
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg">
                            <div class="flex items-start gap-3">
                                <!-- Reorder Buttons -->
                                <div class="flex flex-col gap-1">
                                    <button type="button" wire:click="moveAppBarButtonUp({{ $index }})"
                                            @if($index === 0) disabled @endif
                                            class="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed dark:text-gray-500 dark:hover:text-gray-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    </button>
                                    <button type="button" wire:click="moveAppBarButtonDown({{ $index }})"
                                            @if($index === count($app_bar_buttons) - 1) disabled @endif
                                            class="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed dark:text-gray-500 dark:hover:text-gray-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Form Fields -->
                                <div class="flex-1 space-y-4">
                                    <!-- Label -->
                                    <div>
                                        <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Label</label>
                                        <input type="text" wire:model="app_bar_buttons.{{ $index }}.label"
                                               placeholder="Search"
                                               maxlength="50"
                                               class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                        @error('app_bar_buttons.' . $index . '.label')
                                            <small class="text-red-600 dark:text-red-400 text-xs">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Action Type -->
                                    <div>
                                        <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Action Type</label>
                                        <div class="flex flex-wrap gap-3">
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" wire:model.live="app_bar_buttons.{{ $index }}.action_type" value="internal"
                                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                                <span class="ml-2 text-xs text-gray-900 dark:text-gray-300">Internal Link</span>
                                            </label>
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" wire:model.live="app_bar_buttons.{{ $index }}.action_type" value="external"
                                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                                <span class="ml-2 text-xs text-gray-900 dark:text-gray-300">External Link</span>
                                            </label>
                                            @if(auth()->user()->canUseHostedPages() && $this->getAvailableHostedPages()->count() > 0)
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="radio" wire:model.live="app_bar_buttons.{{ $index }}.action_type" value="hosted_page"
                                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                                    <span class="ml-2 text-xs text-gray-900 dark:text-gray-300">Hosted Page</span>
                                                </label>
                                            @endif
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" wire:model.live="app_bar_buttons.{{ $index }}.action_type" value="share"
                                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                                <span class="ml-2 text-xs text-gray-900 dark:text-gray-300">Share</span>
                                            </label>
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" wire:model.live="app_bar_buttons.{{ $index }}.action_type" value="email"
                                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                                <span class="ml-2 text-xs text-gray-900 dark:text-gray-300">Send Email</span>
                                            </label>
                                            <label class="flex items-center cursor-pointer">
                                                <input type="radio" wire:model.live="app_bar_buttons.{{ $index }}.action_type" value="call"
                                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                                <span class="ml-2 text-xs text-gray-900 dark:text-gray-300">Make a Call</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Dynamic Value Field -->
                                    @php
                                        $actionType = $item['action_type'] ?? 'internal';
                                        $showValue = !in_array($actionType, ['share']);
                                    @endphp
                                    @if($showValue)
                                        <div>
                                            <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">
                                                @if($actionType === 'email')
                                                    Email Address
                                                @elseif($actionType === 'call')
                                                    Phone Number
                                                @elseif($actionType === 'hosted_page')
                                                    Hosted Page
                                                @else
                                                    URL
                                                @endif
                                            </label>
                                            @if($actionType === 'hosted_page')
                                                <select wire:model="app_bar_buttons.{{ $index }}.value"
                                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                                    <option value="">Select a page</option>
                                                    @foreach($this->getAvailableHostedPages() as $page)
                                                        <option value="{{ $page->id }}">{{ $page->title }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input
                                                    type="{{ $actionType === 'email' ? 'email' : ($actionType === 'call' ? 'tel' : 'url') }}"
                                                    wire:model="app_bar_buttons.{{ $index }}.value"
                                                    placeholder="{{ $actionType === 'email' ? 'contact@example.com' : ($actionType === 'call' ? '+1234567890' : 'https://example.com') }}"
                                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                            @endif
                                            @error('app_bar_buttons.' . $index . '.value')
                                                <small class="text-red-600 dark:text-red-400 text-xs">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    @endif

                                    <!-- Icon -->
                                    <div x-data="iconSelector($wire, 'app_bar_buttons', {{ $index }})">
                                        <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Icon</label>
                                        <button type="button" @click="open = true"
                                                class="flex items-center gap-3 px-4 py-2.5 w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg hover:bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:hover:bg-gray-700">
                                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" x-html="getIconSvg(selectedIcon || '{{ $item['icon'] ?? 'home' }}')"></svg>
                                            <span class="flex-1 text-left" x-text="icons[selectedIcon || '{{ $item['icon'] ?? 'home' }}']"></span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>

                                        <!-- Icon Selector Modal -->
                                        <div x-show="open" x-cloak @click.away="open = false"
                                             class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                            <div class="flex items-center justify-center min-h-screen px-4">
                                                <div class="fixed inset-0 bg-gray-500/75 transition-opacity"></div>
                                                <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full p-6 shadow-xl">
                                                    <div class="flex items-center justify-between mb-4">
                                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Select Icon</h3>
                                                        <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                        </button>
                                                    </div>

                                                    <!-- Search -->
                                                    <input type="text" x-model="searchTerm" placeholder="Search icons..."
                                                           class="w-full px-4 py-2 mb-4 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">

                                                    <!-- Icon Grid -->
                                                    <div class="grid grid-cols-5 gap-3 max-h-96 overflow-y-auto">
                                                        <template x-for="[key, name] in filteredIcons" :key="key">
                                                            <button type="button" @click="selectIcon(key)"
                                                                    class="flex flex-col items-center justify-center p-4 border-2 rounded-lg transition-all hover:bg-gray-50 dark:hover:bg-gray-700"
                                                                    :class="selectedIcon === key ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600'">
                                                                <svg class="w-8 h-8 mb-2 text-gray-700 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20" x-html="getIconSvg(key)"></svg>
                                                                <span class="text-xs text-gray-600 dark:text-gray-400 text-center" x-text="name"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Remove Button -->
                                <button type="button" wire:click="removeAppBarButton({{ $index }})"
                                        class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg dark:text-red-400 dark:hover:bg-red-900/20 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach

                    @if(count($app_bar_buttons) >= 3)
                        <p class="text-sm text-amber-600 dark:text-amber-400 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Maximum of 3 app bar buttons reached
                        </p>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <!-- Drawer Menu Items - Only show when layout is app_bar_drawer -->
    @if($app->androidWebViewConfig->layout_template === 'app_bar_drawer')
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-icon">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="settings-card-title">Drawer Menu Items</h3>
                    <p class="settings-card-description">Configure navigation drawer menu items (1 home + up to 9 additional items)</p>
                </div>
                @php
                    $regularDrawerCount = count(array_filter($drawer_items, fn($item) => !isset($item['is_home']) || !$item['is_home']));
                @endphp
                <button type="button" wire:click="addDrawerItem"
                        @if($regularDrawerCount >= 9) disabled @endif
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed dark:disabled:bg-gray-600 transition-colors">
                    + Add Item
                </button>
            </div>

            @if(count($drawer_items) === 0)
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">No drawer menu items configured</p>
                    <button type="button" wire:click="addDrawerItem"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add your first drawer menu item
                    </button>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($drawer_items as $index => $item)
                        @php
                            $isHome = isset($item['is_home']) && $item['is_home'];
                        @endphp
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border {{ $isHome ? 'border-blue-500 dark:border-blue-400' : 'border-gray-200 dark:border-gray-600' }} rounded-lg {{ $isHome ? 'ring-2 ring-blue-500/20 dark:ring-blue-400/20' : '' }}">
                            <!-- Home Item Badge -->
                            @if($isHome)
                                <div class="mb-3 inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-medium rounded-full">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                    </svg>
                                    <span>Home Item - Synced to Main URL</span>
                                </div>
                            @endif
                            <div class="flex items-start gap-3">
                                <!-- Reorder Buttons -->
                                <div class="flex flex-col gap-1">
                                    <button type="button" wire:click="moveDrawerItemUp({{ $index }})"
                                            @if($index === 0) disabled @endif
                                            class="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed dark:text-gray-500 dark:hover:text-gray-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                        </svg>
                                    </button>
                                    <button type="button" wire:click="moveDrawerItemDown({{ $index }})"
                                            @if($index === count($drawer_items) - 1) disabled @endif
                                            class="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed dark:text-gray-500 dark:hover:text-gray-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Form Fields -->
                                <div class="flex-1 space-y-4">
                                    <!-- Label -->
                                    <div>
                                        <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Label</label>
                                        <input type="text" wire:model="drawer_items.{{ $index }}.label"
                                               placeholder="{{ $isHome ? 'Home' : 'About Us' }}"
                                               maxlength="50"
                                               class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                        @error('drawer_items.' . $index . '.label')
                                            <small class="text-red-600 dark:text-red-400 text-xs">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Action Type (hidden for home item) -->
                                    @if(!$isHome)
                                        <div>
                                            <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Action Type</label>
                                            <div class="flex flex-wrap gap-3">
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="radio" wire:model.live="drawer_items.{{ $index }}.action_type" value="internal"
                                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                                    <span class="ml-2 text-xs text-gray-900 dark:text-gray-300">Internal Link</span>
                                                </label>
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="radio" wire:model.live="drawer_items.{{ $index }}.action_type" value="external"
                                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                                    <span class="ml-2 text-xs text-gray-900 dark:text-gray-300">External Link</span>
                                                </label>
                                                @if(auth()->user()->canUseHostedPages() && $this->getAvailableHostedPages()->count() > 0)
                                                    <label class="flex items-center cursor-pointer">
                                                        <input type="radio" wire:model.live="drawer_items.{{ $index }}.action_type" value="hosted_page"
                                                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                                        <span class="ml-2 text-xs text-gray-900 dark:text-gray-300">Hosted Page</span>
                                                    </label>
                                                @endif
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="radio" wire:model.live="drawer_items.{{ $index }}.action_type" value="share"
                                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                                    <span class="ml-2 text-xs text-gray-900 dark:text-gray-300">Share</span>
                                                </label>
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="radio" wire:model.live="drawer_items.{{ $index }}.action_type" value="email"
                                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                                    <span class="ml-2 text-xs text-gray-900 dark:text-gray-300">Send Email</span>
                                                </label>
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="radio" wire:model.live="drawer_items.{{ $index }}.action_type" value="call"
                                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                                    <span class="ml-2 text-xs text-gray-900 dark:text-gray-300">Make a Call</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- URL Field (locked for home item, editable for others) -->
                                    @if($isHome)
                                        <div>
                                            <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">URL</label>
                                            <div class="flex items-center gap-2 px-3 py-2.5 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                </svg>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-medium text-blue-900 dark:text-blue-100 truncate">{{ $app->androidWebViewConfig->url }}</p>
                                                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">Automatically synced from General Settings</p>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Dynamic Value Field -->
                                        @php
                                            $actionType = $item['action_type'] ?? 'internal';
                                            $showValue = !in_array($actionType, ['share']);
                                        @endphp
                                        @if($showValue)
                                            <div>
                                                <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">
                                                    @if($actionType === 'email')
                                                        Email Address
                                                    @elseif($actionType === 'call')
                                                        Phone Number
                                                    @elseif($actionType === 'hosted_page')
                                                        Hosted Page
                                                    @else
                                                        URL
                                                    @endif
                                                </label>
                                                @if($actionType === 'hosted_page')
                                                    <select wire:model="drawer_items.{{ $index }}.value"
                                                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                                        <option value="">Select a page</option>
                                                        @foreach($this->getAvailableHostedPages() as $page)
                                                            <option value="{{ $page->id }}">{{ $page->title }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input
                                                        type="{{ $actionType === 'email' ? 'email' : ($actionType === 'call' ? 'tel' : 'url') }}"
                                                        wire:model="drawer_items.{{ $index }}.value"
                                                        placeholder="{{ $actionType === 'email' ? 'contact@example.com' : ($actionType === 'call' ? '+1234567890' : 'https://example.com') }}"
                                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                                @endif
                                                @error('drawer_items.' . $index . '.value')
                                                    <small class="text-red-600 dark:text-red-400 text-xs">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        @endif
                                    @endif

                                    <!-- Icon -->
                                    <div x-data="iconSelector($wire, 'drawer_items', {{ $index }})">
                                        <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Icon</label>
                                        <button type="button" @click="open = true"
                                                class="flex items-center gap-3 px-4 py-2.5 w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg hover:bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:hover:bg-gray-700">
                                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" x-html="getIconSvg(selectedIcon || '{{ $item['icon'] ?? 'home' }}')"></svg>
                                            <span class="flex-1 text-left" x-text="icons[selectedIcon || '{{ $item['icon'] ?? 'home' }}']"></span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>

                                        <!-- Icon Selector Modal -->
                                        <div x-show="open" x-cloak @click.away="open = false"
                                             class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                            <div class="flex items-center justify-center min-h-screen px-4">
                                                <div class="fixed inset-0 bg-gray-500/75 transition-opacity"></div>
                                                <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full p-6 shadow-xl">
                                                    <div class="flex items-center justify-between mb-4">
                                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Select Icon</h3>
                                                        <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                        </button>
                                                    </div>

                                                    <!-- Search -->
                                                    <input type="text" x-model="searchTerm" placeholder="Search icons..."
                                                           class="w-full px-4 py-2 mb-4 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">

                                                    <!-- Icon Grid -->
                                                    <div class="grid grid-cols-5 gap-3 max-h-96 overflow-y-auto">
                                                        <template x-for="[key, name] in filteredIcons" :key="key">
                                                            <button type="button" @click="selectIcon(key)"
                                                                    class="flex flex-col items-center justify-center p-4 border-2 rounded-lg transition-all hover:bg-gray-50 dark:hover:bg-gray-700"
                                                                    :class="selectedIcon === key ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600'">
                                                                <svg class="w-8 h-8 mb-2 text-gray-700 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20" x-html="getIconSvg(key)"></svg>
                                                                <span class="text-xs text-gray-600 dark:text-gray-400 text-center" x-text="name"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Remove Button (disabled for home item) -->
                                <button type="button" wire:click="removeDrawerItem({{ $index }})"
                                        @if($isHome) disabled title="Home item cannot be deleted" @endif
                                        class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg dark:text-red-400 dark:hover:bg-red-900/20 transition-colors {{ $isHome ? 'opacity-30 cursor-not-allowed hover:bg-transparent dark:hover:bg-transparent' : '' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach

                    @php
                        $regularDrawerCount = count(array_filter($drawer_items, fn($item) => !isset($item['is_home']) || !$item['is_home']));
                    @endphp
                    @if($regularDrawerCount >= 9)
                        <p class="text-sm text-amber-600 dark:text-amber-400 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Maximum of 9 additional items reached (10 total including home item)
                        </p>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <!-- Behavior Settings Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Behavior Settings</h3>
                <p class="settings-card-description">Configure user interactions</p>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Pull to Refresh -->
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">Pull to Refresh</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Allow users to refresh content by pulling down</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="pull_to_refresh" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <!-- Swipe Navigation -->
            @if($app->androidWebViewConfig->layout_template === 'app_bar_drawer')
                <div class="flex items-center justify-between p-4 bg-gray-100 dark:bg-gray-700/30 rounded-lg border border-gray-300 dark:border-gray-600">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Swipe Navigation</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Disabled for drawer layouts to prevent gesture conflicts</p>
                    </div>
                    <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 rounded-full relative">
                        <div class="absolute top-[2px] start-[2px] bg-gray-400 dark:bg-gray-500 border-gray-300 dark:border-gray-600 border rounded-full h-5 w-5"></div>
                    </div>
                </div>
            @else
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">Swipe Navigation</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Allow users to navigate back by swiping from edge</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="swipe_navigation" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            @endif

            <!-- Preserve Tab State -->
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">Preserve Tab State</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Keep current page when switching between tabs/drawer items</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="preserve_tab_state" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                </label>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="flex items-center justify-between pt-4">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-hidden focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-3 text-center dark:bg-blue-600 dark:hover:bg-blue-700 transition-all">
            {{ __('Save') }}
        </button>
    </div>

</form>

@push('scripts')
<script>
// Track if we've already registered to prevent duplicate registrations
window.iconSelectorRegistered = window.iconSelectorRegistered || false;

// Register Alpine.js component - works on both initial load and Livewire navigate
function registerIconSelector() {
    if (typeof Alpine === 'undefined') {
        // Alpine not loaded yet, wait for it
        setTimeout(registerIconSelector, 50);
        return;
    }

    // Prevent duplicate registration
    if (window.iconSelectorRegistered) {
        return;
    }
    window.iconSelectorRegistered = true;

    // Register the component
    Alpine.data('iconSelector', (wire, field, index) => ({
        open: false,
        searchTerm: '',
        selectedIcon: '',
        icons: @json($availableIcons),

        init() {
            // Get current icon value
            this.selectedIcon = this.getCurrentIcon();
        },

        getCurrentIcon() {
            const parts = field.split('.');
            let value = this.$wire.get(parts[0]);
            if (value && value[index]) {
                return value[index].icon || 'home';
            }
            return 'home';
        },

        get filteredIcons() {
            if (!this.searchTerm) {
                return Object.entries(this.icons);
            }
            return Object.entries(this.icons).filter(([key, name]) =>
                name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                key.toLowerCase().includes(this.searchTerm.toLowerCase())
            );
        },

        selectIcon(iconKey) {
            this.selectedIcon = iconKey;
            // Update nested property (navigation_items.0.icon or app_bar_buttons.0.icon)
            this.$wire.set(`${field}.${index}.icon`, iconKey);
            this.open = false;
        },

        getIconSvg(iconKey) {
            const iconSvgs = {
                'home': '<path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>',
                'shopping-cart': '<path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>',
                'heart': '<path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>',
                'flame': '<path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>',
                'newspaper': '<path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"/><path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"/>',
                'server': '<path fill-rule="evenodd" d="M2 5a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm14 1a1 1 0 11-2 0 1 1 0 012 0zM2 13a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2zm14 1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"/>',
                'mobile': '<path d="M2 3a1 1 0 011-1h1.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>',
                'globe': '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v.878A2.999 2.999 0 0110 15a3 3 0 01-2.236-1H8a2 2 0 00-2-2v-1c0-.537.213-1.053.59-1.404a2 2 0 002.805-2.805A2.989 2.989 0 0110 7V5.5A5.972 5.972 0 004.332 8.027z" clip-rule="evenodd"/>',
                'chart': '<path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>',
                'info': '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>',
                'user': '<path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>',
                'settings': '<path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>',
                'mail': '<path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>'
            };
            return iconSvgs[iconKey] || iconSvgs['home'];
        }
    }));
}

// Register on both initial load and Livewire navigation
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', registerIconSelector);
} else {
    registerIconSelector();
}

document.addEventListener('livewire:navigated', registerIconSelector);
</script>
@endpush
